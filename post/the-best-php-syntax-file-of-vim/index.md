# Vim最好的PHP语法高亮插件

[StanAngeloff/php.vim](https://github.com/StanAngeloff/php.vim)应该是目前最新、最全的PHP语法高亮插件了，它解决了旧版本无法高亮`@throws`的问题。

默认会把方法注释全部当做普通注释显示，也就是没有高亮，需要专门做配置：

```vim
function! PhpSyntaxOverride()
    hi! def link phpDocTags  phpDefine
    hi! def link phpDocParam phpType
endfunction

augroup phpSyntaxOverride
    autocmd!
    autocmd FileType php call PhpSyntaxOverride()
augroup END
```


