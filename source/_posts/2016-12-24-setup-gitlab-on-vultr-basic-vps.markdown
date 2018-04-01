---
layout: post
title: "Vultr基配可以部署Gitlab"
date: 2016-12-24 17:18
comments: true
categories: 計算機
tags:
- VPS
- Gitlab
- Git
- Vultr
---

想在Vultr上部署Gitlab，但是官方文檔上說最低要求1GB RAM + 3GB swap，而我的VPS是基礎配置：768MB RAM。

忐忑地試了一下，居然可以用，只是重啓防火牆的時候CPU一直100%，最後reboot了事。

## 環境
* CentOS 7 x64
* Gitlab CE 8.15

## 參考
* [Setup Swap File on Linux - Vultr.com](https://www.vultr.com/docs/setup-swap-file-on-linux)
* [Installation Guide of GitLab Community Edition  | GitLab](https://about.gitlab.com/downloads/#centos7)
