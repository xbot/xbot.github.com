---
layout: post
title: 部分无线路由导致Wordpress无法登录
slug: wordpress fails logging in with some wifi routers
date: 2011-01-31 00:00:00
tags:
- WordPress
- 计算机
- 路由器
status: publish
published: true
comments: true
meta:
  _edit_last: '1'
  views: '635'
  _wp_old_slug: ''
---
问题表现为在登录页面输入用户名和密码并点击登录按钮后，Wordpress跳转到登录页面。

解决方法是修改<strong><em>wp-includes/plugable.php</em></strong>中的<strong><em>wp_set_auth_cookie()</em></strong>函数，将下面这行：

```php
setcookie($auth_cookie_name, $auth_cookie, $expire, ADMIN_COOKIE_PATH, COOKIE_DOMAIN, $secure, true);
```

改成：

```php
setcookie($auth_cookie_name, $auth_cookie, $expire, SITECOOKIEPATH, COOKIE_DOMAIN, $secure, true);
```

这一行在这个函数中有两处，是根据php的版本决定执行哪一块，所以应该根据自己的情况修改，或者干脆两处都修改。

在我的tp-link tl-wr841n中发现这个问题，而中兴无线猫中没有这个问题。
