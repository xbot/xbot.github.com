---
layout: post
title: "FastFold：Vim折叠功能的救赎"
date: 2016-02-24 11:06
comments: true
categories: 计算机
tags:
- vim
- 插件
---

设置Vim的折叠规则为syntax存在两个问题。一是如果源码中有大量折叠区域，在插入模式中输入会变得很卡。二是刚输入一个折叠区域的起始符号，后面所有的折叠都会被打开。

第一个问题是因为Vim的syntax折叠规则处理过于低效。而后一个问题对于所有自动折叠规则都会存在，原因是Vim对折叠的更新过早。

传统的解决办法是将折叠规则置为manual，并在合适的时机重置为syntax或其它相应规则。但是工作繁复而且往往问题很多。

[FastFold](https://github.com/Konfekt/FastFold)是遵循上面所说的方法解决这些问题的插件，不过默认会在所有与折叠相关的时机更新折叠，会导致相关的操作变慢，例如zc和zo时都会有明显的卡顿。鉴于其它时机对折叠的更新已经足够及时，可以通过配置取消受在到明显影响的时机更新折叠：

{% codeblock lang:vim %}
" FastFold只在za/zA/zx/zX时更新折叠信息
let g:fastfold_fold_command_suffixes =  ['x','X','a','A']
{% endcodeblock %}
