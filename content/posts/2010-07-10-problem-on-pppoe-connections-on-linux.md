---
layout: post
title: Linux下pppoe连接建立后仍不能上网的问题
date: 2010-07-10 00:00:00
tags:
- ADSL
- Archlinux
- BASH
- 计算机
- 配置
status: publish
published: true
comments: true
meta:
  _edit_last: '1'
  views: '1346'
  _wp_old_slug: ''
---
<strong>问题描述</strong>

pppoe可以连接，ifconfig可以看到ppp0接口，一切正常，只是不能上网。

<strong>解决办法</strong>

使用如下命令查看路由表：

{% codeblock lang:bash %}
route
{% endcodeblock %}

正常情况下返回的结果中应该有如下内容：

<blockquote>
default  *  0.0.0.0   U   0   0  0 ppp0
</blockquote>

如果没有，可手工添加：

{% codeblock lang:bash %}
route add default dev ppp0
{% endcodeblock %}

如果可以上网，就成功了。

使用如下脚本在pppoe连接成功时自动添加路由表项：

{% codeblock lang:bash %}
#!/bin/sh
 
if ifconfig ppp0  > /dev/null 2>&1 ; then
    route del default
    route add default dev ppp0
fi
{% endcodeblock %}

将上述内容保存成名为<strong>01-route.sh</strong>的文件，权限<strong>755</strong>，放到<strong>/etc/ppp/ip-up.d</strong>目录下。

然后创建以下两个脚本：

{% codeblock lang:bash %}
#!/bin/bash
 
if ifconfig ppp0  > /dev/null 2>&1 ; then
    echo 'PPPOE has already been switched on !'
    exit 0
fi
 
off
 
sudo pon
sleep 5
sudo /etc/ppp/ip-up
{% endcodeblock %}

{% codeblock lang:bash %}
#!/bin/bash
 
if ! ifconfig ppp0  > /dev/null 2>&1 ; then
    echo 'PPPOE has already been switched off !'
    exit 0
fi
 
sudo poff
sudo /etc/ppp/ip-down
{% endcodeblock %}

分别命名为<strong>on</strong>和<strong>off</strong>并复制到环境变量PATH下，以后即可使用这两个脚本建立和注销pppoe连接。

<strong>原因分析</strong>

问题的原因是pppoe连接建立时，没有自动往路由表中添加相关路由项。

一般情况下，如果使用的是<strong>ppp</strong>做pppoe连接，其配置文件中默认会开启<strong>defaultroute</strong>选项，该选项的作用就是在pppoe连接建立时自动添加路由表项，但在路由表中已经存在<strong>default</strong>项时，该操作将不会进行。

一般有两种情况会使路由表中在pppoe连接建立前就存在default项。一种情况是网络接口配置中设置了route，如在archlinux下，就是rc.conf文件中的gateway项。如果使用的是静态地址，则需要做这种配置，但对于大多数使用DHCP动态分配IP地址的情况，这项配置则不需要。另一种情况就是ADSL Modem自带路由功能，在分配IP给计算机时也会自动添加一条路由表项。
