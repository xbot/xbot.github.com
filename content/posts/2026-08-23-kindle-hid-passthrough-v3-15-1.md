---
title: "聊聊 Kindle HID Passthrough v3.15.1"
slug: "kindle-hid-passthrough-v3-15-1"
date: 2026-08-23T13:00:00+08:00
draft: false
tags:
  - 青梅煮酒
  - Kindle
  - KOReader
toc: false
---

作者手头只有一台 Kindle 2022（基础款），我这边有一台 Kindle Oasis 2 和一台 Kindle 2024（基础款），按下葫芦起来瓢，这两天净忙着调试 bug 了，ChatGPT 额度都快用完了。真希望有个稳定点的版本，让我踏实看看书。

## Kindle 2024（基础款）原生阅读器可以翻页了

上一版遗留的问题——Kindle 2024（基础款）在原生阅读器里没法翻页——这次终于解决了。集成的 Kindle Button Mapper 更新到了 V1.5.1，原生系统阅读器可以正常翻页了。

## 支持 KPM 安装

新版本支持了 KPM（Kindle Package Manager）包管理器。之前安装和更新都要手动拷贝文件，现在添加仓库并安装就可以了：

```bash
kpm add-repo https://raw.githubusercontent.com/zampierilucas/kindle-hid-passthrough/main/kpm/repo.json
kpm install kindle-hid-passthrough
```

之后直接用 `kpm upgrade` 就能更新到新版本，配置和已配对的设备都会保留。

## Kindle Oasis 2 上 Button Mapper 服务不自启

不过在 Kindle Oasis 2 上还有一个问题：开机后 Kindle Button Mapper 的服务不会自动启动。

原因是 Button Mapper 的自启动条件绑定在了 MediaTek 芯片平台的 WMT 无线管理服务上，而 Kindle Oasis 2 用的是 Broadcom 的蓝牙芯片，对应的是 `btfd` 服务，系统里根本没有 `wmt` 服务，自启动条件自然永远不会触发。

作者已经解决了这个 issue，预计明天上午就能更新。
