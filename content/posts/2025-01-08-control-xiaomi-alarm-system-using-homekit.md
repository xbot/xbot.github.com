---
title: 小米安防轻松入苹果“家门”，教你一招搞定！
slug: control xiaomi alarm system using homekit
date: 2025-01-08T18:00:33+08:00
tags:
  - 青梅煮酒
  - 智能家居
  - home-automation
  - home-assistant
  - homekit
toc: false
draft: false
description: 把小米官方 Home Assistant 集成的安防映射到苹果的家庭 App
---

## 为什么这么做？

![2024-12-25-15-52-54-ImageDec252024](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20241225155254000-df8cb517762f17ae1523614c57beeb91.avif)

小米官方 Home Assistant 集成的事件订阅机制可是一大亮点，它极大地解放了小米智能组件在 Apple 的 HomeKit 生态中的应用。以往三方集成采用轮询机制，这就导致小米多数的组件响应延迟很大，体验感极差。比如之前安防系统模式变化后几秒钟仪表盘上的组件才改变状态，实在是逼死强迫症。但现在不同了，有了事件订阅机制，组件响应十分及时，我们终于可以在家庭 App 中流畅地调用这些组件实现自动化操作了。

不过，对于安防系统，小米官方 Home Assistant 集成目前只提供了一个下拉框组件。这在日常使用中可就有些不便了，当你想要快速查看安防状态或者切换模式时，还得费劲地去点开下拉框，既不美观，操作也麻烦。要是能在 Home Assistant 的仪表盘和 Apple 的家庭 App 中，显示专为安防系统设计的组件，那使用起来就顺手多了。所以，就需要把这个下拉框组件对应的 select 实体转换成 alarm_control_panel 实体，接下来就看看具体怎么做吧。

## 实操

首先，我们要创建一个 alarm_control_panel 实体，位置在：Settings → Devices & services → Helpers → Create helper → Template helper → Template an alarm control panel。

![2025-01-08-18-04-03-alarm_01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20250108180403000-25935642a4bf6b303521653107296ab5.avif)

在这里，我们需要用模板来进行配置，以下是关键的代码部分：

```yaml
{% set state_map = {
            'basic_arming': 'disarmed',
            'home_arming': 'armed_home',
            'away_arming': 'armed_away',
            'sleep_arming': 'armed_night'
          } %}
          {{ state_map.get(states('select.lumi_cn_463684587_mgl03_arming_mode_p_3_1'), 'unknown') }}
```

这段代码的含义是，我们先定义了一个名为 `state_map` 的字典，它将小米安防下拉框组件中的不同状态值（如 `basic_arming` 等）对应翻译成苹果 HomeKit 能识别的 alarm_control_panel 实体状态值（如 `disarmed` 等）。最后的 `{{ state_map.get(states('select.lumi_cn_463684587_mgl03_arming_mode_p_3_1'), 'unknown') }}` 这部分，则是获取当前小米安防组件的状态，并通过前面的 `state_map` 进行转换，如果获取状态失败，就返回 `unknown`。

在实际操作时，大家找到对应的创建模板助手位置后，把这段代码准确无误地填写进去。注意，代码中的 `select.lumi_cn_463684587_mgl03_arming_mode_p_3_1` 要根据你自己设备的实际 entity_id 来填写，如果填错了，就无法正确获取状态信息了。

创建好这个实体后，我们还需要在 Home Assistant 的仪表盘中添加这个组件，这样就能在 Home Assistant 界面直观地看到安防状态了。添加组件的操作一般是在仪表盘编辑模式下，找到添加安防控件的选项，然后选择我们刚刚创建的 alarm_control_panel 实体即可。

最后，要想让这个安防状态在苹果的家庭 App 中显示，还需要通过 HomeBridge 把这个实体输出给 HomeKit。如果你之前没有配置过 HomeBridge，需要先进行安装和配置。简单来说，就是在 Home Assistant 中安装 HomeBridge 插件，然后在 HomeBridge 的配置文件中，设置好与 Home Assistant 的连接，确保能获取到我们创建的 alarm_control_panel 实体信息，再将其桥接到 HomeKit 中。这一步可能稍微复杂些，涉及到一些插件的安装和参数配置，但只要按照官方文档或者网上的详细教程操作，也能顺利完成。

在整个操作过程中，大家要注意几个小细节。一是确保你的小米智能设备与 Home Assistant 连接正常，网络稳定，不然可能会出现状态获取不准确或者延迟的问题。二是在填写代码和配置参数时，一定要仔细核对，一个小标点的错误都可能导致功能无法实现。还有就是软件版本，Home Assistant、HomeBridge 等软件都要确保是兼容的版本，要是版本不匹配，也容易出现各种奇怪的问题。

## 结语

通过以上步骤，就成功将小米官方 Home Assistant 集成的安防映射到苹果的家庭 App 中了！以后，就可以在苹果设备上轻松控制小米安防设备，无论是出门在外开启离家模式，还是晚上睡觉前设置睡眠安防模式，都变得便捷又高效。

如果你在操作过程中遇到任何问题，欢迎随时在评论区留言，大家一起交流探讨。也期待大家分享自己的智能家居使用心得。