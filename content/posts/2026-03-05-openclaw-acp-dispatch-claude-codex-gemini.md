---
title: "OpenClaw 这个功能，让我在一个对话窗口同时用上 Claude、Codex 和 Gemini"
slug: "openclaw-acp-dispatch-claude-codex-gemini"
date: 2026-03-05T21:00:00+08:00
draft: false
tags:
  - 计算机
  - OpenClaw
  - AI
---

OpenClaw 通过 API 调用各种大模型（如 GLM、MiniMax、Qwen 等）处理对话，但有时需要调用专门的 AI 编程工具来完成复杂任务。OpenClaw 的 ACP（Agent Client Protocol）支持通过 acpx 插件调用 Claude Code、Codex、Gemini CLI 等外部 AI 工具，让不同场景下使用最合适的模型。

比如写代码时调用 Claude Code，做代码审查时用 Codex，快速原型验证时用 Gemini。配置完成后，在对话中说明要使用的工具，OpenClaw 会尝试路由到对应的 ACP agent。

## 前置条件

开始配置前需要确保：

1. **OpenClaw 已安装并运行**
2. **要使用的 AI 工具 CLI 已安装**

## 安装 acpx 插件

```bash
openclaw plugins install @openclaw/acpx
```

## 配置 openclaw.json

编辑 `~/.openclaw/openclaw.json`，追加以下配置：

> ⚠️ 注意：请勿覆盖已有配置，只需添加或合并相应字段。

```json
{
  "acp": {
    "enabled": true,
    "dispatch": { "enabled": true },
    "backend": "acpx",
    "defaultAgent": "claude"
  },
  "plugins": {
    "entries": {
      "acpx": {
        "enabled": true,
        "config": {
          "permissionMode": "approve-all"
        }
      }
    }
  }
}
```

`defaultAgent` 设为默认使用的 AI 工具，`permissionMode` 必须设为 `approve-all`（因为非交互场景需要自动批准写入操作）。

## 重启 Gateway

修改配置后需要重启 OpenClaw Gateway 使配置生效：

```bash
openclaw gateway restart
```

## 验证配置

重启后检查插件是否加载：

```bash
openclaw plugins list | grep acpx
```

## 使用方法

配置完成后，可以在 OpenClaw 对话中直接说明要使用的工具：

```
用 Claude Code 帮我重构这个函数
```

```
用 Codex 帮我 review 这段代码
```

```
用 Gemini 写一个 Python 脚本处理这个 CSV 文件
```

OpenClaw 会识别工具名称并路由到对应的 ACP agent。路由效果受 `defaultAgent` 配置和提示词歧义影响，如果路由不符合预期，可以在提示词中明确指定工具名称。

## 小结

Claude Code、Codex、OpenCode 这些 AI 编程工具也有各自的多 agent 能力，但它们主要是在自己的框架内拆分任务、并行执行，而不是去调用其他厂商的工具。这些工具支持 ACP 协议，但方向是"被调用"——让编辑器或平台能启动和控制它们。OpenClaw 做的事正好反过来：它作为调用方，通过 ACP 把这些工具当作独立进程调起来。你在 Discord 或 Telegram 里说句话就能切换工具，不需要自己开终端。
