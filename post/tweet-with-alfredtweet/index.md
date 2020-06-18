# 用AlfredTweet发推

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



