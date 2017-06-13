---
layout: post
title: "接管PHP致命錯誤的方法"
date: 2014-11-12 00:47
comments: true
categories: 計算機
tags:
- 編程
- PHP
---

Yii 2.0引入了一項新特性，可以接管PHP的致命錯誤。在此之前，如果PHP源碼有語法錯誤，框架本身是不會處理的。

實現的思路如下：

- 禁止顯示錯誤
- 註冊自定義的shutdown回調函數
- 在回調函數中獲取最近的錯誤
- 若錯誤是致命錯誤，調用相應的處理邏輯

代碼如下：

{% codeblock lang:php %}
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
{% endcodeblock %}
