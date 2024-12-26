---
title: 'Leaderf-phpnamespace: Yet another PHP namespace resolver'
slug: 'Leaderf-phpnamespace: Yet another PHP namespace resolver'
date: 2022-09-25 22:33:13+08:00
tags:
- Vim
- LeaderF
- plugin
- 编程
- 计算机
toc: false
---

This plugin uses the power of [LeaderF](https://github.com/Yggdroot/LeaderF) to perform PHP namespace related tasks.

https://github.com/xbot/Leaderf-phpnamespace

## Features

- Insert `use` statements for FQCNs.
- Sort `use` statements alphabetically.
- Expand class names to FQCNs. 
- Insert namespace for the current file.
- All the above features support the fuzzy searching function and all the three modes (nameonly, fullpath and regex) provided by LeaderF.

## Requirements

- [LeaderF](https://github.com/Yggdroot/LeaderF)
- ctags is properly configured in Vim/Neovim.

## Install

Use any of your favourite plugin manager to install it, for example:

### vim-plug

```vim
Plug 'xbot/Leaderf-phpnamespace'
```

## Setup

```vim
" Import the current class under cursor.
noremap <leader>iu :<C-U><C-R>=printf("Leaderf phpns --input %s", expand("<cword>"))<CR><CR>

" Expand the classname under cursor to its FQCN form.
noremap <leader>ec :<C-U><C-R>=printf("Leaderf phpns --input %s --expand", expand("<cword>"))<CR><CR>
```

## Commands

| Command            | Description                            |
| ---                | ---                                    |
| LeaderfPhpns       | Choose an FQCN to import.              |
| PHPNamespaceInsert | Insert namespace for the current file. |

Press `F1` in the popup window to get more help.

## Options

| Option                            | Default | Description                                                             |
| ---                               | ---     | ---                                                                     |
| g:Lf_PHPNamespaceExpandToAbsolute | 1       | Expand the classname under cursor to absolute FQCN.                     |
| g:Lf_PHPNamespaceSortAfterImport  | 1       | Sort the `use` statements block alphabetically after importing a class. |