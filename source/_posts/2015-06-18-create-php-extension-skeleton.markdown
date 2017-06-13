---
layout: post
title: "PHP擴展框架的創建"
date: 2015-06-18 14:15
comments: true
categories: 計算機
tags:
- PHP
- 源碼
---

## 創建項目

在PHP源碼目錄下的ext目錄下執行：

{% codeblock lang:c %}
./ext_skel --extname=foobar
{% endcodeblock %}

修改foobar/config.m4，移除以下三行前的dnl：

>dnl PHP_ARG_WITH(foobar, for foobar support,  
>dnl Make sure that the comment is aligned:  
>dnl [  --with-foobar             Include foobar support])

## 編譯與安裝

在foobar目錄下執行以下命令，生成configure腳本：

{% codeblock lang:bash %}
/usr/bin/phpize
{% endcodeblock %}

執行configure：

{% codeblock lang:bash %}
./configure --with-php-config=/usr/bin/php-config
{% endcodeblock %}

編譯安裝：

{% codeblock lang:bash %}
sudo make install
{% endcodeblock %}

修改php.ini，啟用擴展：

{% codeblock lang:ini %}
extension=foobar.so
{% endcodeblock %}
