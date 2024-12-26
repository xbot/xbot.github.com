---
title: 用 n8n 优化 Homelab 通知流程：从邮件到 Telegram
slug: optimize homelab notifications with n8n from email to telegram
date: 2024-09-06T16:27:57+08:00
categories:
  - 青梅煮酒
tags:
  - homelab
  - n8n
  - telegram
toc: false
draft: false
---
最近，我对 Homelab 的通知系统进行了一次升级，将繁琐的邮件通知转变为简洁的 Telegram 消息。这个过程不仅提高了效率，还大大改善了我的使用体验。

## 背景

我的 Homelab 基础设施主要由 Proxmox Virtual Environment (PVE) 和 Proxmox Backup Server (PBS) 构成。这些系统每天都会发送各种通知，包括系统更新、虚拟机和容器备份、备份数据仓库垃圾回收等任务的执行结果。

长期以来，我一直通过邮件接收这些通知。然而，邮件作为一种信息接收方式，显得既重量级又低效。每天删除这些通知邮件成了一件烦人的琐事，我急需一种更轻量、更即时高效的通知方式。

## 为什么选择 Telegram？

我已经在使用 Telegram 接收 Unraid 的通知消息，体验一直很不错。考虑到这点，我决定将基础平台的通知也集中到 Telegram 上来。

## 初步尝试：Gotify 和 gotify2telegram

PVE 和 PBS 除了支持邮件通知，还支持 Gotify 这个消息收集服务。理论上，我可以使用 [gotify2telegram](https://github.com/anhbh310/gotify2telegram) 插件将 Gotify 的消息转发到 Telegram。

但是，这个方案存在两个问题：

1. 不支持通过 Gotify 的 Application 过滤消息，导致所有发送到 Gotify 的消息都会被无差别转发。
2. 不支持对消息内容进行处理，降低了通知的可读性。比如，PVE 在备份客户机后会发送冗长的备份过程信息，这些信息在 Telegram 中会被截断成多条消息展示，非常不便于阅读。

## 最终方案：n8n + gotify-webhook

为了解决上述问题，我找到了 [gotify-webhook](https://github.com/wuxs/gotify-webhook) 插件。我的想法是通过这个插件将消息转发给 n8n 处理，然后再发送到 Telegram。

![2024-09-06-16-33-19-mac_20240905114752](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-09-06-16-33-19-mac_20240905114752.png)

在提交了一个 PR 并被合并后，我终于实现了这个流程。现在，我的 Homelab 通知体验有了质的飞跃：

1. 消息经过 n8n 处理后更加简洁明了。例如，对于备份任务，如果成功了只会发送一条简短的成功通知，只有在失败时才会显示详细信息。
2. 通过 n8n 的规则，我可以轻松过滤和分类不同类型的通知。
3. Telegram 的即时性让我能够更快地响应任何潜在的问题。

## 总结

通过这次升级，我不仅简化了 Homelab 的日常维护工作，还提高了对系统状态的感知能力。如果你也在为繁琐的 Homelab 通知所困扰，不妨试试这个方案。