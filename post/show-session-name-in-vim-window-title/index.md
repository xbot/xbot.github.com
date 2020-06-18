# 在Vim窗口标题中显示会话名


同时处理多个项目时，在窗口标题中显示会话名，方便区分，需用[session][1]管理会话。

```vim
" custom the window title
fun! MyTitleString()
    let sessionName = xolox#session#find_current_session()
    let sessionStr = ''
    if len(sessionName)>0
        let sessionStr = ' ['.sessionName.'] '
    endif
    return 'VIM'.sessionStr.': %-25.55F %a%r%m'
endfun
au BufEnter * let &titlestring=MyTitleString()
set titlelen=70
```


  [1]: https://github.com/xolox/vim-session

