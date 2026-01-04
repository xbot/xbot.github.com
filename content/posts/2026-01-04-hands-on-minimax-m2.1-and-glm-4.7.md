---
title: "MiniMax m2.1 和 GLM 4.7 的初体验"
slug: "hands-on-minimax-m2.1-and-glm-4.7"
date: 2026-01-04T22:34:52+08:00
draft: false
tags:
- 编程
- AI
- LLM
---
准备用 Agent Skills 实现写日记的自动化。先写个收集 OmniFocus 中已完成事项的 Apple Script ，顺便测试一下最近很火的 MiniMax m2.1 和 GLM 4.7 。

结果有点意外。

一个很简单的小需求：OmniFocus 的项目和标签都是支持命名空间的，比如：`Life:Chore` 或 `Area:Tool:Logseq`。我希望脚本在获取已完成事项时，把所有项目是“Learn”或是它的子项目的条目筛选出来。这两个模型不约而同地写出来的都是「当项目名称包含“Learn”时，就选择该条目」，只有 Sonnet 4.5 很精准地理解了需求，写的是「当项目名称等于“Learn”或以“Learn:”开头时，选择该条目」。

盛名之下，没想到在这么平常的一个点上，还不如 Sonnet 4.5 。
