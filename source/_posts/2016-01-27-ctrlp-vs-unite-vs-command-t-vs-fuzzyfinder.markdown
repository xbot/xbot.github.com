---
layout: post
title: "CtrlP vs Unite vs Command-T vs FuzzyFinder"
date: 2016-01-27 16:16
comments: true
categories: 計算機
tags:
- vim
- 插件
---

簡述
----

Sublime在文件打開速度上的表現很驚豔，Vim有幾個擴展可以實現類似功能，本文對這些擴展做個對比。

雖然每個擴展都或多或少地實現了一些功能，我個人用得最多的也就文件、Tag、Buffer Tag的模糊查找這三個，像MRU、buffer之類的查找不覺得有多大用處，所以提高工作效率的工具一定是在熱度環節有很好表現，其餘偏門功能可以交給插件實現，在這點上CtrlP和Unite做得很好。所以下面針對最常用的這三個功能做下對比，更多功能的對比見下表：

{% img http://pic.yupoo.com/leninlee/FhDLWLYr/medish.jpg %}

文件模糊查找
------------

Command-T的文件索引速度最快，各方面在這幾個擴展中的表現都是最好的。

CtrlP默認使用VIM自建的globpath()，需要手工配置使用[ag](https://github.com/ggreer/the_silver_searcher)以獲取更快的速度，據說使用find命令效率更高，實際感覺和ag差别不大。

Unite也需要手工設置使用ag，而且可以實現異步索引，在這一點的體驗上好于前兩個，但是在模糊匹配的排序上表現不好，比如輸入“php”，當然希望“/etc/php.ini”這樣的結果排在“/home/peter/host/tmp.txt”前面，實際往往不是這樣，Unite的幾個sorter裏，數rank的排序結果最接近于這個要求，但是表現仍然不如前兩個。

Tag模糊查找
-----------

三個擴展都是通過ctags實現，雖然Unite的排序問題仍然存在，對于Tag這麽簡短的東西，問題不大。

Buffer Tag模糊查找
------------------

CtrlP的表現是最好的。

Unite需要通過unite-outline這個source實現，但是隻能列出類和方法，無法定制列舉類型。而且默認選中排在第一位的類名，查詢方法時很不方便。

Command-T不支持此功能。

我的選擇
--------

FuzzyFinder是該類擴展裏的老前輩，不過早已不維護，功能上不比其它幾個出彩。Command-T雖然在文件索引速度上表現最好，但是優勢并不很明顯，而且支持功能最少。所以排除這兩個。

在最常用的三個功能上，CtrlP的綜合表現是最均衡的，所以留下它。Unite本身是一種統一的交互方式和界面，很多擴展（source）可以實現五花八門的功能，像unite-outline雖然在Buffer Tag上表現不佳，對于Markdown和Vim文檔還是很有用的。

Update: 2016-02-01
------------------

[FZF: 又一個文件模糊查詢工具](/post/fzf-yet-another-fuzzy-finder/)
