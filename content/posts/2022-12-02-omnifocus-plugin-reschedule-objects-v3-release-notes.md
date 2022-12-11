---
title: "OmniFocus 插件 reschedule-objects v3.0 小记"
slug: "omnifocus plugin reschedule objects v3.0 release notes"
date: 2022-12-02T19:53:34+08:00
categories: ["青梅煮酒"]
tags:
- OmniFocus
- 效率工具
toc: false
---

今天 [reschedule-objects](https://github.com/xbot/omnifocus-plugin-reschedule-objects) 发布了 3.0 版本，主要包括三部分的变化。

首先是重构。随着我写的插件数的增加，有些代码可以复用，有些 Omni Automation API 比较烦琐，所以有了封装一个工具类的想法，正好 Omni 应用也支持这样，所以封装了 [libdev](https://github.com/xbot/omnifocus-plugin-libdev) 开发库。另外，之前很多地方写得很差劲，这次也一并优化了一下。

其次是用多选组件模拟单选操作，实现了用户体验的提升。Omni Automation API 对于单选操作只提供了下拉列表这一个组件，对于高频操作会显得很低效，这段时间用下来觉得很难受。文档说表单的验证方法可以增减表单里的字段，利用这一点曲线解决了这个问题，用多选组件实现了 radio group 。缺点是切换选项时表单可能产生抖动。

![2022-12-11-16-57-10-omnifocus-plugin-checklist-v3-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-12-11-16-57-10-omnifocus-plugin-checklist-v3-01.gif)

第三个变化是加入了清除排期的功能。目前还需要通过同一个入口触发，最多需要三次点击。还有优化空间，考虑未来重构成多 Action 的插件，可以通过单独的按钮触发，最少只要一次点击就可以了。