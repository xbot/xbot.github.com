---
title: 小米传感器“入驻”苹果家庭App，智能生活轻松拿捏
slug: "mapping xiaomi sensors from home assistant to apple home app "
date: 2024-12-27T22:20:44+08:00
categories: 
tags:
  - 计算机
  - 智能家居
  - home-assistant
  - home-automation
toc: false
draft: false
description: 怎样把小米官方 Home Assistant 集成的传感器映射到 Apple 的家庭 App
---

## 一、为什么要进行映射？

![2024-12-25-15-49-31-mac_20241225154742](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-12-25-15-49-31-mac_20241225154742.jpeg)

小米官方 Home Assistant 集成的事件订阅机制，极大的解放了小米智能组件在 Apple 的 HomeKit 生态中的应用！以往三方集成用的是轮询机制，这就导致小米多数组件响应特别慢，体验感超差。例如起夜时，我希望进入洗手间的同时夜灯就点亮，结果等了几秒它才有反应，是不是很让人抓狂？但现在不一样了，有了事件订阅机制，在家庭 App 里调用小米组件实现自动化变得轻松多了，大大提升了使用的流畅度。

再说说小米的传感器，可能凡是电池供电的都是无状态的。就是只有被触发的时候，才会产生一个事件，而不是像有些传感器那样一直有个持续性的实时状态。所以在小米官方集成里，大多就只提供了一个 event 实体。

反观家庭 App，里面的传感器是以 sensor 和 binary sensor 的形式存在的。它们都是有状态的实体，不同之处在于，sensor 的状态可以多种多样，能反映不同的数值或情况；而 binary sensor 呢，简单直接，只有两种状态，非此即彼。

这么一看，问题就来了，要想在家庭 App 里用好小米传感器，就必须把 Home Assistant 里的那个 event 实体，转换成 sensor 或者 binary sensor，这样两边才能对上号，实现更智能的联动。

## 二、详细操作步骤

### （一）创建存放触发时间的实体

接下来，以小米夜灯的人体传感器为例，介绍实现这一转换的方法。

首先，要创建一个 input_datetime 类型的实体，用来存放最近一次人体移动事件触发时间。在 Home Assistant 里，路径如下：Settings → Devices & services → Helpers → Create helper → Date and/or time。

![2024-12-27-22-54-11-sensor_02](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-12-27-22-54-11-sensor_02.png)

### （二）创建更新实体的自动化

紧接着，创建一个 Automation，用于在传感器的 event 触发时保存当前时间到前面那个实体。同样在 Home Assistant 里，找到 Settings → Automations & scenes → Create automation 这个路径。

![2024-12-27-22-54-49-sensor_04](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-12-27-22-54-49-sensor_04.png)

图中 Action 的模板内容如下：

```yaml
target:
  entity_id: input_datetime.ye_deng_last_motion_detected
data:
  timestamp: "{{ as_timestamp(now()) }}"
action: input_datetime.set_datetime
```

这段代码的意思是：当传感器的 event 被触发时，把当前时间（也就是{{ as_timestamp(now()) }} 存到之前创建的input_datetime.ye_deng_last_motion_detected 这个实体里。

### （三）创建判断状态的 binary_sensor 实体

最后一步，创建一个 binary_sensor 类型的实体，根据当前时间和最近一次事件触发时间间隔来判断传感器的状态，它是决定传感器在家庭 App 里显示啥状态的主体。注意把“Device class”设置成正确的传感器类型，例如这里人体传感器对应的是“Motion”。

![2024-12-27-22-55-21-sensor_03](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-12-27-22-55-21-sensor_03.png)

图中计算状态的模板内容如下：

```yaml
{{ (as_timestamp(now()) - as_timestamp(states('input_datetime.ye_deng_last_motion_detected') or 0)) < 120 }}
```

这里设定两分钟之内（也就是 120 秒，这里的时间你可以根据自己的需求调整哦）如果没有新的事件被触发，就认为状态为有人体移动；超出两分钟，就认为是没有人体移动。

## 三、这种实现方式的优缺点

### （一）优点

首先，它无需手动维护状态，binary_sensor 的状态完全由模板计算。

其次，实时更新，状态由 Home Assistant 自动刷新，不需要额外写个延迟脚本。

还有就是持久性。input_datetime 这个实体可以保存触发时间，不会受到 Home Assistant 重启这种问题的影响。

### （二）缺点

binary sensor 的模板是每分钟更新一次，这就导致两分钟的时间间隔不太严格。实际间隔时间可能在两分钟到三分钟之间，稍微有点偏差。当然，如果需要严格按照一个超时时间执行，也可以通过倒计时实现，不过意义不大，这里就不讨论了。

## 四、结语

通过以上这些步骤，就可以通过 HomeBridge把小米传感器映射到家庭 App 里了。

智能家居为我们的生活带来了诸多便利与惊喜，每个人都有着独特的使用心得。也许你通过智能音箱打造了专属的晨起唤醒模式，或是利用智能灯光营造出浪漫晚餐氛围，又或是借助智能窗帘实现了懒人式的阳光管理。不管是怎样的巧妙玩法，都欢迎大家在评论区畅所欲言，分享自己的智能家居小妙招，让我们一同探索智能生活的更多可能，互相启发，把日子过得更加舒适、有趣。