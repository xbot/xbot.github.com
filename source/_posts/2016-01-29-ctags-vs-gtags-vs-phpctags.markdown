---
layout: post
title: "ctags vs gtags vs phpctags"
date: 2016-01-29 17:55
comments: true
categories: 計算機
tags:
- vim
- 插件
---

這兩天把源碼分析工具從ctags向gtags遷移，另外嘗試了一下phpctags。

之前用ctags有兩個問題，一個是如果同一tag有多個定義，按Ctrl+]隻能跳轉到第一處。另一個是用cscope查詢tag的調用記錄時，有些tag查不到結果。

幾年前接觸過gtags，因爲當時還不支持宇宙最好語言，所以沒用。這次測試了一下，前面兩個ctags的問題都可以解決。

安裝後需要手工拷貝gtags.vim和gtags-cscope.vim到vim的plugin目錄，然後配置如下：

{% codeblock lang:vim %}
let Gtags_Close_When_Single = 1
let Gtags_Auto_Update = 0
let g:cscope_silent = 1
au FileType php,python,c,cpp,javascript,go map <C-]> :Gtags<CR><CR>
au FileType php,python,c,cpp,javascript,go map <C-[> :Gtags -r<CR><CR>
nnoremap <leader><C-]> :execute 'Unite gtags/def:'.expand('<cword>')<CR>
nnoremap <leader><C-[> :execute 'Unite gtags/ref:'.expand('<cword>')<CR>
{% endcodeblock %}

其它選項參見前面兩個文件裏的注釋。

另外嘗試了一下phpctags，據說和ctags兼容并對php做了優化。實際使用效果和ctags沒發現有什麽區别，不過tagbar-phpctags倒是比tagbar用ctags時效果更好。
