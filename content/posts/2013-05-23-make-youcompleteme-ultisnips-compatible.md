---
layout: post
title: How to Make YouCompleteMe Compatible with UltiSnips
slug: make youcompleteme ultisnips compatible
date: 2013-05-23 11:26:00
comments: true
tags:
- Vim
- 计算机
---
I replaced [NeoComplCache][] with [YouCompleteMe][] today. The main reason is for the auto-complete feature and the fast speed. Besides, it seems that YCM provides more features than NCC.

Although NCC can also do auto-completions, it was so slow that I had to turn it off. For a long time, I have been doing completions by triggering the TAB key manually. YCM is much faster in most conditions, but I found it is also slow for C source files. Moreover, with the power of [jedi][], YCM makes completions of python sources much wiser, which is very convenient.

Then a problem raised again. It's the confiliction of mappings for Tab key between YCM and [UltiSnips][]. Many people changed the default mapping of either YCM or UltiSnips, but I cannot tolerate that. I insist that the most convenient way is UltiSnips having a higher priority above YCM, which means, when Tab is triggered, UltiSnips expands the snippet, if there is not a valid snippet, YCM will take over the job.

Fortunately, there is a solution. The idea is the same with [my former post about NCC][post1]. I made it by the help of [SuperTab][].

I have to say that UltiSnips is a well-written script, it passes Tab key through if there is not a snippet. Then SuperTab will be triggered. Since the default action of SuperTab is customizable, so I can set it to the key-binding of YCM. That is the whole trick.

First, change the default key-binding of YCM to \<C-TAB\> and \<C-S-TAB\>:

>let g:ycm_key_list_select_completion = ['\<C-TAB\>', '\<Down\>']  
>let g:ycm_key_list_previous_completion = ['\<C-S-TAB\>', '\<Up\>']

Then set the default action of SuperTab to triggering \<C-TAB\>:

>let g:SuperTabDefaultCompletionType = '\<C-Tab\>'

OK, job done.

### 相关阅读：

* [用neocomplete换掉了YouCompleteMe](/post/replace-youcompleteme-with-neocomplete/)
* [Vim的终极自动补全插件：NeoComplCache](/post/neocomplcache-vim/)

[NeoComplCache]: https://github.com/Shougo/neocomplcache
[YouCompleteMe]: https://github.com/Valloric/YouCompleteMe
[jedi]: https://github.com/davidhalter/jedi
[post1]: /post/neocomplcache-vim/
[SuperTab]: https://github.com/ervandew/supertab
[UltiSnips]: https://github.com/SirVer/ultisnips


