# Vim搜索的最佳实践


## 目标

* 使用PCRE正则表达式在当前目录下递归搜索
* 将选定内容自动转换成正则表达式
* 有较高的搜索速度

## 依赖

* [Ferret](https://github.com/wincent/ferret)：实现用PCRE正则表达式递归搜索
* [The Silver Searcher](https://github.com/ggreer/the_silver_searcher)：搜索速度快

## 配置

```vim
" 在当前缓冲区中查找与替换
nmap <leader>ff yiw/\<<C-R>"\>\C
vmap <leader>ff y/<C-R>=XEscapeRegex(@")<CR>\C
nmap <leader>rr yiw:%s/\<<C-R>"\>\C//g<LEFT><LEFT>
vmap <leader>rr y:%s/<C-R>=XEscapeRegex(@")<CR>\C//g<LEFT><LEFT>
nmap <leader>rl yiw:s/\<<C-R>"\>\C//g<LEFT><LEFT>
vmap <leader>rl y:s/<C-R>=XEscapeRegex(@")<CR>\C//g<LEFT><LEFT>

" 在当前目录下递归搜索和替换
let g:FerretExecutable='ag'

nmap <leader>ak <Plug>(FerretAck)
nmap <leader>lak <Plug>(FerretLack)
nmap <leader>aw <Plug>(FerretAckWord)
nmap <leader>as <Plug>(FerretAcks)
vmap <leader>ak y:Ack <C-R>=XEscapeRegex(@", 2)<CR>
vmap <leader>lak y:Lack <C-R>=XEscapeRegex(@", 2)<CR>

nnoremap <leader>a :set operatorfunc=GrepOperator<CR>g@
vnoremap <leader>a :<c-u>call GrepOperator(visualmode())<CR>
function! GrepOperator(type)"{{{
    if a:type ==# 'v'
        normal! `<v`>y
    elseif a:type ==# 'char'
        normal! `[v`]y
    else
        return
    endif

    exec "Ack ".XEscapeRegex(@@, 2)
    " exec "Grep ".XEscapeRegex(@@, 2)
endfunction"}}}

" 转义正则表达式特殊字符，以便在正则表达式中使用
" a:1   是否转义为vimgrep的pattern格式，1，2
" a:2   是否用shellescape()转义，1是转义，2是转义并去掉两侧单引号
function! XEscapeRegex(str, ...)"{{{
    let pattern = a:str
    let pattern = escape(pattern, '/\.*$^~[]"')

    if a:0 && a:1
        let pattern = escape(pattern, '()+?')
        if a:1 == 2
            let pattern = escape(pattern, '\')
        endif
    endif

    if a:0 > 1 && a:2
        let pattern = shellescape(pattern)
        if a:2 == 2
            let pattern = pattern[1:-2]
        endif
    endif

    let whitespacePattern = a:0 && a:1 ? '\\s\+' : '\\s\\+'
    let pattern = substitute(pattern, '\s\+', whitespacePattern, 'g')

    return pattern
endfunction"}}}
```

### Ferret

Ferret是我用过的vim搜索扩展里最接近理想的一个，最大的优点是不用引号包裹搜索内容，且较大程度地支持PCRE正则表达式。

EasyGrep的缺点是正则表达式必须是shellescape()过的，手写不方便，也不直观。Ferret在底层做了shellescape()，所以比EasyGrep简单一些。不过Ferret对反斜杠的处理仍然不直观，例如搜索`App\Link`，正则表达式是`App\\Link`，而Ferret里只能用`App\\\\Link`。原因是从输入到执行，存在著三层转义：Vim命令行、Shell、grep/ag。所以对于EasyGrep，要搜索一个`\`，必须输入`\\\\\\\\`。对于Ferret，由于底层做了shellescape()，只需要两层转义，即`\\\\`。当然这样也不方便，所以我用自定义函数XEscapeRegex()对选择的内容做这个事，不过最完美的方法当然是Ferret自己支持，或者自己再封装一下Ferret的命令，这样输入的正则表达式可读性就正常了。

### The Silver Searcher

ag是我用过的搜索工具里对速度和功能兼顾得最好的。

### 搜索运算符

GrepOperator是从[Learn Vimscript the Hard Way](http://learnvimscriptthehardway.stevelosh.com/)里扒出来的，可以实现自动搜索指定范围的内容：

* `<leader>aiw`：搜索光标下的词
* `<leader>a$`：搜索从光标到行末的内容
* `<leader>at;`：搜索从光标到下一个分号的内容

### 转换选定内容到正则表达式

XEscapeRegex()根据参数转换字符串到不同格式的正则表达式。主要用途为转换成给搜索当前缓冲区的Vim格式的正则，或给Ferret使用的PCRE格式。

