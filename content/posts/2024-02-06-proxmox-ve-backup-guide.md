---
title: Proxmox VE 备份指南
slug: Proxmox VE Backup Guide
date: 2024-02-06T18:48:31+08:00
categories:
  - 计算机
tags:
  - PVE
  - PBS
  - 数据备份
  - NAS
  - homelab
  - 虚拟技术
toc: false
draft: false
---

在当今数据中心管理中，数据备份是确保业务连续性和灾难恢复的关键环节。即使是 Home Lab ，也需要保证重要数据的安全。Proxmox Virtual Environment (PVE) 提供了一个强大的平台来管理虚拟化基础设施，而 Proxmox Backup Server (PBS) 则是其官方推荐的备份解决方案。本文将详细介绍如何使用 PBS 对 Proxmox VE 的虚拟机或容器（Guest）和宿主机（Host）进行备份，以及如何设置自动化备份流程。

# Guest 的备份

## 使用 PBS

Proxmox Backup Server (PBS) 是一个功能强大的备份工具，它提供了多种备份选项，包括增量备份、加密备份、自动备份以及自动修剪等功能。这些功能使得 PBS 成为与 PVE 结合紧密的理想选择。

### 优势

- **增量备份**：只备份自上次备份以来发生变化的数据，节省存储空间和时间。
- **加密备份**：确保备份数据的安全性。
- **自动备份**：设置定时任务，无需手动干预。
- **自动修剪**：自动清理旧的备份，保持存储空间的合理使用。
- **与 PVE 结合紧密**：PBS 专为 PVE 设计，备份和恢复过程无缝对接。

### 步骤

1. **安装 PBS**

   - **LXC 方式**：创建一个 Debian 容器，然后添加 PBS 的源。这种方式轻量级，但 LXC 容器只能使用 RAW 格式的镜像。
   - **ISO 方式**：通过 PBS 官方 ISO 镜像创建虚拟机。这种方式限制较少，能适应更多硬件环境。

2. **创建 PBS 备份用户**

   在 PBS 中，进入 Configuration → Access Control → User Management 创建一个新用户，例如 `dataguard`。

3. **创建 Datastore**

   - 如果使用单独的磁盘或磁盘镜像，可以在 Configuration → Administration → Storage/Disks -> Directory 中添加，并选中“Add as Datastore”。
   - 如果使用 NFS，先在 `/etc/fstab` 中挂载到一个目录，然后在 Datastore → Add Datastore 中添加。

4. **配置自动修剪规则**

   例如，保留最近 7 天的每日备份、8 周的每周备份和 6 个月的每月备份。

![2024-02-06-18-26-38-JCL4GH](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-02-06-18-26-38-JCL4GH.png)

5. **配置 Datastore**

   在新建的 Datastore 的 Permissions 选项卡中为新建的用户赋权，通常使用 `DatastoreAdmin` 角色。如果需要备份多台机器，可以在 Content 选项卡中创建对应的命名空间。

6. **配置 PVE**

   在 Datacenter → Storage 中添加 Proxmox Backup Server，然后在 Datacenter → Backup 中创建备份计划。

# Host 的备份

在 PBS 支持备份 PVE 主机之前，我们可以使用脚本通过 `proxmox-backup-client` 将重要文件备份到 PBS。

## 脚本

https://raw.githubusercontent.com/xbot/homelab/main/pve/backup.sh。

## 配置

在 `/root/.profile` 中添加以下环境变量：

```bash
export PBS_NAMESPACE="[命名空间]"
export PBS_PASSWORD="[PBS 备份用户的密码]"
export PBS_REPOSITORY="[备份用户名]@pbs@[PBS主机名或IP]:[Datastore名称]"
```

## 执行

1. 手动执行脚本以确保一切正常。
2. 使用 `cron` 定时任务自动执行脚本。

通过以上步骤，你可以确保你的 Proxmox VE 环境得到妥善的备份，无论是虚拟机还是主机。记得定期检查备份状态，确保备份过程顺利进行，并在必要时进行恢复测试。这样，即使面对数据丢失的风险，你也能迅速恢复业务。