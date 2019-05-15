---
layout: post
title: "PHP扩展框架的创建"
date: 2015-06-18 14:15
comments: true
categories: 计算机
tags:
- PHP
- 源码
---

## 创建项目

在PHP源码目录下的ext目录下执行：

{% codeblock lang:c %}
./ext_skel --extname=foobar
{% endcodeblock %}

修改foobar/config.m4，移除以下三行前的dnl：

>dnl PHP_ARG_WITH(foobar, for foobar support,  
>dnl Make sure that the comment is aligned:  
>dnl [  --with-foobar             Include foobar support])

## 编译与安装

在foobar目录下执行以下命令，生成configure脚本：

{% codeblock lang:bash %}
/usr/bin/phpize
{% endcodeblock %}

执行configure：

{% codeblock lang:bash %}
./configure --with-php-config=/usr/bin/php-config
{% endcodeblock %}

编译安装：

{% codeblock lang:bash %}
sudo make install
{% endcodeblock %}

修改php.ini，启用扩展：

{% codeblock lang:ini %}
extension=foobar.so
{% endcodeblock %}
