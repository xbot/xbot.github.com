---
title: "OpenClaw 2026.3.7 新功能：ACP Binding 持久会话配置指南"
slug: "acp-binding-persistent-session-configuration-guide"
date: 2026-03-10T12:30:00+08:00
draft: false
tags:
  - 计算机
  - OpenClaw
  - AI
---

之前写过一篇《[OpenClaw 这个功能，让我在一个对话窗口同时用上 Claude、Codex 和 Gemini](/posts/openclaw-acp-dispatch-claude-codex-gemini/)》的文章，那个方案是 ACP Dispatch——在对话中说"用 Claude Code 帮我写个脚本"，OpenClaw 识别意图后路由到对应的工具。用起来没问题，但每次都要在提示词里指定工具名，有时路由不准确。

OpenClaw 2026.3.7 新增了 ACP Binding，可以把 Discord 频道和外部 AI 工具做固定绑定：频道 A 的消息永远走 Claude Code，频道 B 永远走 Codex，不需要每次说明，也不会路由错。

## ACP Dispatch vs ACP Binding

两者的区别：

| | ACP Dispatch | ACP Binding |
|---|---|---|
| 触发方式 | 对话中指定工具名 | 按频道自动路由 |
| 路由准确性 | 依赖语义识别，可能走偏 | 固定绑定，不会走偏 |
| 会话持久性 | 单次调用 | 持久会话，保留上下文 |
| 配置复杂度 | 只需开启 ACP | 需要定义 agent + binding |

简单说，Dispatch 适合偶尔用一下的场景，Binding 适合给特定频道指定专属工具。

## 前置条件

- OpenClaw 2026.3.7 或更高版本
- 要使用的 AI 工具 CLI 已安装（Claude Code、Codex、OpenCode、Gemini CLI 等）
- ACP 基础配置已完成（参考《[OpenClaw 这个功能，让我在一个对话窗口同时用上 Claude、Codex 和 Gemini](/posts/openclaw-acp-dispatch-claude-codex-gemini/)》）

## 配置步骤

以把一个 Discord 频道绑定到 Claude Code 为例，需要三步：定义 agent、添加 binding、准备工作目录。

### 1. 定义 ACP Agent

编辑 `~/.openclaw/openclaw.json`，在 `agents.list` 数组中添加一个 agent：

```json
{
    "id": "claude",
    "runtime": {
        "type": "acp",
"acp": {
            "agent": "claude",
            "backend": "acpx",
            "mode": "persistent",
            "cwd": "$HOME/.openclaw/workspace-claude"
        }
    },
    "identity": {
        "name": "Claude",
        "emoji": "🧠"
    }
}
```

关键字段：

- `acp.agent` 是 acpx 中注册的工具名，比如 `claude`、`codex`、`opencode`、`gemini`

### 2. 添加 ACP Binding

在 `bindings` 数组中添加一条绑定规则：

```json
{
    "type": "acp",
    "agentId": "claude",
    "match": {
        "channel": "discord",
        "peer": {
            "kind": "channel",
            "id": "<你的频道ID>"
        }
    },
    "acp": {
        "label": "claude-code"
    }
}
```

### 3. 准备工作目录

创建 agent 的工作目录，并放入对应 CLI 工具的指令文件：

```bash
mkdir -p ~/.openclaw/workspace-claude
cat > ~/.openclaw/workspace-claude/CLAUDE.md << 'EOF'
## Language

- Respond in the same language as the user's message
- Default to Chinese (Simplified) when ambiguous

## Behavior

- Be concise and direct
- Prefer action over asking for confirmation on simple tasks
- When working with code, read existing files before making changes
EOF
```

不同的 CLI 工具读取不同的指令文件：

| CLI 工具 | 指令文件 |
|---------|---------|
| Claude Code | `CLAUDE.md` |
| Codex | `AGENTS.md` |
| OpenCode | `AGENTS.md` |
| Gemini CLI | `GEMINI.md` |

在工作目录中创建对应的指令文件，写上基础指令即可，上面的 `CLAUDE.md` 就是一个最小可用的例子。

如果工作目录是由 OpenClaw 自动创建的，里面会有一些模板文件（AGENTS.md、SOUL.md、TOOLS.md 等），这些是 OpenClaw 自身 bootstrap 系统用的，ACP agent 不需要，可以清理掉，只保留外部 CLI 工具对应的指令文件。

同样的模式可以复制到其他工具。比如同时配置 Claude Code、Codex、Gemini，每个绑定一个 Discord 频道。

### 4. 重启 gateway：

```bash
openclaw gateway restart
```

## 验证

重启后用 `openclaw status` 检查，Agents 一行应该能看到所有配置的 agent：

```
Agents: 4 · sessions 26 · default main active 3h ago
Heartbeat: 30m (main), disabled (claude), disabled (codex), disabled (gemini)
```

然后在对应的 Discord 频道里发消息测试。

![20260310162320374-cd8c9e0e59865c59061ce4564f768dbb](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20260310162320374-cd8c9e0e59865c59061ce4564f768dbb.avif)

## acpx 内部的差异

虽然在 `openclaw.json` 里四个 ACP agent 的配置格式完全一样，但 acpx 底层启动它们的方式并不相同。查看 acpx 源码（`mcp-agent-command.ts`，以 acpx 0.1.15 为例）可以看到内置的启动命令：

| Agent | acpx 启动命令 | ACP 接入方式 |
|-------|-------------|------------|
| Claude | `npx -y @zed-industries/claude-agent-acp` | 专用 ACP 适配器 |
| Codex | `npx @zed-industries/codex-acp` | 专用 ACP 适配器 |
| OpenCode | `npx -y opencode-ai acp` | CLI 内置 `acp` 子命令 |
| Gemini | `gemini` | 原生 CLI，需要额外参数 |

Claude 和 Codex 都有专门的 ACP 适配器包，OpenCode 的 CLI 内置了 `acp` 子命令，三者启动后都能直接说 ACP 协议。Gemini 不一样——acpx 只是裸启动 `gemini`，没有传任何参数。而当前版本的 Gemini CLI 需要加 `--experimental-acp` 参数才会进入 ACP 模式，否则就进入普通的交互式聊天，坐等终端输入，永远不会和 acpx 建立协议通信。

修复方法是创建 acpx 全局配置，覆盖 Gemini 的启动命令：

```bash
mkdir -p ~/.acpx
cat > ~/.acpx/config.json << 'EOF'
{
  "agents": {
    "gemini": {
      "command": "gemini --experimental-acp"
    }
  }
}
EOF
```

然后重启 gateway：

```bash
openclaw gateway restart
```

后续版本可能会改为默认行为或者换个参数名，到时候需要同步更新 `~/.acpx/config.json`。

## 排查问题

配置过程中容易遇到几个问题：

**消息走了主 agent。** 大概率是 binding 里没有设置 `type: "acp"`，或者 `agentId` 对应的 agent 没有在 `agents.list` 中定义。检查配置文件，确保两边的 `id` 一致。

**ACP error (ACP_TURN_FAILED)。** 说明 ACP 路由成功了，但外部工具执行出错。手动在终端启动对应的 AI 工具（如 `claude`、`codex`、`gemini`），输入一条简单测试消息，看是否能正常响应。 如果工具本身就报错（比如 Codex 的 403），那是工具自身的认证问题，不是 OpenClaw 的配置问题。

**Gemini 频道发消息完全没反应。** 大概率是上面说的 `--experimental-acp` 问题。确认方法：`ps aux | grep acpx` 看是否有 `gemini sessions ensure` 进程长时间挂起。如果有，按上面的方法创建 `~/.acpx/config.json` 并重启 gateway。

**升级后插件路径失效。** OpenClaw 通过 Homebrew 安装时，插件路径包含版本号（如 `/opt/homebrew/Cellar/openclaw-cli/2026.3.7/...`）。升级后旧版本目录会被删除，需要更新 `~/.openclaw/openclaw.json` 文件中的 `plugins.load.paths` 和 `plugins.installs` 路径。
