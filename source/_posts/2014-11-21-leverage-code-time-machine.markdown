---
layout: post
title: "用好代碼時光機"
date: 2014-11-21 23:19
comments: true
categories: 計算機
tags:
- git
- svn
- 編程
---

今天看了篇文章，講幾種常見的、糟糕的注釋用法。其中之一是把廢棄的代碼注釋起來，而不是直接删掉，原因是擔心以後可能會用。

這個其實就是版本控制系統（VCS）要解決的問題之一。包括對于團隊協作的項目，經常需要看某段代碼是誰改的、什麽時間、什麽原因。都是可以用VCS很方便地解決的問題。

我以前是用二分法在提交列表裏找的。其實有更好的解決方法，思路是列出源碼在曆次提交中修改的内容，然後在其中查找要找的東西就行了。

git的解決方法：

```bash
git log -p abc.php
```

svn的解決方法：

```bash
svn log --diff --internal-diff abc.php
```

vim的輔助函數：

```vim
" Show commit history of the current file under the given VCS in a new window
function! ShowCommitHistory(vcs)
    " Check parameter
    if a:vcs != 'svn' && a:vcs != 'git'
        echoerr 'Unknow VCS: '.a:vcs
        return
    endif

    " Do the dirty work
    let fileName = expand('%')
    if !empty(fileName)
        exe 'new'
        if a:vcs == 'svn'
            exe 'r !svn log --diff --internal-diff '.fileName
        elseif a:vcs == 'git'
            exe 'r !git log -p '.fileName
        endif
    else
        echo 'File not found.'
    endif
endfunction
nnoremap <leader>ssch :call ShowCommitHistory('svn')<CR>
nnoremap <leader>gsch :call ShowCommitHistory('git')<CR>
```
