---
title: "我在用的 OmniFocus 插件"
slug: "the Omnifocus Plugins I Am Using"
date: 2021-12-25T22:05:40+08:00
categories:
- 青梅煮酒
tags:
- OmniFocus
- GTD
---
# 生成工作日志

[generate-work-journal.omnijs](https://github.com/xbot/omnifocus-plugins/blob/3db61166c7470d4fc7698c84852bac64cd330e2c/generate-work-journal.omnijs)

用于每天下班前一键生成工作日志。

插件会收集名为 `Work` 的目录下当天完成的 Tasks 并根据 Task 标题和 Note 的内容追加不同的状态描述。例如，标题包含“面试”的 Task 不追加状态描述；Note 为空的 Task 追加“完成”，否则追加 Note 的内容。目录目前是写死的，没有做通用性适配。

# 打开 Jira Ticket

[open-jira-ticket.omnijs](https://github.com/xbot/omnifocus-plugins/blob/3db61166c7470d4fc7698c84852bac64cd330e2c/open-jira-ticket.omnijs)

从被选中的 Project 标题中解析 Jira Ticket 序号，并根据配置的 Jira URL 打开对应的 Ticket 。如果选中的是 Task ，使用其所属的 Project 。

# 在 Flomo 中搜索当前 Project

[search-flomo-by-project.omnijs](https://github.com/xbot/omnifocus-plugins/blob/3db61166c7470d4fc7698c84852bac64cd330e2c/search-flomo-by-project.omnijs)

从被选中的 Project 标题中按自定义规则解析关键词，并在 Flomo 中搜索相关的笔记。可以指定一个 Tag 辅助过滤。

这个插件的通用性做得稍好，解析关键词的正则表达式可以自定义，解析出的结果可以填充到自定义模板中作为 Flomo 的搜索词。

Flomo 目前只支持通过单个 Tag 过滤，所以配置界面只能指定一个 Tag 。

# 转换 Tasks 为指定目录下的 Projects

[convert-tasks-to-projects.omnijs](https://github.com/xbot/omnifocus-plugins/blob/3db61166c7470d4fc7698c84852bac64cd330e2c/convert-tasks-to-projects.omnijs)

OmniFocus 自带的转换功能会把新 Project 追加到库的末尾，需要先跳转到 Projects Perspective 再拖动到目标目录。所以写了这个插件，转换前先指定目标目录，转换后自动跳转到新的 Projects 。

选中的目录会被记录，下次执行转换操作时默认选中上次使用的目录。

# 使用当前网页或邮件创建 Task

[omnifocus.lua](https://github.com/xbot/hammerspoon/blob/9a2abed9d9997e1f602cc34fb4381d235a2079d2/modules/omnifocus.lua)

这个不是 OmniFocus 插件，是 Hammerspoon 的脚本。

通过快捷键把当前浏览器（Chrome 及其衍生物）页签或电邮（Outlook 或 Mail）添加到 OmniFocus 。

主要使用 Hammerspoon 的 Spoon [SendToOmniFocus](https://www.hammerspoon.org/Spoons/SendToOmniFocus.html) 实现，但它会在创建的 Task 标题前插入 “Review:”前缀，所以我增加了一个快捷键用于过滤它，如果新建的是 Jira Ticket 的 Task ，还会过滤掉无用的后缀。
