---
title: 怎样在Vim中删除引号文本对象
slug: how to delete quoted text object in vim
date: 2017-02-25 19:33:18
categories:
- 计算机
tags:
- vim
- 笔记
---

假设有这样一段代码（光标在「|」位置）：

> $title = "Article:|" . $realTitle;

要删除引号及其内容，并进入插入模式准备输入其它内容。

如果执行`ca"`，会得到如下的结果：

> $title = |. $realTitle;

连引号旁边的空格也删除了。

如果不想删除空格，可以用`c2i"`。

参考：[Is it possible to exclude surrounding whitespace from the word-object `a"`?](https://www.reddit.com/r/vim/comments/5v4gm5/is_it_possible_to_exclude_surrounding_whitespace/)
