---
layout: post
title: "UltraBlog.vim v3.6.1: 文章分類自動補全"
date: 2014-04-20 22:50
comments: true
categories: 計算機
tags:
- Plugin
- Python
- UltraBlog.vim
- Vim
- 博客
- 編程
---

春節期間收到兩個issue，一直拖到今天才有時間完成。

增加了一個功能，在文章編輯視圖的元數據中分類那行，使用熱鍵\<C-X\>\<C-U\>自動完成文章的分類。在配置數組**ub_blog**中增加鍵值對“categories”，默認使用這一項的值實現自動完成，如下：

{% codeblock lang:vim %}
let ub_blog = {'login_name':'admin',
            \'password':'pass2011',
            \'url':'http://www.sample.com/',
            \'xmlrpc_uri':'xmlrpc.php',
            \'db':'$VIM/UltraBlog.db',
            \'categories': 'News|Computer|Image'
            \}
{% endcodeblock %}

如果這一項不存在，將從博客中拉取分類數據。

自從遷移到Octopress，有兩年不用Wordpress了，沒想到居然還有人在用[UltraBlog.vim](/post/ultrablog-as-an-ultimate-vim-blogging-plugin/)。
