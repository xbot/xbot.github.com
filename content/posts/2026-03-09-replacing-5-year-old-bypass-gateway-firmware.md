---
title: "我把用了五年的旁路网关固件换掉了"
slug: "replacing-5-year-old-bypass-gateway-firmware"
date: 2026-03-09T20:00:00+08:00
draft: false
tags:
  - 计算机
  - OpenWrt
  - ImmortalWrt
  - homelab
---

家里用 OpenWrt 跑旁路网关，主要做透明代理。之前一直用 eSir 的高大全固件，最近换成了 ImmortalWrt。

## eSir 高大全固件

高大全，顾名思义，什么都预装。插件列表拉出来一长串，DNS 分流、多拨、各种代理协议……实际用到的就一个 OpenClash，剩下的全是摆设。

更大的问题是没法更新。eSir 只提供完整镜像，不支持增量升级。想升级只能下载新固件全新安装，然后从头配置。

## eSir 佛跳墙固件

后来 eSir 出了佛跳墙版本，精简不少，预装插件少了，系统清爽。日常使用没问题。

但核心痛点还在——不能平滑升级。eSir 升级时不能保留配置，每次都得从头设置。一年更新两三次倒也能忍，但每次重配 OpenClash 挺烦的，订阅、规则、DNS 设置，一套弄下来得半小时。

拖了五年才换，就像一辆从不保养的车——小问题忍着忍着，最后变成不想换的惯性。**五年前的选择，今天需要还债。技术债务不会消失，只会利息越滚越高。**

## 换到 ImmortalWrt

既然核心诉求就是能平滑升级，不如直接用有持续维护和包管理的发行版。ImmortalWrt 是 OpenWrt 的社区分支，面向中国大陆用户做了本地化优化，支持 `sysupgrade`，升级时大部分配置可以保留，不用每次从头来。

这次迁移我用了 [OpenClaw](/posts/openclaw-acp-dispatch-claude-codex-gemini/) 来协调：让 Claude Code 根据我的环境整理了一个完整的部署计划，然后让 Codex 审核，检查有没有遗漏或不合理的地方，确认没问题后再执行。

如果对在 PVE 上部署 ImmortalWrt 旁路网关感兴趣，具体步骤可以参考这篇：[PVE + ImmortalWrt 旁路网关部署实战](/posts/pve-immortalwrt-bypass-gateway-deployment/)。
