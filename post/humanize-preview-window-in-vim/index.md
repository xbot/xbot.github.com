# 人性化Vim的预览窗口

<p>Vim的预览窗口由<code>:ptag</code>等命令触发，可用于预览函数定义。但在缺省情况下，预览窗口的高度是固定的，对于注释信息较多的函数，往往不能完全显示注释，而对于注释较少的函数，又会有多余的行浪费屏幕空间。</p>

<p>下面的函数用于解决这个问题：</p>

```vim
" 调用:ptag命令预览光标所在<word>的定义
" 对类C语言风格的函数/方法定义和注释有效，对其余情况仍沿用:ptag的原有效果
function! PTagIt()
    exec "ptag ".expand("<cword>")
    let cwin = winnr()
    silent! wincmd P
    let lnr = line('.')
    if lnr < 3
        return
    endif

    if foldlevel('.')>0
        normal zo
    endif

    let chead = 0
    let linestr = getline(lnr-1)
    if linestr =~ '^\s*\*/'
        let ptr = lnr-2
        while ptr>0
            let linestr = getline(ptr)
            if linestr =~ '^\s*/\*'
                let chead = ptr
                break
            endif
            let ptr = ptr-1
        endwhile
    endif

    if chead>0
        exec 'resize '.(lnr-chead+1)
        exec 'normal '.chead."z\<CR>"
        exec 'normal '.lnr.'G'
    endif

    exec cwin.'wincmd w'  
endfunction
nmap <leader>pp :call PTagIt()<CR>
nmap <leader>pc :pclose<CR>
```

<p>例如对于如下的函数定义：</p>

```php
/*
 * 我是一个测试函数
 * @param int 我是整型形参
 * @param string 我是字符串形参
 * @param array 我是返回值
 */
function foo($bar1, $bar2) {...}
```

<p>实际效果为打开的预览窗口将完全显示并只显示以上内容。</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

