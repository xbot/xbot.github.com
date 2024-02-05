---
title: Homelab 升级记：从 Unraid 到 Proxmox VE 的平滑过渡
slug: A Smooth Transition From Unraid to PVE
date: 2024-02-05T22:22:57+08:00
categories:
  - 青梅煮酒
tags:
  - Unraid
  - PVE
  - homelab
  - NAS
toc: false
draft: false
---
最近，我对我的 Homelab 进行了一次重大调整，从 Unraid 迁移到了 Proxmox VE（PVE），并用它实现了对 Unraid 的虚拟化，这一转变不仅提高了系统的稳定性、灵活性，还带来了一系列令人惊喜的改进。在这篇文章中，我将分享这次调整的动机、硬件升级以及软件配置的详细过程。

# 动机：解耦虚拟机与 Unraid

我决定将虚拟机从 Unraid 中分离出来，这样做的目的是为了减轻 Unraid 重启时对整个网络环境的影响，降低磁盘阵列卡住的风险，并加速 Unraid 的重启过程。

此外，这样的调整也使得使用 Proxmox Backup Server (PBS) 进行备份变得更加方便，减少了对 Unraid 缓存盘的占用，并优化了硬盘的休眠策略，减少了不必要的唤醒次数。

最后，减少虚拟化嵌套层数，简化了架构，提高了整体效率。

# 硬件升级

1. **机箱改造：** 为了适应新的硬件配置，我用角磨机切掉了蜗牛星际机箱 B 款的横梁，因为它挡住了 PCIe 插槽。这一小小的改动为后续的硬件安装提供了空间。

![2024-02-05-22-40-47-IMG_1058](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-02-05-22-40-47-IMG_1058.jpeg)

![2024-02-05-22-42-35-DSC00141](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-02-05-22-42-35-DSC00141.jpg)

2. **主板与扩展卡：** 主板只有一个 M.2 插槽，我选择在这里安装了 PVE。为了加装第二款 NVME 固态硬盘，我购买了 PCIe 转 M.2 扩展卡，将 Unraid 的缓存盘插到了这个扩展卡上。小火炉铠侠 RC10 在加装了扩展卡附带的散热片后降温效果显著，目测温度下降了大约 15℃，Unraid 系统再也没有出现过报警。

3. **视频采集卡：** 为了在没有额外显示器的情况下管理 Homelab ，我购买了视频采集卡，并配合 iPad 使用，作为临时显示器。

![2024-02-05-23-20-26-IMG_1079](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-02-05-23-20-26-IMG_1079.jpeg)

# 软件配置

1. **直通设备：** 在 PVE 中，我将 Unraid 系统 U 盘、Sata 控制器、外置硬盘以及 NVME 缓存盘直通（passthrough）给了 Unraid 虚拟机。这样做的好处是，仍然使用 Unraid 作为 NAS 系统管理所有存储资源，并继续使用 Unraid 优秀的应用社区，同时使 Jellyfin 可以直接使用硬件加速解码，提高了性能和响应速度。

2. **显卡优化：** 对于 i5-8600t 的集成显卡 UHD630，我开启了 GVT-g（Intel® Graphics Virtualization Technology –g）功能，并增加了显存孔径（Aperture Size）。默认的孔径只有 256MB，这限制了虚拟机的显卡性能，只能支持一个 1200p 或两个 768p 的显卡。通过将孔径增加到 1024MB，虚拟机现在可以共享更强大的显卡资源，为运行更高分辨率或更多虚拟机提供了可能。

![2024-02-05-23-04-11-WFhOJe](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-02-05-23-04-11-WFhOJe.png)

# 结语

通过这一系列调整，我的 Homelab 更加灵活、高效，同时在硬件和软件层面都得到了优化。这个过程中的每一个改进都让我更好地满足了不断变化的需求，也为未来的扩展奠定了更好的基础。