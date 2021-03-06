# Vim自定义命令的参数自动补全

<p><a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>起初的几个版本采用增加命令个数的方式减少每个命令的参数个数，目的是降低命令格式记忆的难度。从2.3.0版本起，开始采用Vim自定义命令的一些高级特性，精简命令个数，虽然参数个数增加了，但由于实现了参数补全，反而更加直观。</p>

<p>Vim对自定义命令提供了多种补全参数的方式，详见<code>:h command-complete</code>。其中，最常用的当属<strong>custom</strong>：</p>

```vim
command! -nargs=? -complete=custom,StatusCmpl UBSend exec('py ub_send_item(<f-args>)')
```

<p>以上代码定义了一个自定义命令<strong>UBSend</strong>，它可以获得一个或零个参数（<em>-nargs=?</em>），如果指定参数，则补全方式采用<strong>custom</strong>，该方式调用一个名为<strong>StatusCmpl</strong>的函数并以其返回值作为补全的值域：</p>

```vim
function! StatusCmpl(ArgLead, CmdLine, CursorPos)
  return "draft\npublish\nprivate\npending\n"
endfunction
```

<p>从以上代码可以看出，该函数需要接受三个参数，返回值应该是一个用换行符“\n”分割的字符串，在执行补全时，Vim自动使用正则表达式匹配备选项。对于只有一个参数的情况，这种实现方式简单高效。</p>

<p>对于参数较多的情况，<strong>customlist</strong>补全方式最为适用：</p>

```vim
command! -nargs=* -complete=customlist,UBNewCmpl UBNew exec('py ub_new_item(<f-args>)')
```

<p>以上代码定义了命令<strong>UBNew</strong>，<strong>customlist</strong>补全方式调用函数<strong>UBNewCmpl</strong>获取补全的值域：</p>

```vim
function! UBNewCmpl(ArgLead, CmdLine, CursorPos)
    let lst = split(a:CmdLine)
    if len(a:ArgLead)>0
        let lst = lst[0:-2]
    endif

    let results = []
    " For the first argument, complete the object type
    if len(lst)==1
        let objects = ['post','page','tmpl']
        for obj in objects
            if stridx(obj,a:ArgLead)==0
                call add(results,obj)
            endif
        endfor
    " For the second argument, complete the syntax for :UBNew post or :UBNew
    " page
    elseif len(lst)==2 && count(['post', 'page'], lst[1])==1
        let syntaxes = ['markdown','html','rst','textile','latex']
        for synx in syntaxes
            if stridx(synx,a:ArgLead)==0
                call add(results,synx)
            endif
        endfor
    endif
    return results
endfunction
```

<p>这时侯，前面提到的三个参数就派上用场了。<strong>ArgLead</strong>是进行补全时，已输入的参数部分，例如输入<code>:UBNew p</code>，然后按Tab键，则ArgLead的值就是“p”；<strong>CmdLine</strong>是已经输入的命令的全部，按上例，这个参数的值就是“UBNew p”；<strong>CursorPost</strong>是当前光标距离命令行开头的字符数。利用这三个参数，就可以判断正在补全命令的第几个参数，进而利用ArgLead筛选该参数的值域。</p>

<p>与<strong>custom</strong>调用的函数不同，<strong>customlist</strong>调用的函数的返回值应该是一个<strong>list</strong>类型的值。</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

