---
title: "聊聊 Kindle HID Passthrough v3.14.0"
slug: "kindle-hid-passthrough-v3-14-0"
date: 2026-08-21T18:03:43+08:00
draft: false
tags:
  - 青梅煮酒
  - Kindle
  - KOReader
toc: false
---

前两天 [`kindle-hid-passthrough`](https://github.com/zampierilucas/kindle-hid-passthrough) 发布了 v3.14.0。这个版本解决了之前几个一直让人头疼的问题，值得单独说说。不过使用中发现了几个问题，排查和与作者沟通，耽搁了两天。

## 集成了 Kindle Button Mapper

[`Kindle Button Mapper`](https://github.com/zampierilucas/kindle-button-mapper-rs) 是个按键映射工具，之前需要自己手动安装。新版本会内置并自动安装它。

安装之后，可以直接在 KOReader 里给按键、方向键和扳机键分配动作，而且这些映射在 Kindle 的原生系统里同样生效。

## 软键盘弹不出来

有些翻页器会声明一大堆按键，KOReader 会把它们当成键盘。这样一来，KOReader 自带的软键盘就弹不出来，没法输入文字。

现在可以把这类设备交给 Kindle Button Mapper，并设置成 Exclusive 模式。这个模式下，只有已经映射过的按键才会触发动作，其余的输入会被直接丢弃，不会再干扰内置键盘。

## 翻页器用不了

有些翻页器会声明一个 Power 键。旧版由 KOReader 负责按键映射，为了防止误触系统电源键，这类设备会被直接忽略，结果整个翻页器都用不了。

新版绕过了 KOReader，改由 Kindle Button Mapper 来处理设备和按键的映射，这类翻页器也能正常用了。

## 还没完全解决的问题

- Kindle Oasis 2：原生系统阅读器旋转屏幕后，翻页方向会不对。把 Kindle Button Mapper 更新到 1.4.5 以上就能解决，最新的 Kindle HID Passthrough v3.14.1 已经内置了 Kindle Button Mapper 1.4.6。
- Kindle 2024（基础款）：原生系统阅读器里还是没法翻页，已经提交 issue，等上游修复。
