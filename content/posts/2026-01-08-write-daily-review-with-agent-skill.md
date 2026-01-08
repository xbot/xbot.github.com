---
title: "用 Agent Skill 写日记"
slug: "write-daily-review-with-agent-skill"
date: 2026-01-08T22:00:40+08:00
draft: false
tags:
- 计算机
- agent-skill
- 最佳实践
---
用 Agent Skill 实现了在 Mac 上自动写每日回顾，比之前在 iPhone 上用快捷指令方便。

因为限制较多，用快捷指令只能实现半自动化。需要手动选择 OmniFocus 里的已完成事项，然后发送给 ChatGPT 总结，最后在 Day One 里创建新日记后还要手动修改部分属性和内容。

Mac 上做同样的事就自由多了。Apple Script 很强大，可以自动从 OmniFocus 里取出完全符合要求的已完成事项，不用再手工挑拣。Agent Skill 也比简单地给 ChatGPT 喂 Prompt 靠谱得多。Day One 的命令行工具提供了足够多的选项，能避免在手机上还要打开 App 修改日记属性的问题。几乎实现了完全的自动化。

![20260108220606847-25c29be535441af2d67f117ca061458a](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20260108220606847-25c29be535441af2d67f117ca061458a.avif)
