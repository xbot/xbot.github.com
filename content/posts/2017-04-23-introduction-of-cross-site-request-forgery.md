---
title: Cross-site Request Forgery简介
date: 2017-04-23 20:55:58
categories:
- 计算机
tags:
- 笔记
- 编程
---

## 什么是CSRF

Cross-site Request Forgery（简称CSRF），意思是跨站请求伪造。原理是利用网站服务器对浏览器的信任，通过一些技术手段欺骗用户的浏览器访问自己曾经认证过的网站，并执行一些危害用户利益的操作。

例如，假设网站的会话信息保存在cookie中，提现的请求是`GET http://www.sample.com/withdraw?toCard=123456&amount=10000`，攻击者在自己的网页中通过img标签、iframe或者AJAX访问这个链接，并诱使用户访问这个网页，如果用户刚刚登录过sample.com，余额就会被转走。

## 防止CSRF的方法

CSRF的关键在于用户的鉴权信息保存在cookie中，或攻击者可以拿到它。目前防止CSRF攻击的方法主要有两种：JWT和表单校验token。

JWT的token通过HTTP请求的header传递，且由于浏览器的跨站限制，钓鱼网页中的JS无法拿到token，从而达到防止CSRF的目的。关于JWT更详细的介绍在「[JSON Web Token简介](/post/introduction-of-json-web-token/)」。

表单校验token通常是存储在表单的隐藏字段中的一个随机字符串，服务器端通过比对表单提交的token判断请求是否伪造，此时需要注意服务器端的CORS配置，即便在有需要的情况下开放了，也应该严格限制允许的HTTP方法和域，否则，钓鱼网页的JS就可以拿到这个token，从而实现CSRF。

