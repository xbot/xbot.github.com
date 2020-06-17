---
layout: post
title: Vim保存文件时移除尾行换行符的方法
slug: howto remove eof in vim
date: 2010-12-05 00:00:00
tags:
- Vim
- Windows
- 计算机
- 配置
status: publish
published: true
comments: true
meta:
  _edit_last: '1'
  views: '1192'
  _wp_old_slug: ''
---
Vim认为一个文本文件的每一行都应该由一换行符结束，即使文件的最后一行也不例外。这就使得Vim保存过的文本文件在其它文本编辑器中查看时可能会在文件末尾多出一个空行。一般来说，这不是个问题，但对于程序员来说，有时候一些不规范的运行环境或其它组件就要求文件的最后一行不能有换行符，否则就会出莫名其妙的问题，尤其是在Windows环境下最常见。

要在保存文件时不在最后一行添加换行符，最常见、最简单的方法就是：

```vim
:set binary
:set noendofline
```

但是这样做有一个问题，就是会把DOS格式的文件自动转换成UNIX格式，WinSlave们肯定不希望这么做。

因此，可在vimrc中加入如下内容：

```vim
" Save the current buffer as a file with no EOF sign.
function! SaveAsNOEOF(filename)
    let a=getline(1,line('$')-1)
    let b=map(a, 'iconv(v:val,"'.&enc.'","'.&fenc.'") . nr2char(13)')
    call extend(b, getline('$', '$'))
    call writefile(b,a:filename, 'b')
    if a:filename == bufname('%')
        set nomodified
    endif
endfunction
" Save the current buffer and get rid of the EOF sign.
function! SaveNOEOF()
    call SaveAsNOEOF(bufname('%'))
endfunction
command! -complete=file -nargs=0 SaveNOEOF :call SaveNOEOF()
command! -complete=file -nargs=1 SaveAsNOEOF :call SaveAsNOEOF(<q-args>)
```

然后就可以使用SaveNOEOF和SaveAsNOEOF两条命令去保存没有EOF的文件了。

<em>说明：本文的目的在于保存文件时移除最后一行的换行符，而并非移除EOF，上述配置中使用EOF只为记忆更容易起见。</em>

<strong>更新：</strong>

<blockquote>
<strong>2010-12-15</strong>

<ol>
	<li>修正SaveNOEOF命令报缺少参数的错误的问题</li>
	<li>修正保存当前buffer后没有变更文档修改状态的问题</li>
	<li>修正保存文件后总是将编码转换成encoding选项的值的问题</li>
</ol>


</blockquote>

