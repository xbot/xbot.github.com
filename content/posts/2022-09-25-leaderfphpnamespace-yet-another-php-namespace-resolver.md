---
title: "Leaderf-phpnamespace: Yet another PHP namespace resolver"
slug: "Leaderf-phpnamespace: Yet another PHP namespace resolver"
date: 2022-09-25T22:33:13+08:00
categories: ["计算机"]
tags:
- Vim
- LeaderF
- plugin
- 编程
toc: false
---

This plugin uses the power of [LeaderF](https://github.com/Yggdroot/LeaderF) to perform PHP namespace related tasks.

https://github.com/xbot/Leaderf-phpnamespace

## Features

- Insert `use` statements for FQCNs.
- Expand class names to FQCNs. 
- TODO: Insert namespace for the current file.
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
" Import the current class under cursor
noremap <leader><leader>iu :<C-U><C-R>=printf("Leaderf phpns --input %s", expand("<cword>"))<CR><CR>
```

## Commands

```vim
:LeaderfPhpns
```

Press `F1` in the popup window to get more help.
