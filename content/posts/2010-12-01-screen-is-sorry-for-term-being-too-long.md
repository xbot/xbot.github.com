---
layout: post
title: screen曰：$TERM too long - sorry.
date: 2010-12-01 00:00:00
tags:
- BASH
- Linux
- rxvt
- screen
- 终端
- 计算机
status: publish
published: true
comments: true
meta:
  _edit_last: '1'
  views: '816'
  _wp_old_slug: ''
---
最近rxvt-unicode-256color的一次更新触发了screen的一个缺陷。新的版本将默认的环境变量$TERM由rxvt-256color改成了rxvt-unicode-256color，这导致在rxvt中启动screen时报这样的错误：

<blockquote>
$TERM too long - sorry.
</blockquote>

虽然可以在.Xdefaults中指定$TERM的值，但是<a href="https://aur.archlinux.org/packages.php?ID=13060">据说</a>这样会导致rxvt加载错误的terminfo，并导致终端的颜色由256色降低到88色。另外虽然按照同样的说法，可以在screenrc中设置$TERM的值，但是不知道是我写的格式不对还是什么原因，无效。

最终还是设了个别名：

{% codeblock lang:bash %}
# vi ~/.bashrc

alias screenx='export TERM=screen-256color && screen'
{% endcodeblock %}
