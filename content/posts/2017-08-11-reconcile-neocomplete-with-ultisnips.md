---
title: 使neocomplete和ultisnips共用tab键的方法
slug: reconcile neocomplete with ultisnips
date: 2017-08-11 14:43:14
categories:
- 计算机
tags:
- vim
- 最佳实践
---

昨天用vim-clevertab换掉了supertab，但是实际使用中总是出莫名其妙的问题。看了一下插件源码，使用全局变量判断状态，这是个很不靠谱的方案，所以还是卸掉了。

下面的配置可以很好的解决问题：

```vim
let g:UltiSnipsExpandTrigger="<c-tab>"
let g:UltiSnipsJumpForwardTrigger="<c-tab>"
let g:UltiSnipsJumpBackwardTrigger="<s-tab>"
let g:UltiSnipsSnippetsDir='~/.vim/UltiSnips'
let g:ulti_expand_or_jump_res = 0
function! CleverTab()"{{{
    call UltiSnips#ExpandSnippetOrJump()
    if g:ulti_expand_or_jump_res
        return ""
    else
        if pumvisible()
            return "\<c-n>"
        else
            return neocomplete#start_manual_complete()
        endif
    endif
endfunction"}}}
inoremap <silent> <tab> <c-r>=CleverTab()<cr>
snoremap <silent> <tab> <esc>:call UltiSnips#ExpandSnippetOrJump()<cr>
```

ultisnips没有禁用按键映射的开关，而默认的映射会干扰上述配置，所以这里把默认的映射改成了`<c-tab>`。

