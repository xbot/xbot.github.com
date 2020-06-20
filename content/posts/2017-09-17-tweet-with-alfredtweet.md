---
title: 用AlfredTweet发推
slug: tweet with alfredtweet
date: 2017-09-17 09:26:41
categories:
- 计算机
tags:
- 最佳实践
- 工具
- mac
---
AlfredTweet是用来解决“最后一公里”问题的东西，有了它就可以随时发推了。

需要修改源码指定代理：

```php
// twitteroauth.php

function http($url, $method, $postfields = NULL) {
    // ...
    curl_setopt($ci, CURLOPT_HTTPPROXYTUNNEL, TRUE);
    curl_setopt($ci, CURLOPT_PROXY, '127.0.0.1:1086');
    curl_setopt($ci, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    // ...
}
```

