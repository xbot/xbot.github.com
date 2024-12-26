---
title: 为 Seafile 配置反向代理的问题
slug: Setup Reverse Proxy for Seafile
date: 2022-01-10 23:27:08+08:00
tags:
- NAS
- 计算机
---

我目前用 SWAG 做 Homelab 的反向代理，在和其它服务一样为 Seafile 配置好后，访问时却报如下错误：

> Contradictory scheme headers

原因是 Seafile 的 Docker 镜像里已经使用了 NGINX 做反代，而且配置里包含和 SWAG 反代相同的配置项：

```NGINX
proxy_set_header X-Forwarded-Proto $scheme；
```

此时，通过 HTTPS 访问的 SWAG 反代传递的 HEADER 里 `X-Forwarded-Proto` 是 `https` ，而通过 HTTP 访问的 Seafile 传递的是 `http` ，就会报以上错误。

一种解决方法是，把 Seafile 的 `/etc/nginx/conf.d/seafile.nginx.conf` 复制到宿主机，把文件中 `X-Forwarded-Proto` 这一行注释掉，然后映射到 Docker 容器的上述路径上：

```shell
-v '/mnt/user/appdata/seafile/seafile.nginx.conf':'/etc/nginx/conf.d/seafile.nginx.conf':'ro'
```

需要注意的是，不同版本的 Seafile 镜像里该文件的路径可能不同。
