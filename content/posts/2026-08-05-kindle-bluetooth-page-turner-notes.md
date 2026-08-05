---
title: "关于 Kindle 蓝牙翻页器的一些补充"
slug: "kindle-bluetooth-page-turner-notes"
date: 2026-08-05T21:55:34+08:00
draft: false
tags:
  - 青梅煮酒
  - Kindle
  - KOReader
toc: false
---

关于配置 Kindle 蓝牙翻页器的教程已经很多了。首先要满足两个前提条件：一是 Kindle 必须是支持蓝牙的版本，二是已经越狱。

配置过程也很简单：先安装 [`zampierilucas/kindle-hid-passthrough`](https://github.com/zampierilucas/kindle-hid-passthrough)，然后配对蓝牙翻页器，再配置按键映射。详细操作可以参考其他教程，这里只补充几个容易产生疑问的地方。

## 为什么需要 HID 透传

Kindle 原生系统的蓝牙功能只支持音频设备，不支持键盘这类人机接口设备，也就是 HID（Human Interface Device）。因此，即使翻页器能够和 Kindle 建立蓝牙连接，系统本身也不能直接接收它的按键输入。

所谓透传，就是单独实现一套蓝牙设备的配对和管理机制，接收 HID 设备的输入，再转换成 Kindle 系统能够识别的输入并传递过去。这就是 `kindle-hid-passthrough` 的主要功能。

## 经典蓝牙还是 BLE

不用太在意翻页器使用的是经典蓝牙还是 BLE。对用户来说，只要发送的输入信号相同，最终得到的结果就是相同的。事实上，功耗更低的 BLE 反而更适合翻页器这种使用场景。

## 短视频翻页器为什么不好用

刷短视频用的翻页器不一定完全不能用，但确实不好用。

这类翻页器在短按时，通常会调用手机系统的辅助触控功能，模拟点击或滑动，而不是发送标准的 HID 输入信号。因此，在 Kindle 的命令行里执行下面的命令，往往看不到任何输出：

```bash
evtest /dev/input/eventN
```

这里的 `N` 是翻页器对应的设备编号，并不是固定值。

不过，有些翻页器在长按时会发送可以识别的输入。比如我手头这款，长按一秒后会发送音量加或音量减的信号。稍加配置也能用来翻页，只是每次都要长按，用起来很不舒服。

## 补充按键映射

配置按键映射时，如果按下按键后没有被识别，可以先用 `evtest` 查看翻页器发送的编码，再修改下面这个文件，把监听到的编码添加进去：

```text
koreader/plugins/hidpassthrough.koplugin/event_map_extra.lua
```

这样就可以让原本没有预设映射的按键用于翻页。
