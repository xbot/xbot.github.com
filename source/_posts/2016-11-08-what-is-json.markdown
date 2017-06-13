---
layout: post
title: "JSON是對象還是字符串？"
date: 2016-11-08 10:46
comments: true
categories: 計算機
tags:
- 筆記
---

前兩天，一個前端跟我爭論說JSON是對象。我在接口文檔里的數據格式寫的是JSON，結果對方真就傳了個對象過來。

要搞清楚JSON是什麼，先得知道JSON是為瞭解決什麼問題的。維基百科里的定義是：

> JSON（JavaScript Object Notation）是一種由道格拉斯·克羅克福特構想設計、輕量級的數據交換語言，以文字為基礎，且易於讓人閱讀。儘管JSON是Javascript的一個子集，但JSON是獨立於語言的文本格式，並且採用了類似於C語言家族的一些習慣。

所以說，JSON是為瞭解決語言之間數據交換的一種文本格式，體現在數據上，JSON就是字符串類型。那麼為什麼需要為數據交換制訂一種通用的文本格式呢？可以做一個簡單的試驗：

用JavaScript建立一個到PHP的WebSocket併發送一個對象：

```javascript
var ws = new WebSocket('ws://127.0.0.1:4759');
ws.send({name:"hello"});
```

PHP接收並打印對象：

```php
use Workerman\Worker;
$worker = new Worker('websocket://0.0.0.0:4759');
$worker->onMessage = function($connection, $data) {
        var_dump($data);
}
Worker::runAll();
```

得到的結果是：

> [object Object]

所以，一種語言的對象的實例以二進制形式直接傳遞給另一種語言是無法識別的，因此需要把對象數據用文本描述之後再行傳遞。

此外，相對於XML等其它格式，JSON有可讀性上的優勢，如果是對象，二進制的數據哪來的可讀性？
