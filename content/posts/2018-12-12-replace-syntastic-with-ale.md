---
title: 用ALE替换了Syntastic
slug: replace syntastic with ale
date: 2018-12-12 20:42:10
tags:
- Vim
- 最佳实践
- 计算机
---
习惯频繁地保存源码，但是每次保存都有明显的卡顿，于是用Vim自己的调优功能跟踪了一下。

<!--more-->

依次执行以下命令：

```
:profile start profile.log
:profile file *
:profile func *
```

然后执行保存操作，再执行以下命令：

```
:profile pause
:noautocmd qall!
```

在当前目录下生成的文件profile.log里，看到执行时间最长的是Syntastic的函数，禁用这个扩展后保存果然比原来快多了。

但是Syntastic是个很有用的扩展，所以Google一下有没有异步执行的方法，偶然发现了ALE，主要特性就是异步执行。试用之后觉得很好用，就把前者卸了。

ALE的初始配置如下：

```vim
" ALE
let g:ale_sign_column_always = 1
let g:ale_set_highlights = 0
let g:airline#extensions#ale#enabled = 1
"自定义error和warning图标
let g:ale_sign_error = '✗'
let g:ale_sign_warning = '⚠'
" 显示Linter名称,出错或警告等相关信息
let g:ale_echo_msg_error_str = 'E'
let g:ale_echo_msg_warning_str = 'W'
let g:ale_echo_msg_format = '[%linter%] %s [%severity%]'
" PHP
let g:ale_php_phpcs_standard = 'PSR2'
let g:ale_php_phpmd_ruleset = '~/.phpmd.xml'
" 普通模式下，sp前往上一个错误或警告，sn前往下一个错误或警告
nmap sp <Plug>(ale_previous_wrap)
nmap sn <Plug>(ale_next_wrap)
" 触发/关闭语法检查
nmap <Leader>at :ALEToggle<CR>
" 查看错误或警告的详细信息
nmap <Leader>ad :ALEDetail<CR>
```
