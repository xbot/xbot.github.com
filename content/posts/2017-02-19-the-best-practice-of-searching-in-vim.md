---
title: Vim 查找与替换的最佳实践
slug: the best practice of searching in vim
date: 2017-02-19 20:37:18
categories:
- 计算机
tags:
- vim
- 最佳实践
---

## 更新

| 日期       | 更新内容                                                     |
| ---------- | ------------------------------------------------------------ |
| 2022-03-16 | 更新 80% 的内容。包含这几年所有的配置上的更新，并且把 ag 换成了 rg 。 |
| 2017-02-19 | 第一版。                                                     |



## 目标

1. 多文件搜索和替换
   * 使用 PCRE 正则表达式在工作目录下递归搜索
   * 将选定文本自动转换成正则表达式
   * 有较高的搜索速度
   * 对搜索结果做替换

2. 当前 buffer 内的搜索和替换
   - 一键将选定文本自动转换成正则表达式并执行搜索或替换


## 依赖

* [Ferret](https://github.com/wincent/ferret)：实现对搜索和替换操作的封装。
* [ripgrep](https://github.com/BurntSushi/ripgrep)：搜索工具，速度快。

## 工具和方法

### Ferret

Ferret 是我用过的 vim 搜索扩展里最接近理想的一个，最大的优点是不用引号包裹搜索内容，且较大程度地支持 PCRE 正则表达式。

EasyGrep 的缺点是正则表达式必须是 shellescape() 过的，手写不方便，也不直观。Ferret 在底层做了 shellescape() ，所以比 EasyGrep 简单一些。~~不过 Ferret 对反斜杠的处理仍然不直观，例如搜索`App\Link`，正则表达式是`App\\Link`，而Ferret里只能用`App\\\\Link`。原因是从输入到执行，存在著三层转义：Vim命令行、Shell、grep/ag。所以对于EasyGrep，要搜索一个`\`，必须输入`\\\\\\\\`。对于Ferret，由于底层做了shellescape()，只需要两层转义，即`\\\\`。当然这样也不方便，所以我用自定义函数XEscapeRegex()对选择的内容做这个事，不过最完美的方法当然是Ferret自己支持，或者自己再封装一下Ferret的命令，这样输入的正则表达式可读性就正常了。~~

Ferret 的另一个优点是可以对搜索结果做筛选，删除不需要做替换的行，再对剩下的行执行批量替换。当执行替换命令 `:Acks` 时，会自动把上一个 `:Ack` 命令中的正则表达式或关键词补全到命令的参数中。

Ferret 最大的问题在于，搜索使用 rg 、 ag 这样的命令行工具，而替换使用 Vim 内置的 `:substitute` 命令。前者使用 PCRE 正则表达式，而后者使用 Vim 自己的正则表达式。两者在格式上的不兼容导致对一些复杂的 pattern 做搜索和替换时，往往需要对 pattern 做修改才能正常对之前的搜索结果做替换。关于这个问题，后面细说。

### ripgrep

rg 是我用过的搜索工具里对速度和功能兼顾得最好的。

### 转换选定内容到正则表达式

我使用自定义函数 `EscapeRegex()` 根据参数转换字符串到不同格式的正则表达式。主要用途为转换成给搜索当前缓冲区的 Vim 正则表达式，或给 Ferret 使用的 PCRE 正则表达式。

### 解决 Ferret 搜索和替换命令的正则表达式兼容性问题

需要用到 Vim 的两个特性： `magic` 选项和 `command-line window` 。

使用 `magic` 选项的控制符，可以使搜索内容中的特殊字符表现出不同的行为。例如，当表达式前附加 `\m` 时，表示使用 Vim 内置的正则表达式格式，由于这是默认的行为，所以也可以省略这个控制符。

以下是 4 种 `magic` 控制符的总结：

| 控制符 | 含义         | 说明                                                         |
| ------ | ------------ | ------------------------------------------------------------ |
| \m     | magic        | ^ $ . * ~ []等具有特殊含义。当然，反斜杠和表达模式起止的分隔符也算具有特殊含义。 |
| \M     | nomagic      | 仅 ^ $ 具有特殊含义。当然，反斜杠和表达模式起止的分隔符也算具有特殊含义。 |
| \v     | very magic   | 所有 ASCII 字符中（即键盘上能看到的字符），除了数字 0-9 、大小写字母 A-Za-z 和下划线 _ 外，全都有特殊含义。 |
| \V     | very nomagic | 大多数字符都表示其本身，除了反斜杠 \ ，以及用来表示模式起止的分隔符（如 / 或 ? ）。 |

所以对于当前 buffer 的搜索和替换，我使用 `\m` ，即 Vim 内置的正则格式，并通过调用只传递第一个参数的 `EscapeRegex()` 函数把选中的内容转义成正则表达式。

对于多文件的搜索，仍然通过 `EscapeRegex()` 函数转义正则，但此时需传递第二个参数（值为 1 ），从而转换成 PCRE 格式的正则。

对于多文件的替换，把 `\v` 控制符附加在正则表达式前面。在 `very magic` 格式下，大部分非字母和数字符号都被认为有特殊含义，此时，PCRE 表达式中的特殊字符是它的子集。多数情况下，替换前的搜索中使用的简单的表达式不需要修改就可以直接用于 `:Acks` 命令，当存在差集中的字符时，修改表达式并对这些字符做转义即可。

为了简化这个步骤，这里需要用到 `command-line window` 。通过 `cedit` 选项配置的快捷键，可以在 Vim 的命令模式下打开包含当前正在编辑以及之前执行过的命令的窗口。如果在普通模式下，可以通过 `q:` 指令打开。此时，把光标置于要转义的命令所在行，然后执行相应的快捷键即可，我这里定义的是 `<leader>ss` ，它调用的是自定义函数 `EscapeFerretPatternInCurrentLine()` ，此函数简单地对当前行中的特定字符做转义。

## 配置

```vim
set cedit=\<C-E>

" Find and replace
nmap <leader>// yiw/\<<C-R>"\>\C
vmap <leader>// y/\m<C-R>=EscapeRegex(@")<CR>\C
nmap <leader>rr yiw:%s/\<<C-R>"\>\C//g<LEFT><LEFT>
vmap <leader>rr y:%s/<C-R>=EscapeRegex(@")<CR>\C//g<LEFT><LEFT>
nmap <leader>rl yiw:s/\<<C-R>"\>\C//g<LEFT><LEFT>
vmap <leader>rl y:s/<C-R>=EscapeRegex(@")<CR>\C//g<LEFT><LEFT>

let g:FerretExecutable='rg'
let g:FerretExecutableArguments = {
            \   'rg': '--no-ignore-vcs --vimgrep --no-heading --no-config --max-columns 4096 --follow -g !vendor/composer/ -g !storage/ -g !node_modules/'
            \ }
let g:FerretQFHandler='botright copen 20'
let g:FerretLLHandler='botright lopen 20'

nmap <leader>ak  <Plug>(FerretAck)
nmap <leader>lak <Plug>(FerretLack)
nmap <leader>aw  <Plug>(FerretAckWord)
nmap <leader>as  <Plug>(FerretAcks)
vmap <leader>ak  y:Ack <C-R>=EscapeRegex(@", 1)<CR>
vmap <leader>lak y:Lack <C-R>=EscapeRegex(@", 1)<CR>

" The patterns passed to the :Ack command may be not compatible with the
" :Acks command, so we need to escape some characters in them additionally
" in the command-line window.
augroup escape_ferret_pattern_in_the_current_line_in_commandline_window
    au!
    au BufEnter * if mode() == 'n' && getcmdwintype() == '' | nnoremap <leader>ss :call EscapeFerretPatternInCurrentLine()<CR> | endif
augroup END

function! EscapeFerretPatternInCurrentLine()
    exec 's/>/\\>/g'
    exec 's/&/\\&/g'
endfunction

" 转义正则表达式特殊字符，以便在正则表达式中使用
" a:1   是否转义为vimgrep的pattern格式，1，2
" a:2   是否用shellescape()转义，1是转义，2是转义并去掉两侧单引号
function! EscapeRegex(str, ...)"{{{
    let pattern = a:str
    let pattern = escape(pattern, '^$.*[]~"/\')

    if a:0 && a:1
        let pattern = escape(pattern, '()+?{}|')
        let pattern = substitute(pattern, '\\/', '/', 'g')
        if a:1 == 2
            let pattern = escape(pattern, '\')
        endif
    endif

    if a:0 > 1 && a:2
        let pattern = shellescape(pattern)
        if a:2 == 2
            let pattern = pattern[1:-2]
        endif
    endif

    let whitespacePattern = a:0 && a:1 ? '\\s\+' : '\\s\\+'
    let pattern = substitute(pattern, '\s\+', whitespacePattern, 'g')

    return pattern
endfunction"}}}
```
