---
title: "我在用的 OmniFocus 插件"
slug: "the Omnifocus Plugins I Am Using"
date: 2021-12-25T22:05:40+08:00
categories:
- 青梅煮酒
tags:
- omnifocus
- GTD
- 效率工具
---
# 检查列表

[xbot/omnifocus-plugin-checklist](https://github.com/xbot/omnifocus-plugin-checklist)

在 OmniFocus 中指定一个 folder ，其下的所有 project 都将作为 checklist 的模板。

选中一或多个 projects 并触发这个插件的 action ，在弹出的对话框中选择要用的模板，将会在目标 projects 中创建同名 task ，模板中的 tasks 创建为该 task 的子任务。

模板的 Parallel 、 Complete with last action 、 tags 和 note 会被同步过去。

# 生成工作日志

[xbot/omnifocus-plugin-generate-work-journal](https://github.com/xbot/omnifocus-plugin-generate-work-journal)

用于每天下班前一键生成工作日志。

插件会收集名为 `Work` 的目录下当天完成的 Tasks 并根据 Task 标题和 Note 的内容追加不同的状态描述。例如，标题包含“面试”的 Task 不追加状态描述；Note 为空的 Task 追加“完成”，否则追加 Note 的内容。目录目前是写死的，没有做通用性适配。

# 打开 Jira Ticket

[xbot/omnifocus-plugin-open-jira-ticket](https://github.com/xbot/omnifocus-plugin-open-jira-ticket)

从被选中的 Project 标题中解析 Jira Ticket 序号，并根据配置的 Jira URL 打开对应的 Ticket 。如果选中的是 Task ，使用其所属的 Project 。

# 在 Flomo 中搜索当前 Project

[xbot/omnifocus-plugin-find-in-flomo](https://github.com/xbot/omnifocus-plugin-find-in-flomo)

从被选中的 Project 标题中按自定义规则解析关键词，并在 Flomo 中搜索相关的笔记。可以指定一个 Tag 辅助过滤。

这个插件的通用性做得稍好，解析关键词的正则表达式可以自定义，解析出的结果可以填充到自定义模板中作为 Flomo 的搜索词。

Flomo 目前只支持通过单个 Tag 过滤，所以配置界面只能指定一个 Tag 。

# 转换 Tasks 为指定目录下的 Projects

[xbot/omnifocus-plugin-convert-to-projects](https://github.com/xbot/omnifocus-plugin-convert-to-projects)

OmniFocus 自带的转换功能会把新 Project 追加到库的末尾，需要先跳转到 Projects Perspective 再拖动到目标目录。所以写了这个插件，转换前先指定目标目录，转换后自动跳转到新的 Projects 。

选中的目录会被记录，下次执行转换操作时默认选中上次使用的目录。

# 重置 Review 视图

[xbot/omnifocus-plugin-reset-review-status](https://github.com/xbot/omnifocus-plugin-reset-review-status)

我希望实现早晚两次 Review ，这个插件可以帮我做到这一点。

# 重新排期

[xbot/omnifocus-plugin-reschedule-objects](https://github.com/xbot/omnifocus-plugin-reschedule-objects)

为选中的 Tasks 或 Projects 重新指定 Defer 和 Due 时间。

可用的时间段有：

- Morning   ：9:30  ~ 12:00
- Noon      ：12:00 ~ 14:00
- Afternoon ：14:00 ~ 18:30
- Evening   ：18:30 ~ 23:00
- Daytime   ：9:30  ~ 18:30
- Whole Day ：9:30  ~ 23:00

目前时间段是按我的时间制定的，暂时不可自定义。

# 使用当前网页或邮件创建 Task

[omnifocus.lua](https://github.com/xbot/hammerspoon/blob/master/modules/omnifocus.lua)

这个不是 OmniFocus 插件，是 Hammerspoon 的脚本。

通过快捷键把当前浏览器（Chrome 及其衍生物）页签或电邮（Outlook 或 Mail）添加到 OmniFocus 。

主要使用 Hammerspoon 的 Spoon [SendToOmniFocus](https://www.hammerspoon.org/Spoons/SendToOmniFocus.html) 实现，但它会在创建的 Task 标题前插入 “Review:”前缀，所以我增加了一个快捷键用于过滤它，如果新建的是 Jira Ticket 的 Task ，还会过滤掉无用的后缀。

# 写日记

[每日回顾](https://www.icloud.com/shortcuts/e7ab81894e6a4a0992e7f67bab90d5dc)

这是个快捷指令。

可以列出指定日期从 0 点到第二天凌晨 3 点之间完成的所有任务，你可以选择重要的内容并粘贴到 Day One 中新创建的日记里。