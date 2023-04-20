---
title: 一次对Workflow不完全成功的抓包过程
slug: a failed attempt to capture workflow requests
date: 2017-04-04 20:19:52
categories:
- 计算机
tags:
- 网络
- 笔记
---

一个workflow对https链接的请求总是失败，而同样的接口在postman里是成功的。所以想对比一下两个请求的差异。

尝试了两种抓包方式，都不成功。一种是用中间人攻击的原理，用的是mitmproxy，类似的还有charles、fiddler等。另一种是从网卡直接抓取，用的是wireshark。

第一种情况，虽然在iOS里安装并信任了mitmproxy的伪证书、safari里也是可以正常访问https链接的，但在workflow里仍然不认。

第二种情况，wireshark支持两种解密TLS包的方式：一是使用https服务的私钥，二是用浏览器输出的「SSLKEYLOGFILE」。这里需要用第二种。但是这种方式只对本机发送的请求有效，而且在实际操作中还发现有时候本机也解密不了。

最后只能采用临时方案，把https链接改成http，然后用mitmproxy抓取，虽然请求是失败的，但是可以拿到请求本身的数据。