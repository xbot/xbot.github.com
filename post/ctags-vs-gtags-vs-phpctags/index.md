# ctags vs gtags vs phpctags


这两天把源码分析工具从ctags向gtags迁移，另外尝试了一下phpctags。

之前用ctags有两个问题，一个是如果同一tag有多个定义，按Ctrl+]只能跳转到第一处。另一个是用cscope查询tag的调用记录时，有些tag查不到结果。

几年前接触过gtags，因为当时还不支持宇宙最好语言，所以没用。这次测试了一下，前面两个ctags的问题都可以解决。

安装后需要手工拷贝gtags.vim和gtags-cscope.vim到vim的plugin目录，然后配置如下：

```vim
let Gtags_Close_When_Single = 1
let Gtags_Auto_Update = 0
let g:cscope_silent = 1
au FileType php,python,c,cpp,javascript,go map <C-]> :Gtags<CR><CR>
au FileType php,python,c,cpp,javascript,go map <C-[> :Gtags -r<CR><CR>
nnoremap <leader><C-]> :execute 'Unite gtags/def:'.expand('<cword>')<CR>
nnoremap <leader><C-[> :execute 'Unite gtags/ref:'.expand('<cword>')<CR>
```

其它选项参见前面两个文件里的注释。

另外尝试了一下phpctags，据说和ctags兼容并对php做了优化。实际使用效果和ctags没发现有什么区别，不过tagbar-phpctags倒是比tagbar用ctags时效果更好。

