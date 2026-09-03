---
title: "给越狱后的 Kindle 做一套远程备份"
slug: "kindle-remote-backup"
date: 2026-09-03T11:46:38+08:00
draft: false
tags:
  - 青梅煮酒
  - Kindle
toc: false
diagram: true
---

前段时间，KOReader 的一次崩溃把 Kindle 拖进了一连串故障，最后不得不恢复出厂设置。机器虽然救回来了，但越狱、安装 KOReader、KUAL 和各种插件，再把配置一点点恢复到原来的样子，实在谈不上愉快。

为了避免下次再从头折腾一遍，我实现了一套备份工具，把 Kindle 的用户分区完整备份到 NAS。首先在 NAS 上部署一个名为 `rsync-server` 的 Docker 容器，用来接收 Kindle 传来的备份。需要备份时，只要在 Kindle 的 KUAL 菜单中执行备份，设备就会通过 rsync 和 SSH 将 `/mnt/us/` 发送给这个容器，并写入 NAS 上的镜像目录。随后，Kopia 会对镜像创建快照，保留不同时间的历史版本。

```mermaid
flowchart LR
    A["Kindle<br/>KUAL 触发"] -->|rsync + SSH| B["NAS<br/>rsync-server 容器"]
    B --> C["Kindle 镜像目录"]
    C --> D["Kopia<br/>历史快照"]
```

整个过程不需要在电脑上挂载 Kindle，也不受新款 Kindle 改用 MTP 的影响。第一次使用时，可以在 KUAL 里选择 `Dry run`，查看将要同步和删除的文件，确认无误后再执行 `Back up now`。同步过程和最终状态都会显示在 KTerm 中。

NAS 为每台 Kindle 保存独立的镜像，接收端权限也只覆盖对应的备份目录。

安装流程已经整理进 [Kindle Remote Backup](https://github.com/xbot/kindle-remote-backup) 项目。准备好 NAS 接收端，并配置好电脑到 NAS 和 Kindle 的 SSH 登录后，在项目目录运行：

```bash
./setup.sh
```

具体部署方法和完整的手动安装说明都放在项目中，这里不再展开。

rsync 镜像始终与 Kindle 当前状态保持一致。Kindle 上删除的文件，下次同步时也会从 NAS 镜像中删除。历史版本由 Kopia 或其他快照工具保存。以 Kopia 为例，创建快照只需要执行：

```bash
docker exec kopia kopia snapshot create /source/my-kindle
```

需要恢复时，先把指定快照还原到 NAS 的临时目录：

```bash
docker exec kopia kopia snapshot restore <快照根对象ID> /restore/kindle/my-kindle-restore
```

检查内容后，再通过 SSH、USB 或 MTP，把需要的图书、KOReader 配置和插件复制回 Kindle。

这样一来，即使以后又不得不重置设备，也不需要从一片空白开始重建整套使用环境。
