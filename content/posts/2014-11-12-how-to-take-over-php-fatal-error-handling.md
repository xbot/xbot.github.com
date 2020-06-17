---
layout: post
title: "接管PHP致命错误的方法"
slug: how to take over php fatal error handling
date: 2014-11-12 00:47:00
comments: true
categories:
- 计算机
tags:
- 编程
- PHP
---

Yii 2.0引入了一项新特性，可以接管PHP的致命错误。在此之前，如果PHP源码有语法错误，框架本身是不会处理的。

实现的思路如下：

- 禁止显示错误
- 注册自定义的shutdown回调函数
- 在回调函数中获取最近的错误
- 若错误是致命错误，调用相应的处理逻辑

代码如下：

```php
<?php

// ...

ini_set('display_errors', false);
register_shutdown_function(function(){
    $error = error_get_last();
    if (isset($error['type']) && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING])) {
        ob_clean();
        echo '<pre>'; var_dump($error); echo '</pre>';
        exit(1);
    }
});

// ...

?>
```
