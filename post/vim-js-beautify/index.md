# 在Vim中格式化Javascript、HTML和CSS


## 依赖

* [vim-jsbeautify](https://github.com/maksimr/vim-jsbeautify)
* [js-beautify](https://github.com/beautify-web/js-beautify)

js-beautify的安装：

```bash
npm -g install js-beautify
# 或
pip install jsbeautifier
```

## 配置快捷键

```vim
vmap <leader>jsb :'<,'>!js-beautify -i<CR>
autocmd FileType javascript noremap <buffer>  <leader>jsb :call JsBeautify()<CR>
autocmd FileType html noremap <buffer> <leader>htmlb :call HtmlBeautify()<CR>
autocmd FileType css noremap <buffer> <leader>cssb :call CSSBeautify()<CR>
```

