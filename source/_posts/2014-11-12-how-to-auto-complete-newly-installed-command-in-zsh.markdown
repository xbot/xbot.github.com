---
layout: post
title: "ZSH下新安裝的程序無法自動補全的解決方法"
date: 2014-11-12 22:15
comments: true
categories: 計算機
tags:
- zsh
- linux
---

Zsh默認開啟了對PATH變量的緩存，這是導致新安裝的程序無法立即使用自動補全的原因。

其實只要PATH變量不太複雜，安裝的程序不太多，完全沒必要開啟緩存，實際上我把緩存關掉後完全沒有感覺到補全的速度有什麼變化。

方法如下，在.zshrc中增加一行：

{% codeblock lang:bash %}
zstyle ':completion:*' rehash true
{% endcodeblock %}

也可以在必要的時間手工執行命令**rehash**，也是個臨時解決方法。
