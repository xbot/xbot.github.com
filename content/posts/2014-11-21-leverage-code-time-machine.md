---
layout: post
title: "用好代码时光机"
slug: leverage code time machine
date: 2014-11-21 23:19:00
comments: true
categories:
- 计算机
tags:
- git
- svn
- 编程
---

今天看了篇文章，讲几种常见的、糟糕的注释用法。其中之一是把废弃的代码注释起来，而不是直接删掉，原因是担心以后可能会用。

这个其实就是版本控制系统（VCS）要解决的问题之一。包括对于团队协作的项目，经常需要看某段代码是谁改的、什么时间、什么原因。都是可以用VCS很方便地解决的问题。

我以前是用二分法在提交列表里找的。其实有更好的解决方法，思路是列出源码在历次提交中修改的内容，然后在其中查找要找的东西就行了。

git的解决方法：

```bash
git log -p abc.php
```

svn的解决方法：

```bash
svn log --diff --internal-diff abc.php
```

vim的辅助函数：

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
