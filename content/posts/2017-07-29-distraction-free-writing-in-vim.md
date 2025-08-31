---
title: 利用goyo.vim专注写作
slug: distraction free writing in vim
date: 2017-07-29 16:48:45
tags:
- Vim
- 最佳实践
- 计算机
---

goyo.vim是给vim提供专注写作模式的插件，配合markdown效果不错：

![](https://wx4.sinaimg.cn/large/006tNbRwly1fwvwwvhfjlj31400p0q6b.jpg)

最好在单独的vim实例中使用，已发现和vim-workspace配合不好的情况。

对markdown文件自动开启goyo的配置如下：

```vim
function! s:auto_goyo()
    if &ft == 'markdown'
        Goyo 80
    else
        let bufnr = bufnr('%')
        Goyo!
        execute 'b '.bufnr
    endif
endfunction

augroup goyo_markdown
    autocmd!
    autocmd BufNewFile,BufRead * call s:auto_goyo()
augroup END
```
