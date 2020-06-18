# 使用热键切换Vim的QuickFix窗口打开状态

QuickFix窗口只有打开和关闭的命令，而使用一个热键来切换打开状态可以减少热键资源的浪费，使用也更方便。

从<a href="http://vim.wikia.com/wiki/Toggle_to_open_or_close_the_quickfix_window">这里</a>找到使用自定义函数完成此功能的方法：使用一个全局变量记录QuickFix窗口的打开状态，从而判断是应该关闭还是打开。但其提供的函数存在状态同步的问题，假如在QuickFix窗口中使用“:q”退出窗口，此方法将不能正常工作。评论中给出了很好的解决方法，但其提供的代码有些小问题，修改之后如下：

```vim
nmap <leader>co :QFix<CR>
nmap <leader>ct :call QFixToggle(1)<CR>
command! -bang -nargs=? QFix call QFixToggle(<bang>0)

function! QFixToggle(forced)
    if exists("g:qfix_win") && a:forced != 0
        cclose
    else
        if exists("g:my_quickfix_win_height")
            execute "copen ".g:my_quickfix_win_height
        else
            copen
        endif
    endif
endfunction

augroup QFixToggle
    autocmd!
    autocmd BufWinEnter quickfix let g:qfix_win = bufnr("$")
    autocmd BufWinLeave * if exists("g:qfix_win") && expand("<abuf>") == g:qfix_win | unlet! g:qfix_win | endif
augroup END
```

