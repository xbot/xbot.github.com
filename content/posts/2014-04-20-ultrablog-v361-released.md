---
layout: post
title: "UltraBlog.vim v3.6.1: 文章分类自动补全"
date: 2014-04-20 22:50:00
comments: true
categories:
- 计算机
tags:
- Plugin
- Python
- UltraBlog.vim
- Vim
- 博客
- 编程
---

春节期间收到两个issue，一直拖到今天才有时间完成。

增加了一个功能，在文章编辑视图的元数据中分类那行，使用热键\<C-X\>\<C-U\>自动完成文章的分类。在配置数组**ub_blog**中增加键值对“categories”，默认使用这一项的值实现自动完成，如下：

```vim
let ub_blog = {'login_name':'admin',
            \'password':'pass2011',
            \'url':'http://www.sample.com/',
            \'xmlrpc_uri':'xmlrpc.php',
            \'db':'$VIM/UltraBlog.db',
            \'categories': 'News|Computer|Image'
            \}
```

如果这一项不存在，将从博客中拉取分类数据。

自从迁移到Octopress，有两年不用Wordpress了，没想到居然还有人在用[UltraBlog.vim](/post/ultrablog-as-an-ultimate-vim-blogging-plugin/)。
