---
title: "用 WireGuard + Tailscale 搭建异构双活隧道"
slug: "heterogeneous-dual-active-tunnel-with-wireguard-and-tailscale"
date: 2026-02-27T10:00:00+08:00
draft: false
tags:
  - 计算机
  - WireGuard
  - Tailscale
  - homelab
  - 网络
---

家里有两台物理主机，每台上部署一个虚拟机，同时运行 WireGuard 和 Tailscale，组成异构双活隧道架构。两套隧道互为备份，任何一个挂了都不影响远程访问内网。

为什么选这两个组合：WireGuard 开源免费，性能好，但需要公网 IP 地址；Tailscale 是商业软件，不需要公网 IP，穿透能力强，免费额度足够个人使用。两者互补，公网 IP 在的时候走 WireGuard，没有的时候 Tailscale 兜底。再加上两台主机各跑一套，单点故障基本不存在了。

## WireGuard

WireGuard 用的是 [wg-easy](https://github.com/wg-easy/wg-easy)，带 Web UI，比手动改配置文件方便太多。

用 Docker Compose 部署：

```yaml
services:
  wg-easy:
    image: ghcr.io/wg-easy/wg-easy:15
    container_name: wg-easy
    volumes:
      - /root/appdata/wg-easy:/etc/wireguard
      - /lib/modules:/lib/modules:ro
    network_mode: host
    restart: unless-stopped
    environment:
      - HOST_HOSTNAME=${HOSTNAME}
      - TZ=Asia/Shanghai
    cap_add:
      - NET_ADMIN
      - SYS_MODULE
```

几个注意点：

**端口映射问题。** 安装引导界面上设置的端口，会同时作为服务端的监听端口和客户端的连接端口。如果在路由器上做端口映射时外部端口和内部端口不一致，需要到后台修改客户端的连接端口，否则客户端会连不上。

**多实例网段冲突。** 两台主机上同时部署 WireGuard 时，要使用不同的网段，比如一个用 `10.8.0.0/24`，另一个用 `10.9.0.0/24`。修改位置比较隐蔽：管理面板 → 接口配置 → 修改 CIDR。

**分流访问内网。** wg-easy 默认的 Allowed IPs 是 `0.0.0.0/0`，所有流量都走隧道。如果只想让内网流量走隧道、其余走本地网络，需要把"允许的 IP"改为内网网段（如 `192.168.1.0/24, 10.8.0.0/24`），同时把"DNS"指向内网的 DNS 服务器，这样才能解析内网主机名。

## Tailscale

Tailscale 的部署更简单，装好客户端，登录账号，开启子网路由就行：

```bash
# 安装
curl -fsSL https://tailscale.com/install.sh | sh

# 启动并开启子网路由
tailscale up --advertise-routes=192.168.1.0/24 --accept-routes
```

几个注意点：

**子网路由需要后台审批。** 节点运行并设置了 `--advertise-routes` 后，还需要到 Tailscale 管理后台找到对应节点，点击"Approve"允许子网路由。不做这一步的话，其他设备是访问不了内网的。

**禁用密钥过期。** 子网路由器节点的密钥默认有过期时间限制，过期后远程设备就无法访问内网了。子网路由器是基础设施节点，不太可能每次都及时去手动重新认证，尤其是两个节点同时过期的话会直接断连。建议在管理后台找到节点，禁用密钥过期（Disable key expiry）。

**配置 DNS。** 在管理后台的 DNS 选项卡中，把内网 DNS 服务器设置为 Global Nameservers，并开启 Override local DNS 选项。这样客户端才能解析内网主机名，而不是只能靠 IP 地址访问。
