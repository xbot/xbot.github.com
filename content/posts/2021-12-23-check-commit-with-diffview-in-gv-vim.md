---
title: "在 gv.vim 中使用 Diffview.nvim 查看提交内容"
slug: "Check Commit With Diffview in Gv Vim"
date: 2021-12-23T14:42:03+08:00
categories:
- 计算机
tags:
- Vim
---
Vim 没有很好的 Git commits viewer ，普通的 diff 模式对于很小的 commit 还行，文件较多、改动较大的 commit 看起来很糟糕。

gv.vim + Diffview.nvim 是我目前找到的最好的解决方案，下面稍加配置，实现在 gv.vim 的 log 界面通过快捷键 `vv` 在 Diffview.nvim 里打开光标所在行对应的 commit ：

```vim
lua << EOF
function _G.diff_view_commit(commit_hash)
    require'diffview'.open(commit_hash .. '~1..' .. commit_hash)
end
EOF

au! FileType GV nnoremap vv <Esc>:call <SID>DiffviewCommitUnderCursor()<CR>

function! s:DiffviewCommitUnderCursor()
    normal! ^2f w
    call v:lua.diff_view_commit(expand('<cword>'))
endfunction
```
