---
title: "我是怎样接收家庭主机的消息提醒的"
slug: "how-i-receive-notifications-from-home-servers"
date: 2026-03-01T10:00:00+08:00
draft: false
tags:
  - 计算机
  - Gotify
  - Telegram
  - homelab
---

家里两台物理主机都跑的 PVE，PVE 从 8.1 开始原生支持把通知发送到 Gotify。Gotify 是一个开源的自托管消息推送服务，部署简单，API 友好，很适合作为 homelab 的统一消息中心。

不过我日常看消息主要还是在 Telegram，所以需要一个中间层把 Gotify 收到的消息转发过去。

## 之前的方案：gotify-webhook 插件

之前用的是 [gotify-webhook](https://github.com/wuxs/gotify-webhook) 插件，在 Gotify 内部以插件的形式运行，把收到的消息通过 webhook 转发到 Telegram Bot API。功能上没什么问题，但有一个烦人的地方：每次 Gotify 更新都要重新编译插件。

这是 Go 插件系统的限制。Gotify 插件本质上是 Go 的 `.so` 共享库，Go 要求插件和宿主程序必须使用相同的 Go 版本和依赖版本编译，否则无法加载。也就是说，Gotify 每发一个新版本，插件就得用对应的 Go 版本和依赖重新编译一次，版本稍有不对就会报错。长期维护起来很麻烦。

## 现在的方案：Gotigram

最近换成了 [Gotigram](https://github.com/Tiagura/gotigram)。它是一个独立运行的服务，通过 WebSocket 连接 Gotify，收到消息后转发到 Telegram。因为不依赖 Gotify 的插件系统，Gotify 随便更新都不影响它。

不过 Gotigram 是一个比较新的项目，使用过程中遇到了一些问题，我提交了几个 PR 来解决：

**消息模板不可自定义。** 原来的消息格式是固定的，没法按自己的需求调整。我给它加上了 `TELEGRAM_TEMPLATE` 环境变量，支持用 Go 的 `text/template` 语法自定义消息格式，同时开启了 Telegram 的 Markdown 渲染。

**Gotify 重启后不会自动重连。** Gotify 一旦重启，Gotigram 的 WebSocket 连接就断了，之后的消息全部丢失，必须手动重启 Gotigram。我加了带指数退避的自动重连机制，断线后会自动尝试恢复连接。

**Telegram 发送失败没有重试。** 网络波动或 Telegram API 临时不可用时，消息直接丢了。我加了带指数退避的重试机制，同时用一个带缓冲的 channel 把 WebSocket 读取和 Telegram 发送解耦，避免发送阻塞影响消息接收。

## 部署 Gotify

Gotify 用 Docker Compose 部署：

```yaml
services:
  gotify:
    image: gotify/server
    container_name: gotify
    ports:
      - 9090:80
    environment:
      TZ: "Asia/Shanghai"
    volumes:
      - ${HOME}/appdata/gotify:/app/data
    networks:
      - gotify-network
    restart: unless-stopped

networks:
  gotify-network:
    name: gotify-network
```

启动后访问 `http://<ip>:9090`，默认账号密码都是 `admin`，登录后记得改密码。

进入 Gotify 后台，在 **Clients** 页面创建一个 Client，拿到 Client Token，后面 Gotigram 要用。

这里创建了一个名为 `gotify-network` 的 Docker 网络，后面 Gotigram 会加入同一个网络，这样两个容器之间可以直接用服务名通信，不需要通过宿主机端口。

## 部署 Gotigram

同样用 Docker Compose，和 Gotify 放在同一个 compose 文件里：

```yaml
  gotigram:
    image: tiagura/gotigram:latest
    container_name: gotigram
    environment:
      GOTIFY_WS_URL: ws://gotify
      GOTIFY_REST_URL: http://gotify
      GOTIFY_CLIENT_TOKEN: <上一步拿到的 Client Token>
      TELEGRAM_TOKEN: <Telegram Bot Token>
      TELEGRAM_CHAT_ID: <你的 Chat ID>
      SUBSCRIPTIONS_FILE: /subscriptions/subscriptions.json
      HTTP_PROXY: <代理地址>
      HTTPS_PROXY: <代理地址>
      NO_PROXY: gotify,localhost,127.0.0.1
    volumes:
      - ${HOME}/appdata/gotigram:/subscriptions
    networks:
      - gotify-network
    restart: unless-stopped
```

因为 Gotigram 和 Gotify 在同一个 Docker 网络里，`GOTIFY_WS_URL` 和 `GOTIFY_REST_URL` 直接用容器名 `gotify` 就行，不需要写宿主机 IP 和端口。

Gotigram 需要访问 Telegram API，如果网络环境需要代理，可以通过 `HTTP_PROXY` 和 `HTTPS_PROXY` 设置。注意把 `gotify` 加到 `NO_PROXY` 里，避免内部通信也走代理。

Telegram Bot Token 通过 [@BotFather](https://t.me/BotFather) 创建机器人获取。Chat ID 可以先给机器人发一条消息，然后访问 `https://api.telegram.org/bot<TOKEN>/getUpdates` 查看。

启动后在 Telegram 里给机器人发 `/apps`，列出 Gotify 上的所有应用，然后用 `/subscribe <app_id>` 订阅想要转发的应用。订阅完成后用 `/save` 保存，这样重启后不用重新订阅。

## 配置 PVE 通知

PVE 8.1+ 原生支持 Gotify 作为通知目标。在 PVE 管理界面，进入 **Datacenter → Notifications**，点击 **Add → Gotify**：

- **Server URL**：填 Gotify 的地址，比如 `http://<GOTIFY_SERVER>:9090`
- **Token**：在 Gotify 后台的 **Apps** 页面创建一个 Application，把拿到的 Token 填在这里

注意这里的 Token 是 Application Token，不是前面 Gotigram 用的 Client Token。Gotify 里 Application 用来发消息，Client 用来收消息，两者是分开的。

保存后可以点 **Test** 按钮发一条测试消息，如果 Gotigram 那边已经订阅了这个应用，Telegram 上应该很快就能收到。
