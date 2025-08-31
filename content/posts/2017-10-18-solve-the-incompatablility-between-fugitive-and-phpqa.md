---
title: 解决phpqa和fugitive不兼容的问题
slug: solve the incompatablility between fugitive and phpqa
date: 2017-10-18 11:05:12
tags:
- 计算机
- Vim
---
在`:Gstatus`中查看diff时，报错：

> Error detected while processing function Phpqa#PhpLint:  
line    8:  
E684: list index out of range: 0  
E116: Invalid arguments for function match(l:php_list[0],"No syntax errors") == -1  
E15: Invalid expression: 0 != v:shell_error && match(l:php_list[0],"No syntax errors") == -1

这是phpqa的bug，有人创建了PR，但作者没有合并，需要手工合并：

```bash
curl -L https://github.com/joonty/vim-phpqa/pull/43.patch | git am
```

