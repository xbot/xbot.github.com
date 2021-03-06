# 在Vim中检查语法和执行代码

<div class="illustration_right">
<a href="http://picasaweb.google.com/lh/photo/OXk7sdAO9gNHu0qkTgK1PA?feat=embedwebsite"><img src="http://lh3.ggpht.com/_ceUJ_lBTHzc/SaV494hGreI/AAAAAAAAAzY/xExf7CzGyv0/s800/the-mug-of-vi.jpg" /></a>
</div>

<p>《<a href="http://www.pragprog.com/the-pragmatic-programmer">The Pragmatic Programmer</a>》的确是本好书，虽然中文译名极为恶俗。为践行书中提到的<a href="http://en.wikipedia.org/wiki/Don't_repeat_yourself">DRY原则</a>，我开始反思平时编码时总是重复出现的问题，其中之一就是很低级的语法错误，有时忘了语句最后的分号，有时忘了声明变量，凡此种种，不一而足。</p>

<p>相对于IDE，我更喜欢Vim，这也是语法错误没有检查出来的原因。因此就产生了给Vim添加语法检查功能的需求。下面是检查PHP代码的vimrc内容：</p>

<p>
```vim
" Check the syntax of a PHP file
function! CheckPHPSyntax()
    if &filetype != 'php'
        echohl WarningMsg | echo 'This is not a PHP file !' | echohl None
        return
    endif
    setlocal makeprg=php\ -l\ -n\ -d\ html_errors=off\ %
    setlocal errorformat=%m\ in\ %f\ on\ line\ %l
    echohl WarningMsg | echo 'Syntax checking output:' | echohl None
    if &modified == 1
        silent write
    endif
    silent make
    clist
endfunction
au filetype php map <F5> :call CheckPHPSyntax()<CR>
au filetype php imap <F5> <ESC>:call CheckPHPSyntax()<CR>
```
</p>

<p>以上脚本为Vim添加了一个检查PHP语法错误的函数和两个快捷键映射，此后可按F5键即时检查当前Buffer中的PHP当面的语法。和网上其它同类的函数相比，这个函数的优点在于改进了检查结果的显示，使用silent命令隐藏了所有不必要的输出。</p>

<p>以下是检查Python代码语法错误的脚本：</p>

<p>
```vim
" Check the syntax of a python file
function! CheckPythonSyntax()
    if &filetype != 'python'
        echohl WarningMsg | echo 'This is not a Python file !' | echohl None
        return
    endif
    setlocal makeprg=python\ -u\ %
    set efm=%C\ %.%#,%A\ \ File\ \"%f\"\\,\ line\ %l%.%#,%Z%[%^\ ]%\\@=%m
    echohl WarningMsg | echo 'Syntax checking output:' | echohl None
    if &modified == 1
        silent write
    endif
    exec "silent make -c \"import py_compile;py_compile.compile(r'".bufname("%")."')\""
    clist
endfunction
au filetype python map <F5> :call CheckPythonSyntax()<CR>
au filetype python imap <F5> <ESC>:call CheckPythonSyntax()<CR>
```
</p>

<p>上面两部分的配置脚本中虽然都指定了使用F5检查代码语法，但由于使用了<strong>au filetype</strong>指定了相应的语言类型，因此没有冲突。</p>

<p>和PHP相比，Python似乎更受垂青，因为除了上面和PHP一样的语法检查方式，它还有令人惊艳的<a href="http://www.vim.org/scripts/script.php?script_id=2441">PyFlakes</a><a href="#footnote-pyflakes"> <sup><b>1</b></sup></a>。</p>

<p>PyFlakes的意思是<strong>Make on the fly</strong>，与另一神器Emacs的Flymake遥相呼应。只需要举出PyFlakes的两三个特性，就足以说明这是怎样的一个尤物了：其一是实时和高效，PyFlakes会在输入代码的同时检查语法错误，而且用户丝毫感觉不到任何停顿（<em>对于游戏玩家或高清电影狂可能“卡”更好理解</em>）；其二是智能，它居然能检查出哪些导入的模块没有被使用，哪些被使用的模块没有被导入；其三，PyFlakes检查出语法错误后会使用红色波浪线标识出错误位置。它使Vim完成了一个华丽的转身。</p>

<p>此外，由于PyFlakes是通过解析代码来检查语法错误，因此不必担心代码会被实际执行。</p>

<p>需要说明的是，PyFlakes要求Vim在编译时启用了对Python的支持，这一点可以使用<strong>:ver</strong>命令查看，一般各Linux发行版自带的Vim都加入了这个特性，而Windows下的版本没有此特性的可能性较大，好在作者提供了<a href="http://symbolsystem.com/vim/">加入Python特性的Vim的Windows编译版</a>。</p>

<p>既然事已如此，不妨一不做、二不休，为Vim加上即时执行代码的功能。这个想法由来已久，出于和<a href="http://0x3f.org/?p=1433">寻找phpsh</a>同样的原因，我希望能在Vim中临时输入小块代码，然后即时执行并查看结果；或者即时执行正在编写的Python模块或程序。以下脚本实现了随手打开一个新的分割窗口并创建一个临时的脚本文件的功能：</p>

<p>
```vim
" Open a temporary PHP file in a new window
function! PHPSandBox()
    let tmpfile = tempname().'.php'
    exe 'new '.tmpfile
    call setline('.', '<?php')
    normal o
    normal o
    call setline('.', '?>')
    normal k
    startinsert
endfunction
" Open a temporary Python file in a new window
function! PySandBox()
    let tmpfile = tempname().'.py'
    exe 'new '.tmpfile
    call setline('.', '#!/usr/bin/python')
    normal o
    call setline('.', '# -*- coding: utf-8 -*-')
    normal o
    startinsert
endfunction
nmap <leader>sbpy :call PySandBox()<CR>
nmap <leader>sbph :call PHPSandBox()<CR>
```
</p>

<p>下面的脚本则实现了即时执行当前Buffer中代码的功能：</p>

<p>
```vim
" Run a PHP script
function! ExecutePHPScript()
    if &filetype != 'php'
        echohl WarningMsg | echo 'This is not a PHP file !' | echohl None
        return
    endif
    setlocal makeprg=php\ -f\ %
    setlocal errorformat=%m\ in\ %f\ on\ line\ %l
    echohl WarningMsg | echo 'Execution output:' | echohl None
    if &modified == 1
        silent write
    endif
    silent make
    clist
endfunction
au filetype php map <C-F5> :call ExecutePHPScript()<CR>
au filetype php imap <C-F5> <ESC>:call ExecutePHPScript()<CR>

" Run a python script
function! ExecutePythonScript()
    if &filetype != 'python'
        echohl WarningMsg | echo 'This is not a Python file !' | echohl None
        return
    endif
    setlocal makeprg=python\ -u\ %
    set efm=%C\ %.%#,%A\ \ File\ \"%f\"\\,\ line\ %l%.%#,%Z%[%^\ ]%\\@=%m
    echohl WarningMsg | echo 'Execution output:' | echohl None
    if &modified == 1
        silent write
    endif
    silent make
    clist
endfunction
au filetype python map <C-F5> :call ExecutePythonScript()<CR>
au filetype python imap <C-F5> <ESC>:call ExecutePythonScript()<CR>
```
</p>

<blockquote id="footnote-pyflakes">
<p><sup><b>Foot Note 1 :</b></sup></p>
<p>
PyFlakes本身是个通用的工具，其作者提供了一个Vim的插件调用PyFlakes的功能，故此处所说的PyFlakes实指Vim的PyFlakes插件。
</p>
</blockquote>

