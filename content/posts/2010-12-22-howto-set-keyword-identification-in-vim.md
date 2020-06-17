---
layout: post
title: Vim中设置关键词识别规则的方法
date: 2010-12-22 00:00:00
tags:
- PHP
- Vim
- 计算机
- 配置
status: publish
published: true
comments: true
meta:
  _edit_last: '1'
  views: '667'
  _wp_old_slug: ''
---
在Vim中，关键词是一个至关重要的概念。合理地利用关键词，可以快速地对光标下有特殊含义的字符串进行一系列的操作，例如通过快捷键复制关键词、查询函数定义或文档、在项目中查询所有引用当前函数的地方等等。这对工作效率的提升有极大的帮助。

但我的Vim对PHP文件的关键词识别有问题，在选择函数名的时候总是将函数名后的小括弧一起选入。

Vim使用选项iskeyword设定关键词的匹配规则，使用如下命令查看当前文件类型的关键词规则：

{% codeblock lang:vim %}
set iskeyword
{% endcodeblock %}

我这里查看PHP文件的规则结果如下：

<blockquote>
iskeyword=@,48-57,_,192-255,$,(
</blockquote>

不明白为什么缺省设置中会有左括弧，在vimrc中加入如下配置取消之：

{% codeblock lang:vim %}
au FileType php set iskeyword-=(
{% endcodeblock %}
