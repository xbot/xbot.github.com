---
title: "OmniFocus 插件 generate-work-journal v4.0 小记"
slug: "omnifocus plugin generate work journal v2.0 release notes"
date: 2022-12-18T16:32:34+08:00
categories: ["青梅煮酒"]
tags:
- omnifocus
- 效率工具
toc: false
---
OmniFocus 是个非常强大的待办列表 App ，最大的特点之一是支持开发插件。如果你跟我一样有各种稀奇古怪的需求且其它待办 App 不能满足时，可以试试它，尽管很贵，但是很值。

[generate-work-journal](https://github.com/xbot/omnifocus-plugin-generate-work-journal) 是一个生成工作日志的工具，可以根据当天完成的、满足特定条件的任务一键生成日报，以节省时间。

包含以下特性：

- 只对特定目录下的日志有效。
- 支持大型任务（包含多项任务的 project ）和单项任务。
- 多种输出格式：
    - 纯文本
    - Markdown 无序列表
    - Markdown 有序列表
- 自动追加任务状态描述：
    - 进行中
    - 已完成
    - 自定义
- 通过标签过滤不需要的记录。

今天 generate-work-journal 发布了 4.0 版本，加入了自动判断状态为“进行中”的任务的功能。对于设置为周期重复的任务，如果还存在待执行状态的记录，那么当天已完成的记录将被认为是“进行中”。可以在设置选项中指定该状态的文字描述，和“已完成”状态一样，如果文字描述为空，最终生成的日志内容将不包含状态的描述。
