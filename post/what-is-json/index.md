# JSON是对象还是字符串？


前两天，一个前端跟我争论说JSON是对象。我在接口文档里的数据格式写的是JSON，结果对方真就传了个对象过来。

要搞清楚JSON是什么，先得知道JSON是为了解决什么问题的。维基百科里的定义是：

> JSON（JavaScript Object Notation）是一种由道格拉斯·克罗克福特构想设计、轻量级的数据交换语言，以文字为基础，且易于让人阅读。尽管JSON是Javascript的一个子集，但JSON是独立于语言的文本格式，并且采用了类似于C语言家族的一些习惯。

所以说，JSON是为了解决语言之间数据交换的一种文本格式，体现在数据上，JSON就是字符串类型。那么为什么需要为数据交换制订一种通用的文本格式呢？可以做一个简单的试验：

用JavaScript建立一个到PHP的WebSocket并发送一个对象：

```javascript
var ws = new WebSocket('ws://127.0.0.1:4759');
ws.send({name:"hello"});
```

PHP接收并打印对象：

```php
use Workerman\Worker;
$worker = new Worker('websocket://0.0.0.0:4759');
$worker->onMessage = function($connection, $data) {
        var_dump($data);
}
Worker::runAll();
```

得到的结果是：

> [object Object]

所以，一种语言的对象的实例以二进制形式直接传递给另一种语言是无法识别的，因此需要把对象数据用文本描述之后再行传递。

此外，相对于XML等其它格式，JSON有可读性上的优势，如果是对象，二进制的数据哪来的可读性？

