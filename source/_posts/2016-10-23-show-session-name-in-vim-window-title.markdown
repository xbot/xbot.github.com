---
layout: post
title: "在Vim窗口标題中顯示會話名"
date: 2016-10-23 11:52
comments: true
categories: 計算機
tags:
- Vim
---

同時處理多個項目時，在窗口标題中顯示會話名，方便區分，需用[session][1]管理會話。

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
