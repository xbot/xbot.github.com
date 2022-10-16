---
title: "用 Caddy 做 Home Lab 的反向代理"
slug: "Reverse proxying home lab with Caddy"
date: 2022-10-16T22:37:08+08:00
categories: ["计算机"]
tags:
- homelab
- 反向代理
- Caddy
toc: false
---

我原本用 [SWAG](https://docs.linuxserver.io/general/swag) 做 Home Lab 的反向代理，它自带很多服务的配置模板，只需要复制后改几个参数就能工作，还有通过 ACME 自动更新 SSL 证书的功能，但是从某个时间起，首次访问经由它代理的服务会报 404 或 502 的错误，刷新页面就恢复正常，然后隔段时间后又会再次发生，另外还会偶尔出现页面无法显示最新的状态的问题，尤以当服务出错时发生居多。

最近尝试用 [Caddy](https://caddyserver.com/docs/) 代替 SWAG ，效果非常好，再也没有出现上述问题。而且 Caddy 的使用很简单，只需要在 Caddyfile 中加几行配置信息就能实现对一个服务的反向代理。当然，它也集成了自动更新 SSL 证书的功能。此外，它的镜像只有 80 多 MB ，在我测试的几个反向代理服务里是最节省空间的。

出于安全考虑，我没有把 Home Lab 的服务暴露在公网上，所以需要通过 DNS challenge 的方式申请 SSL 证书。我的 Docker 容器部署在 Unraid 上，官方软件仓库里的 Caddy 镜像并不包含 Cloudflare 的模块，所以找了个打包了该模块的[镜像](https://github.com/SlothCroissant/caddy-cloudflaredns)，看起来更新得还很及时。

镜像的 README 里有申请 Cloudflare API Token 的说明，部署容器前填到环境变量里即可。这里有个小坑，创建容器前需要手动创建 Caddyfile ，空文件就行，否则会报错：

> Are you trying to mount a directory onto a file

Caddy 不如 SWAG 的地方是没有为服务提供配置模板，所以我保留了后者的配置目录，作为在前者中配置服务时的参考。以下是我的 Caddyfile 的示例，展示了怎样配置泛域名的反向代理和 SSL 证书的自动申请，以及服务的几种典型反向代理配置：

{{<gist xbot 27040ca35b7763f28403b2242efa3751>}}

此外也试用了 [Nginx Proxy Manager](https://nginxproxymanager.com/) ，这个名字起得一点品牌意识都没有，以前就是因为名字太烂没用它，不过用后发现也是个很好的东西，完全通过 UI 配置，上手很容易，而且配置即时生效，不像 SWAG 和 Caddy 还需要重启服务。