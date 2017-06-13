---
layout: post
title: "FastFold：Vim折疊功能的救贖"
date: 2016-02-24 11:06
comments: true
categories: 計算機
tags:
- vim
- 插件
---

設置Vim的折疊規則爲syntax存在兩個問題。一是如果源碼中有大量折疊區域，在插入模式中輸入會變得很卡。二是剛輸入一個折疊區域的起始符号，後面所有的折疊都會被打開。

第一個問題是因爲Vim的syntax折疊規則處理過于低效。而後一個問題對于所有自動折疊規則都會存在，原因是Vim對折疊的更新過早。

傳統的解決辦法是将折疊規則置爲manual，并在合适的時機重置爲syntax或其它相應規則。但是工作繁複而且往往問題很多。

[FastFold](https://github.com/Konfekt/FastFold)是遵循上面所說的方法解決這些問題的插件，不過默認會在所有與折疊相關的時機更新折疊，會導緻相關的操作變慢，例如zc和zo時都會有明顯的卡頓。鑒于其它時機對折疊的更新已經足夠及時，可以通過配置取消受在到明顯影響的時機更新折疊：

{% codeblock lang:vim %}
" FastFold隻在za/zA/zx/zX時更新折疊信息
let g:fastfold_fold_command_suffixes =  ['x','X','a','A']
{% endcodeblock %}
