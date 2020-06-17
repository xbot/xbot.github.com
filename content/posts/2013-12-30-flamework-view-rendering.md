---
layout: post
title: "PHP框架实战（四）：View的模板与渲染"
slug: flamework view rendering
date: 2013-12-30 21:05:00
comments: true
categories:
- 计算机
tags:
- PHP
- Flamework
- 框架
- 编程
---

目标
----

实现MVC模型的**V**iew层，Controller的Action中应可以将从Model层获取的数据填充到View模板中，并将渲染结果返回给访问者。本文并不志在实现一个完备的模板框架，相应的需求可借助Smarty这样现有的实现。

获取代码
--------

```bash
git checkout v0.4
```

设计与实现
----------

View的模板是最好实现的，因为PHP本身就是一个模板语言，所以这里实现的模板主要是指几条约定：

  1. 模板文件本身是一个普通PHP文件，文件名后缀是“.php”；
  - 模板文件应存储在项目指定的模板基础目录中；
  - 模板名称指模板文件相对于项目的模板基础目录的路径，并且去掉文件后缀；
  - 模板本身的实际效果等同于在Controller的Action中执行的代码，故可以调用Controller的所有方法以及Flame对象的方法等内容；
  - 所有要填充到模板中的数据以键值对的形式存储在一个关联数组中，并传递给渲染模板的方法，在模板中使用与键名相同的变量调用数据；

**View的渲染方法**

添加Controller::render()方法：

```php
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
```

$view是模板名称，$data是要填充到模板中的数据。

验证Demo
--------

在Demo项目的protected下新增文件“view/post/list.php”，并创建不存在的这两个上级目录“view”和“post”。内容如下：

```html
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
```

将DefaultController::index()方法修改为：

```php
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
```

访问Demo项目，页面将显示预期的内容和样式。

总结
----

PHP本身的特点使得View这一层很容易实现。不过，随著富客户端的流行，MVC模型的View这一层正在被逐渐弱化，现在主流的开发方式是前端通过AJAX与服务器端交换数据，而不是把数据填充到模板中再返回给客户端。
