# Postman: Could Not Get Any Response


Postman如果不显示API返回结果，而是报错：

> Could not get any response

有一种原因是响应的header存在错误：

![](https://wx1.sinaimg.cn/large/006tNbRwly1fwvx7lf9t7j30jd04fq3q.jpg)

图中以双引号开头的第一行是有问题的。

PS：[httpie](https://httpie.org)是个好东西。

