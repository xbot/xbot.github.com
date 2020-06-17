---
layout: post
title: "CtrlP vs Unite vs Command-T vs FuzzyFinder"
date: 2016-01-27 16:16:00
comments: true
categories:
- 计算机
tags:
- vim
- 最佳实践
---

简述
----

Sublime在文件打开速度上的表现很惊艳，Vim有几个扩展可以实现类似功能，本文对这些扩展做个对比。

虽然每个扩展都或多或少地实现了一些功能，我个人用得最多的也就文件、Tag、Buffer Tag的模糊查找这三个，像MRU、buffer之类的查找不觉得有多大用处，所以提高工作效率的工具一定是在热度环节有很好表现，其余偏门功能可以交给插件实现，在这点上CtrlP和Unite做得很好。所以下面针对最常用的这三个功能做下对比，更多功能的对比见下表：

{% img http://pic.yupoo.com/leninlee/FhDLWLYr/medish.jpg %}

文件模糊查找
------------

Command-T的文件索引速度最快，各方面在这几个扩展中的表现都是最好的。

CtrlP默认使用VIM自建的globpath()，需要手工配置使用[ag](https://github.com/ggreer/the_silver_searcher)以获取更快的速度，据说使用find命令效率更高，实际感觉和ag差别不大。

Unite也需要手工设置使用ag，而且可以实现异步索引，在这一点的体验上好于前两个，但是在模糊匹配的排序上表现不好，比如输入“php”，当然希望“/etc/php.ini”这样的结果排在“/home/peter/host/tmp.txt”前面，实际往往不是这样，Unite的几个sorter里，数rank的排序结果最接近于这个要求，但是表现仍然不如前两个。

Tag模糊查找
-----------

三个扩展都是通过ctags实现，虽然Unite的排序问题仍然存在，对于Tag这么简短的东西，问题不大。

Buffer Tag模糊查找
------------------

CtrlP的表现是最好的。

Unite需要通过unite-outline这个source实现，但是只能列出类和方法，无法定制列举类型。而且默认选中排在第一位的类名，查询方法时很不方便。

Command-T不支持此功能。

我的选择
--------

FuzzyFinder是该类扩展里的老前辈，不过早已不维护，功能上不比其它几个出彩。Command-T虽然在文件索引速度上表现最好，但是优势并不很明显，而且支持功能最少。所以排除这两个。

在最常用的三个功能上，CtrlP的综合表现是最均衡的，所以留下它。Unite本身是一种统一的交互方式和界面，很多扩展（source）可以实现五花八门的功能，像unite-outline虽然在Buffer Tag上表现不佳，对于Markdown和Vim文档还是很有用的。

Update: 2016-02-01
------------------

[FZF: 又一个文件模糊查询工具](/post/fzf-yet-another-fuzzy-finder/)
