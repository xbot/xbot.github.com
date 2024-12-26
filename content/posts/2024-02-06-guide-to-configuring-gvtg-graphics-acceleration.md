---
title: GVT-g 配置指南：实现多虚拟机共享 Intel 核显硬件加速
slug: Guide to Configuring GVT-g Graphics Acceleration
date: 2024-02-06 11:25:58+08:00
tags:
- NAS
- PVE
- homelab
- 虚拟技术
- 计算机
toc: false
draft: false
---

Proxmox Virtual Environment（PVE）是一个功能强大的开源虚拟化管理平台。它允许用户在单个主机上运行多个虚拟机，每个虚拟机都可以拥有自己的操作系统。为了进一步提升虚拟机的性能，特别是对于那些需要图形处理能力的应用，我们可以通过配置 Intel® Graphics Virtualization Technology –g (GVT-g) 来实现。GVT-g 是一种硬件辅助的虚拟化技术，它允许虚拟机直接访问宿主机的图形处理单元（GPU），从而提高图形性能。本文将详细介绍如何在 PVE 中为 Intel 核显开启 GVT-g 。

本文环境：

- 操作系统：PVE 8.0
- CPU： Intel Core i5-8600t
- 核显：UHD630

### 步骤 1 ：修改 GRUB 配置

首先，我们需要确保 GRUB 引导加载器在启动时启用了必要的参数。打开`/etc/default/grub`文件，并找到`GRUB_CMDLINE_LINUX_DEFAULT`行。添加以下参数：

```shell
GRUB_CMDLINE_LINUX_DEFAULT="quiet intel_iommu=on iommu=pt i915.enable_gvt=1"
```

这将启用 Intel IOMMU（Input/Output Memory Management Unit）和 GVT-g 。保存文件后，运行以下命令来更新 GRUB 配置：

```shell
sudo update-grub
```

### 步骤 2 ：添加内核模块

接下来，我们需要确保 PVE 在启动时加载必要的内核模块。编辑`/etc/modules`文件，添加以下模块：

```shell
vfio
vfio_iommu_type1
vfio_pci
vfio_virqfd
kvmgt
```

如果你之前因为配置直通核显而在`/etc/modprobe.d/pve-blacklist.conf`中屏蔽了显卡驱动，现在需要解除屏蔽。这可以通过注释掉或删除相关行来实现。

完成修改后，运行以下命令来更新 initramfs ：

```shell
sudo update-initramfs -u -k all
```

### 步骤 3 ：重启系统

为了使更改生效，我们需要重启系统。在重启后，可以通过以下命令来检查 GVT-g 是否已经成功启用：

```shell
dmesg | grep -e DMAR -e IOMMU -e AMD-Vi
```

这将显示与 IOMMU 相关的内核消息。如果一切顺利，你应该能看到相关的启用信息。

### 步骤4：验证 GVT-g 支持

最后，我们可以检查系统是否成功启用 GVT-g 。在终端中运行以下命令：

```shell
ls /sys/bus/pci/devices/0000:00:02.0/mdev_supported_types
```

如果输出中包含多种规格的虚拟 GPU ，那么 GVT-g 已经准备好被使用了。

### 结语

通过以上步骤，你已经成功地为 Intel 核显配置了 GVT-g 。这将为你的 PVE 上更多的虚拟机提供更好的图形性能，特别是在运行需要高性能图形处理的应用时。

请注意，虚拟化技术不断发展，确保你的 PVE 内核版本支持 GVT-g ，并且硬件也满足要求。如果你的硬件或软件版本不支持 GVT-g ，可能需要考虑其他虚拟化技术或升级你的系统。