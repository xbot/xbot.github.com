---
layout: post
title: Linux下使用freetds连接SQL Server
date: 2010-06-20 00:00:00
tags:
- cygwin
- 安装
- 客户端
- 数据库
- 编译
- 计算机
status: publish
published: true
comments: true
meta:
  _edit_last: '1'
  views: '936'
  _wp_old_slug: ''
---
<a href="http://www.freetds.org">freetds</a>是第三方、开源的数据库客户端和连接库，支持Sybase和Microsoft SQL Server，它最大的好处是允许非Windows平台的程序连接SQL Server。

freetds的编译和安装命令如下：

{% codeblock lang:bash %}
./configure --with-tdsver=8.0 --enable-msdblib
make
make install
{% endcodeblock %}

configure的这两个参数是必不可少的，否则可能无法连接SQL Server。

安装后修改<strong>freetds.conf</strong>（<em>一般应该在/usr/local/etc/freetds.conf</em>），添加如下内容：

<blockquote>
[mysvr]
        host = 10.1.29.40
        port = 1433
        tds version = 8.0
        client charset = utf-8
</blockquote>

上述参数应根据实际情况修改。

最后使用如下命令测试连接：

{% codeblock lang:bash %}
tsql -S mysvr -U sa -P 123456
{% endcodeblock %}

给公司产品做Linux下的安装包，把开发环境放在了Cygwin中，非常享受在全屏、半透明的mintty下敲命令。今天发现在Cygwin下也是可以编译安装Linux下的程序的，只是效率非常低下。故上述内容对Cygwin环境同样成立。
