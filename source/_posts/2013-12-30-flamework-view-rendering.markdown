---
layout: post
title: "PHP框架實戰（四）：View的模板與渲染"
date: 2013-12-30 21:05
comments: true
categories: 計算機
tags:
- PHP
- Flamework
- 框架
- 編程
---

目標
----

實現MVC模型的**V**iew層，Controller的Action中應可以將從Model層獲取的數據填充到View模板中，並將渲染結果返回給訪問者。本文並不志在實現一個完備的模板框架，相應的需求可借助Smarty這樣現有的實現。

獲取代碼
--------

{% codeblock lang:bash %}
git checkout v0.4
{% endcodeblock %}

設計與實現
----------

View的模板是最好實現的，因為PHP本身就是一個模板語言，所以這裡實現的模板主要是指幾條約定：

  1. 模板文件本身是一個普通PHP文件，文件名後綴是“.php”；
  - 模板文件應存儲在項目指定的模板基礎目錄中；
  - 模板名稱指模板文件相對於項目的模板基礎目錄的路徑，並且去掉文件後綴；
  - 模板本身的實際效果等同於在Controller的Action中執行的代碼，故可以調用Controller的所有方法以及Flame對象的方法等內容；
  - 所有要填充到模板中的數據以鍵值對的形式存儲在一個關聯數組中，並傳遞給渲染模板的方法，在模板中使用與鍵名相同的變量調用數據；

**View的渲染方法**

添加Controller::render()方法：

{% codeblock lang:php %}
<?php

    // ...

    /**
     * Render the view template with data
     * @param string $view View template relative path to base path of the templates
     *                     For example, 'post/list' point to file /srv/http/mysite/protected/view/post/list.php
     * @param array $data Associative array in which data is stored as key-value pairs
     * @return void
     * @since 1.0
     */
    public function render($view, $data)
    {
        extract($data, EXTR_PREFIX_SAME, 'tpl_');
        $viewFile = Flame::app()->getViewPath().DIRECTORY_SEPARATOR.$view.'.php';
        if (is_readable($viewFile)) {
            require($viewFile);
        } else {
            throw new FlameException("View template $view does not exist or cannot be readable.");
        }
    }

    // ...

?>
{% endcodeblock %}

$view是模板名稱，$data是要填充到模板中的數據。

驗證Demo
--------

在Demo項目的protected下新增文件“view/post/list.php”，並創建不存在的這兩個上級目錄“view”和“post”。內容如下：

{% codeblock lang:html %}
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        <title>Post</title>
    </head>
    <body>
        <h1><?php echo $name; ?></h1>
        <p><?php echo $age; ?></p>
    </body>
</html>
{% endcodeblock %}

將DefaultController::index()方法修改為：

{% codeblock lang:php %}
<?php
    
    // ...

    public function index()
    {
        $this->render('post/list', array(
            'name' => 'leigh',
            'age' => 23,
        ));
    }

    // ...

?>
{% endcodeblock %}

訪問Demo項目，頁面將顯示預期的內容和樣式。

總結
----

PHP本身的特點使得View這一層很容易實現。不過，隨著富客戶端的流行，MVC模型的View這一層正在被逐漸弱化，現在主流的開發方式是前端通過AJAX與服務器端交換數據，而不是把數據填充到模板中再返回給客戶端。
