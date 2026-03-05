---
title: "OpenClaw 配置 Discord 客户端的方法"
slug: "configure-openclaw-with-discord-client"
date: 2026-03-04T15:30:00+08:00
draft: false
tags:
  - 计算机
  - OpenClaw
  - Discord
  - AI
---

OpenClaw 是一个开源的多平台 AI agent 框架，支持 Discord、Telegram 等多种聊天平台作为客户端，可以配置多个 agent 和不同的模型。Discord 相比 Telegram 消息格式更丰富，支持代码高亮、嵌入卡片和按钮交互，更适合技术讨论和代码相关的场景。本文记录了配置 OpenClaw 使用 Discord 作为客户端的完整过程。

## 一、创建 Discord Bot

首先需要在 Discord Developer Portal 创建一个应用（Bot 会自动创建）。

1. 访问 [Discord Developer Portal](https://discord.com/developers/applications)
2. 点击 **New Application** 创建应用，名称可以随便起，比如「OpenClaw」
3. 进入左侧 **Bot** 标签页，在 **Privileged Gateway Intents** 中启用两个选项：
   - ✅ **Message Content Intent**（必需，用于读取消息内容）
   - ✅ **Server Members Intent**（推荐，用于角色权限匹配）
4. 点击 **Reset Token** 生成 Bot Token，**复制保存好**，后面配置要用

## 二、邀请 Bot 到服务器

Bot 创建后需要邀请到你的 Discord 服务器。如果还没有服务器，需要先创建一个：

1. 打开 Discord 客户端，左侧服务器列表底部点击 **+** 号
2. 选择 **创建服务器**，输入名称后创建

接下来生成邀请链接：

1. 回到 Developer Portal，进入你的应用
2. 左侧 **OAuth2** → **URL Generator**
3. Scopes 勾选 **bot** 和 **applications.commands**
4. Bot Permissions 勾选：
   - Read Messages / View Channels
   - Send Messages
   - Read Message History
   - Embed Links
   - Attach Files
   - Add Reactions（可选）
5. 底部会生成一个 URL，复制后在浏览器打开
6. 选择你的服务器，点击 **授权**

完成后在 Discord 服务器里应该能看到 Bot 上线了（绿色状态）。

## 三、配置 OpenClaw

### 基础配置

编辑 `~/.openclaw/openclaw.json`，添加 Discord 配置：

```json
{
  "channels": {
    "discord": {
      "enabled": true,
      "token": "你的_DISCORD_BOT_TOKEN"
    }
  }
}
```

或者用命令行设置：

```bash
openclaw config set channels.discord.token "你的_DISCORD_BOT_TOKEN"
```

### 解决群组消息不响应的问题

默认配置下，`groupPolicy` 是 `allowlist` 模式，但没有添加任何群组到白名单，导致所有群组消息都被静默丢弃。需要改成开放模式：

```bash
openclaw config set channels.discord.groupPolicy "open"
openclaw gateway restart
```

这个设置对 Discord 和 Telegram 都有效：

```bash
openclaw config set channels.telegram.groupPolicy "open"
```

### 配置频道免 @ 响应和授权斜杠命令

默认情况下，在 Discord 频道里需要 @ Bot 才能触发响应，且斜杠命令（如 `/reset`、`/status`）只有管理员可以使用。如果想让 Bot 响应所有消息并授权特定用户使用斜杠命令，需要在服务器级别配置。

建议直接编辑配置文件：

```json
{
  "channels": {
    "discord": {
      "enabled": true,
      "token": "你的_DISCORD_BOT_TOKEN",
      "groupPolicy": "open",
      "guilds": {
        "你的服务器ID": {
          "requireMention": false,
          "users": ["你的用户ID"]
        }
      }
    }
  }
}
```

**获取 ID**：
- **服务器 ID**：在 Discord 中开启开发者模式（设置 → 高级 → 开发者模式），然后右键服务器名 → 复制服务器 ID
- **用户 ID**：右键你的 Discord 头像 → 复制用户 ID

**注意**：用户 ID 在配置中必须用字符串（带双引号），不要写成数字类型。Discord 用户 ID 是大整数，用数字类型会有精度丢失风险。

配置后重启 Gateway：

```bash
openclaw gateway restart
```

这里 `groupPolicy: "open"` 允许所有人在频道聊天，`requireMention: false` 让 Bot 响应所有消息（不需要 @），`users` 列表控制谁能使用斜杠命令。

**注意**：斜杠命令授权是在 `channels.discord.guilds.<服务器ID>.users` 中配置，不是 `commands.allowFrom`。

### 关于流式输出

OpenClaw 支持流式输出（打字效果），通过 `channels.discord.streaming` 设置，可选值有 `"off"`、`"partial"`。但实际体验下来，Discord 的流式输出效果不太好，消息频繁编辑会导致闪烁，建议保持默认的 `"off"`。

## 四、验证配置

完成上述配置后，重启 Gateway：

```bash
openclaw gateway restart
```

然后在 Discord 频道里发消息测试：

1. 直接发消息（不 @），Bot 应该能响应
2. 使用 `/status` 或 `/reset` 命令，应该能正常执行

## 五、常见问题

### Bot 在线但不响应消息

检查 `groupPolicy` 是否设置为 `"open"`：

```bash
openclaw config get channels.discord.groupPolicy
```

### 斜杠命令提示未授权

检查服务器的 `users` 列表配置：

```bash
openclaw config get channels.discord.guilds."你的服务器ID".users
```

注意：斜杠命令授权是在 `guilds.<服务器ID>.users` 中配置，不是 `commands.allowFrom`。
