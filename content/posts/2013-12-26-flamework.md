---
layout: post
title: "PHP框架实战：Flamework"
slug: flamework
date: 2013-12-26 20:24:00
comments: true
categories:
- 计算机
tags:
- PHP
- Flamework
- 框架
- 编程
---

从今天开始，逐步实现一个PHP的MVC框架，以践行平时对这方面的一些想法。

项目信息
--------

 - 名称：Flamework (_Flame Framework_)
 - 源码：https://github.com/xbot/flamework

框架特性
--------

**激进**

用PHP高版本引入的新特性，不考虑向前兼容问题，没有历史包袱。

**命名空间**

PHP 5.3引入的命名空间可以有效避免类命名冲突，这样可以使类名看起来更自然，不用再在类名前面加难看的前缀了。

**类的自动加载**

手动include会增加维护的难度，因为经常会出现一个类被从源码中移除而它的include行还在的问题，这会拖慢程序执行速度、增加内存占用。

实现类的自动加载可以在类被引用时自动include相应的源码。

**异常的自动处理**

在设计程序时，一般应该把用户级的错误返回给页面显示，或者对一些HTTP错误显示个性化的页面（_例如人民群众喜闻乐见的404页面_），所以在业务逻辑、数据操作这些层一般应该逐级向上抛异常，然后在Controller里捕获并加工成页面可识别的格式（_例如JSON_）。这样做的缺点是Controller里每个Action都包含重复的try...catch块。

异常的自动处理允许开发者指定自定义的异常处理逻辑，将异常处理与普通逻辑解耦，这样每个Action只需实现它所关注的功能即可。

**过滤器**

过滤器允许面向切面编程，是将横向逻辑与纵向逻辑解耦的重要工具。Flamework要实现针对Controller和Action两个级别的过滤器链，过滤器可在该级别逻辑前后执行，并能停止该级别逻辑及后续过滤器的执行。

**懒加载**

对尽可能多的资源实现懒加载，例如数据库连接、类、组件等，目的是提高效率、节约资源。

**参数绑定**

自动将请求中的参数与Action方法的参数绑定，从而避免在Action里出现通过$\_POST、$\_GET这些数组取参数的脏代码，也可以自动实现参数的校验和错误处理。

**ActiveRecord**

ORM是对关系模型和对象模型的阻抗不匹配问题的解决方案，ActiveRecord是目前最流行的一种ORM的实现方式。通过AR，可以以更对象化的方式操作关系数据库的数据。

**依赖注入**

依赖注入是个很好的解耦方法，也可以很优雅地实现懒加载。

目录
----

  1. [零：代码规范](/post/flamework-code-spec)
  - [一：框架入口与类的自动加载](/post/flamework-entry)
  - [二：错误和异常的自动处理](/post/flamework-error-auto-handling)
  - [三：实现Controller和Filter](/post/flamework-controller-and-filter)
  - [四：视图的模板与渲染](/post/flamework-view-rendering)
  - [五：ORM与ActiveRecord](/post/flamework-active-record)
  - [六：依赖注入](/post/flamework-dependency-injection)
  - [∝：烈焰之终章](/post/flamework-summary)
