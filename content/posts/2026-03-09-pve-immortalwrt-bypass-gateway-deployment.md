---
title: "PVE + ImmortalWrt 旁路网关部署实战"
slug: "pve-immortalwrt-bypass-gateway-deployment"
date: 2026-03-09T10:00:00+08:00
draft: false
tags:
  - 计算机
  - ImmortalWrt
  - OpenWrt
  - PVE
  - OpenClash
  - homelab
---

之前一直在用 eSir 的佛跳墙固件，每次升级都要重新刷机、重新配置，挺麻烦的。最近换成了 ImmortalWrt，官方源支持 `sysupgrade` 平滑升级，配置都能保留，省事不少。

ImmortalWrt 是 OpenWrt 的一个分支，主要是针对中国大陆用户做了一些本地化优化，比如内置了更多的驱动、默认开启了一些实用功能。部署在 PVE 上作为旁路网关使用，配合 OpenClash 做透明代理。

## 创建虚拟机

到 [firmware-selector.immortalwrt.org](https://firmware-selector.immortalwrt.org/) 搜索 `x86/64`，下载 **ext4-combined-efi** 的 `.img.gz` 文件。官方源下载慢的话可以用国内镜像。

在 PVE 上创建虚拟机时有个坑：必须指定 `--scsihw virtio-scsi-pci`，否则默认的 LSI 控制器会导致 ImmortalWrt 无法识别磁盘，系统无法正常引导。

```bash
# 创建虚拟机
qm create 211 --name openwrt-new --memory 1024 --cores 2 --bios ovmf \
  --scsihw virtio-scsi-pci \
  --efidisk0 local-lvm:1 --net0 virtio,bridge=vmbr0 --ostype l26 \
  --serial0 socket --agent enabled=1

# 解压镜像并导入磁盘
gunzip /tmp/immortalwrt-*-combined-efi.img.gz
qm importdisk 211 /tmp/immortalwrt-*-combined-efi.img local-lvm
qm set 211 --scsi0 local-lvm:vm-211-disk-1
qm set 211 --boot order=scsi0
qm start 211
```

## 网络配置

ImmortalWrt 默认 IP 是 `192.168.1.1`，和我内网的 `10.0.0.0/24` 网段不一样，无法直接 SSH。需要通过 PVE 的串口终端先改一下 IP：

```bash
# 在 PVE 主机上连接串口（Ctrl+O 退出）
qm terminal 211
```

进入串口后设置 IP、网关和 DNS：

```bash
uci set network.lan.ipaddr='10.0.0.211'
uci set network.lan.gateway='10.0.0.1'
uci set network.lan.dns='10.0.0.1'
uci commit network
/etc/init.d/network restart
```

旁路网关不需要 DHCP 服务，直接关掉。如果启用了 IPv6，还需要禁用 odhcpd 的 RA 广播：

```bash
uci set dhcp.lan.ignore=1
uci commit dhcp
/etc/init.d/dnsmasq restart

# 如果启用了 IPv6，禁用 RA/DHCPv6
uci set dhcp.lan.ra='disabled'
uci set dhcp.lan.dhcpv6='disabled'
uci commit dhcp
/etc/init.d/odhcpd restart
```

## 安装 OpenClash

ImmortalWrt 官方源不含 OpenClash，需要从 [GitHub Releases](https://github.com/vernesong/OpenClash/releases) 手动下载 ipk 安装。

如果虚拟机无法直连 GitHub，可以在能访问的主机上下载后 SCP 过去：

```bash
wget https://github.com/vernesong/OpenClash/releases/download/v0.47.055/luci-app-openclash_0.47.055_all.ipk
scp -O luci-app-openclash*.ipk root@10.0.0.211:/tmp/
```

在 ImmortalWrt 上安装：

```bash
opkg update
opkg install /tmp/luci-app-openclash*.ipk
```

ImmortalWrt 默认自带 `dnsmasq-full` 和 `luci-compat`，不需要像官方 OpenWrt 那样手动替换 dnsmasq。OpenClash 安装后内置三个面板，无需额外安装。

## 配置迁移

如果是从旧的 OpenWrt 迁移，且两边 OpenClash 版本相近，可以直接打包配置文件迁移：

```bash
# 在旧机器上打包
ssh root@<旧机器> "tar czf /tmp/openclash-backup.tar.gz /etc/openclash/ /etc/config/openclash"

# 传到新机器并解包
scp -O root@<旧机器>:/tmp/openclash-backup.tar.gz /tmp/
scp -O /tmp/openclash-backup.tar.gz root@10.0.0.211:/tmp/
ssh root@10.0.0.211 "tar xzf /tmp/openclash-backup.tar.gz -C / && /etc/init.d/openclash restart"
```

## QEMU Guest Agent

装一下 QEMU Guest Agent，这样 PVE 能正确获取虚拟机的 IP 地址：

```bash
ssh root@10.0.0.211 "opkg update && opkg install qemu-ga && \
  /etc/init.d/qemu-ga start && /etc/init.d/qemu-ga enable"
```

安装后需要在 PVE 上**关机再开机**（不是 reboot）让 Guest Agent 生效：

```bash
qm shutdown 211 --timeout 30
qm start 211
qm agent 211 ping  # 验证
```

## 客户端使用

设备端两种方式：

- **HTTP 代理**：设备代理指向 `10.0.0.211:7890`
- **网关模式**：设备的网关和 DNS 都改为 `10.0.0.211`

网关模式下所有流量都走代理，适合需要全局代理的场景。HTTP 代理模式更灵活，可以按需开启。
