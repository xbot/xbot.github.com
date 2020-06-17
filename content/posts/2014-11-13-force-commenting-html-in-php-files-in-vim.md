---
layout: post
title: "Vim强制在PHP中使用HTML注释的方法"
date: 2014-11-13 21:45:00
comments: true
categories:
- 计算机
tags:
- vim
- php
- html
---

对PHP页面模板中的HTML做注释，NERDCommenter是根据文件类型处理的，所以必须临时转换文件类型：

```vim
" 强制使用HTML的注释
function! ForceHTMLComment(mode, type) range
    set ft=html
    if a:mode == "x"
        execute a:firstline.",".a:lastline."call NERDComment(\"x\", \"".a:type."\")"
    else
        if a:type == "Sexy"
            normal ,cs
        else
            normal ,cc
        endif
    endif
    set ft=php
endfunction
au FileType php nmap <buffer> <leader>fhcc :call ForceHTMLComment("n", "Comment")<CR>
au FileType php vmap <buffer> <leader>fhcc :call ForceHTMLComment("x", "Comment")<CR>
au FileType php nmap <buffer> <leader>fhcs :call ForceHTMLComment("n", "Sexy")<CR>
au FileType php vmap <buffer> <leader>fhcs :call ForceHTMLComment("x", "Sexy")<CR>
au FileType php nmap <buffer> <leader>fhcu :call ForceHTMLComment("n", "Uncomment")<CR>
au FileType php vmap <buffer> <leader>fhcu :call ForceHTMLComment("x", "Uncomment")<CR>
```

有日子没写vimscript了，手都生了。
