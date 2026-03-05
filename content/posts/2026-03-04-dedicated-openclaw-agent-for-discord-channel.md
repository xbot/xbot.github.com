---
title: "为 Discord 频道指定专属 agent 实现专注高效处理事务"
slug: "dedicated-openclaw-agent-for-discord-channel"
date: 2026-03-04T20:00:00+08:00
draft: false
tags:
  - 计算机
  - OpenClaw
  - Discord
  - AI
---

OpenClaw 默认只有一个主 agent，所有频道的消息都由它处理。但有些场景需要一个更专注的 agent：只做特定的事，不闲聊，不擅自扩展。

OpenClaw 的 agent 系统支持创建独立的 agent，每个 agent 有自己的工作目录、人格设定和 skill 配置，然后通过路由绑定把特定频道的消息分发给对应的 agent。举个例子，在 Discord 上建一个 RSS 频道，专门用来总结 Miniflux 里的订阅内容。下面记录一下这个配置过程。

## 创建专用工作目录

每个 agent 需要一个独立的工作目录，里面放它的人格设定和可用的 skill。

```bash
mkdir -p ~/.openclaw/workspace-rss/skills
```

### SOUL.md：定义行为边界

SOUL.md 是 agent 的核心，决定了它"是谁"、"能做什么"、"不能做什么"。专用 agent 的关键是把边界画清楚：

```markdown
# SOUL.md - RSS 总结专用 Agent

## 核心原则

你是一个 RSS 内容总结专用助手。你的唯一职责是按照用户的要求，
使用 miniflux-reader skill 获取和总结 Miniflux 中的订阅内容。

## 行为规则

**严格执行，不多不少：**
- 用户让总结几条就总结几条，让总结哪个 feed/category 就总结哪个
- 不主动扩展、不添加用户没要求的内容
- 不闲聊、不寒暄、不发表与 RSS 总结无关的评论

**标记已读规则：**
- 只有在用户明确要求总结新的分类/订阅源时，才把上一轮总结过的条目标记为已读
- 用户只是查询信息、没有要求总结新内容时，不执行标记

## 边界

- 不处理与 RSS/Feed 无关的请求
- 遇到无关请求时，礼貌地说明自己只负责 RSS 内容总结
```

和主 agent 的 SOUL.md 对比，专用 agent 多了很多"不"——不闲聊、不扩展、不越界。这是故意的。通用 agent 需要灵活，专用 agent 需要克制。

### 链接 Skill

把需要的 skill 软链接到工作目录的 skills 目录：

```bash
ln -s ~/.claude/skills/miniflux-reader ~/.openclaw/workspace-rss/skills/miniflux-reader
```

这样 agent 就能使用 miniflux-reader skill 来获取和处理 Miniflux 的未读条目。用软链接而不是复制，好处是 skill 更新时不用同步。

### 其他文件

`IDENTITY.md` 定义名称和 emoji，用来区分是哪个 agent 在回复：

```markdown
- **Name:** RSS 助手
- **Emoji:** 📰
```

`USER.md` 从主工作目录复制过来就行，同一个用户。

## 创建 Agent

用 `openclaw agents add` 创建 agent：

```bash
openclaw agents add rss \
  --workspace ~/.openclaw/workspace-rss \
  --model bailian/glm-5 \
  --non-interactive
```

`--model` 参数改成你自己要用的模型。

设置 identity：

```bash
openclaw agents set-identity --agent rss --name "RSS 助手" --emoji "📰"
```

## 绑定 Discord 频道

这一步是把 Discord 频道的消息路由到专用 agent。需要直接编辑 `~/.openclaw/openclaw.json`，在现有的 `bindings` 数组中追加一条绑定规则（不要覆盖整个文件）：

```json
{
  "agentId": "rss",
  "match": {
    "channel": "discord",
    "peer": {
      "kind": "channel",
      "id": "<你的频道ID>"
    }
  }
}
```

频道 ID 的获取方式：在 Discord 中开启开发者模式（设置 → 高级 → 开发者模式），然后右键频道名 → 复制频道 ID。

改完后重启 gateway：

```bash
openclaw gateway restart
```

用 `openclaw agents bindings` 验证，应该能看到：

```
- rss <- discord peer=channel:<你的频道ID>
```

OpenClaw 的路由匹配支持几种维度：

| 字段 | 用途 |
|------|------|
| `peer.kind` + `peer.id` | 匹配特定频道、群组或 DM |
| `guildId` | 匹配整个 Discord 服务器 |
| `accountId` | 匹配特定账号（多账号场景） |
| `roles` | 按 Discord 角色路由 |

按频道路由用 `peer`，按服务器路由用 `guildId`，按用户路由用 `accountId`。

## 验证

最直观的方式是在 Discord 频道里问"你是谁"。如果回答的是专用 agent 的名字而不是主 agent 的名字，说明路由生效了。
