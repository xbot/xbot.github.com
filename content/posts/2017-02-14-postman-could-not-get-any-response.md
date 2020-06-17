---
title: "Postman: Could Not Get Any Response"
date: 2017-02-14 14:37:54
tags:
- restful
- 编程
categories:
- 计算机
---

Postman如果不显示API返回结果，而是报错：

> Could not get any response

有一种原因是响应的header存在错误：

![](https://wx1.sinaimg.cn/large/006tNbRwly1fwvx7lf9t7j30jd04fq3q.jpg)

图中以双引号开头的第一行是有问题的。

PS：[httpie](https://httpie.org)是个好东西。
