---
title: 榨干 Intel 核显潜能，让更多虚拟机用上显卡
slug: Guide to Increasing Intel Graphics Aperture Size
date: 2024-02-06T12:30:40+08:00
categories:
  - 计算机
tags:
  - PVE
  - 虚拟技术
  - homelab
  - NAS
toc: false
draft: false
---

在虚拟化环境中，尤其是使用 Proxmox VE 或其他虚拟化平台时，有时我们需要增加 Intel 核显的显存孔径（Aperture Size），以便让更多虚拟机用上显卡的硬件加速。例如，我的 i5-8600t CPU 的核显是 UHD630 ，它的显存孔径被限制为 256M ，这只能提供一个 1200p 或两个 768p 的虚拟 GPU ，显然太少了。而当我们把这个值上调到 1024M ，就能获得更多或性能更高的虚拟 GPU 。

这可以通过修改 BIOS 设置来实现，但通常主板 BIOS 不提供直接的选项来调整这个值。幸运的是，我们可以通过一些工具来间接修改这个设置。以下是详细的步骤：

### 步骤 1: 下载和主板上同版本的 BIOS

首先，你需要从主板制造商的官方网站下载与你的主板型号相匹配的 BIOS 文件。确保下载的是与你的主板上安装的 BIOS 版本相同的文件，以便后续操作不会引发兼容性问题。

### 步骤 2: 使用 UEFITool 打开 BIOS

接下来，你需要使用 [UEFITool](https://github.com/LongSoft/UEFITool)，这是一个 UEFI 固件图像查看器和编辑器。下载并安装 UEFITool，然后使用它打开你下载的 BIOS 文件。

### 步骤 3: 搜索“aperture size”

在 UEFITool 中，打开搜索对话框并选择“Text”类型，然后输入“aperture size”进行搜索。搜索结果中可能会有多个匹配项。

### 步骤 4: 导出 Body 并转换

选择一个搜索结果，导出其 Body。然后，使用 [IFRExtractor-RS](https://github.com/LongSoft/IFRExtractor-RS)，这是一个 Rust 实用程序，可以将 UEFI IFR 数据提取成人类可读的文本。运行 IFRExtractor-RS 并指定你刚刚导出的文件，它会生成一个文本文件。

### 步骤 5: 找到“VarOffset”值

在生成的文本文件中，搜索“aperture size”，找到包含内存大小选项的部分。记下“VarOffset”的值，例如：0x8A5。

### 步骤 6: 准备 U 盘并修改设置

将 U 盘格式化为 FAT32 文件系统，并在 U 盘的根目录下创建一个 `/EFI/BOOT` 目录。然后，下载 [grub-mod-setup_var](https://github.com/datasone/grub-mod-setup_var)，这是一个修改过的 GRUB，允许调整隐藏的 BIOS 设置。将 `modGRUBShell.efi` 文件复制到 `/EFI/BOOT` 目录，并重命名为 `bootx64.efi`。

### 步骤 7: 重启并执行设置

重启你的计算机，并从 U 盘引导。在 GRUB 菜单中选择使用 `bootx64.efi` 启动。在 GRUB shell 中，执行以下命令来修改显存孔径：

```shell
setup_var 0x8A5 0x7
```

这里的 `0x8A5` 是你之前记下的“VarOffset”值，`0x7` 是你想要设置的新值，代表 1GB 的显存。

### 步骤 8: 重启并验证

重启你的计算机，并在启动后使用以下命令来验证显存孔径是否已成功修改：

```shell
lspci -vs 00:02.0
```

你应该能看到显存孔径的值已经更新。

请注意，这些步骤可能需要一定的技术知识，并且在操作过程中可能会有风险。在进行任何修改之前，请确保你已经备份了重要数据，并且了解可能的后果。如果你不确定自己的操作，建议寻求专业人士的帮助。