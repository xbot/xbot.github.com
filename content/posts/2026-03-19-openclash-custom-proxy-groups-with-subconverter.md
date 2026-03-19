---
title: "OpenClash 升级后我这样找回了自定义策略组"
slug: "openclash-custom-proxy-groups-with-subconverter"
date: 2026-03-19T18:00:00+08:00
draft: false
tags:
  - 计算机
  - OpenClash
  - homelab
  - OpenWrt
---

OpenClash v0.47.071 移除了"一键生成"功能。

这个功能之前可以在订阅配置的基础上插入自定义策略组。比如我会按地区筛选节点，组成几个专用的策略组，方便日常切换。升级之后这些自定义策略组全部失效了。

官方推荐用订阅转换来替代一键生成。订阅转换是一个中间服务，把机场提供的订阅链接转换成各种客户端能用的配置文件，转换过程中可以注入自定义的策略组和规则。

## 部署 Subconverter

[Subconverter](https://github.com/MetaCubeX/subconverter) 是一个常用的开源订阅转换工具。用 Docker 部署很简单：

```yaml
subconverter:
  image: ghcr.io/metacubex/subconverter:latest
  container_name: subconverter
  restart: unless-stopped
  ports:
    - "25500:25500"
  volumes:
    - /root/appdata/subconverter/pref.toml:/base/pref.toml
    - /root/appdata/subconverter/snippets/custom_groups.toml:/base/snippets/custom_groups.toml
```

Subconverter 的容器镜像自带了一套完整的默认配置，包括标准策略组、规则集和模板文件，都放在容器的 `/base` 目录下。只需要把要自定义的文件挂载进去，`pref.toml` 是主配置文件，`custom_groups.toml` 是自定义策略组，其余全部保留容器默认的。

## 配置自定义策略组

Subconverter 的默认配置里已经有一套标准策略组（节点选择、自动选择、Netflix、Telegram 等）和对应的规则集。我只需要额外添加自己的策略组就行。

在 `pref.toml` 里，通过 import 把默认策略组和自定义策略组分开管理：

```toml
[[custom_groups]]
import = "snippets/groups.toml"

[[custom_groups]]
import = "snippets/custom_groups.toml"
```

`groups.toml` 是容器自带的，不用动。`custom_groups.toml` 是自己的，内容类似这样：

```toml
[[custom_groups]]
name = "US or JP (Auto)"
type = "url-test"
rule = ["Relay-(JP|US)[12]"]
url = "http://www.gstatic.com/generate_204"
interval = 300
tolerance = 150

[[custom_groups]]
name = "US or JP"
type = "select"
rule = ["[]US or JP (Auto)", "Relay-(JP|US)[12]"]

[[custom_groups]]
name = "US (Auto)"
type = "url-test"
rule = ["Relay-US[12]"]
url = "http://www.gstatic.com/generate_204"
interval = 300
tolerance = 150
```

`rule` 里填的是正则表达式，用来从订阅的节点列表里筛选匹配的节点。比如 `Relay-(JP|US)[12]` 会匹配名称中包含 Relay-JP1、Relay-JP2、Relay-US1、Relay-US2 的节点。

## 代理设置

Subconverter 需要从 GitHub 拉取规则集文件，如果网络环境不能直连，需要在 `pref.toml` 里配置代理：

```toml
proxy_config = "<代理地址>"
proxy_ruleset = "<代理地址>"
proxy_subscription = "<代理地址>"
```

这三项分别对应配置文件下载、规则集下载和订阅链接下载的代理。默认值是 `SYSTEM`，会读取容器的环境变量，如果没设的话实际上就是不走代理。

## 在 OpenClash 中使用

部署好 Subconverter 后，可以先用 curl 测试一下转换结果：

```bash
curl "http://<Subconverter地址>:25500/sub?target=clash&ua=clash&url=<订阅链接>"
```

输出的配置里应该能看到默认的标准策略组和自定义策略组都在，规则集也是完整的。确认没问题后，在 OpenClash 的订阅设置里把转换地址填上就行。
