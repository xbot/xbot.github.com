---
layout: post
title: "FZF: 又一個文件模糊查詢工具"
date: 2016-02-01 11:42
comments: true
categories: 計算機
tags:
- vim
- 插件
---

[fzf](https://github.com/junegunn/fzf)是個獨立的命令行工具，索引速度很快，可以通過[fzf-vim](https://github.com/junegunn/fzf.vim)配合使用。

特性
----

功能支持還是比較全的：

-----------------+-------------------------------------------------------------------  
Command          | List                                                                
-----------------+-------------------------------------------------------------------  
 `Files [PATH]`    | Files (similar to  `:FZF` )  
 `GitFiles`        | Git files  
 `Buffers`         | Open buffers  
 `Colors`          | Color schemes  
 `Ag [PATTERN]`    | {ag}{5} search result (ALT-A to select all, ALT-D to deselect all)  
 `Lines`           | Lines in loaded buffers  
 `BLines`          | Lines in the current buffer  
 `Tags`            | Tags in the project ( `ctags -R` )  
 `BTags`           | Tags in the current buffer  
 `Marks`           | Marks  
 `Windows`         | Windows  
 `Locate PATTERN`  |  `locate`  command output  
 `History`         |  `v:oldfiles`  and open buffers  
 `History:`        | Command history  
 `History/`        | Search history  
 `Snippets`        | Snippets ({UltiSnips}{6})  
 `Commits`         | Git commits (requires {fugitive.vim}{7})  
 `BCommits`        | Git commits for the current buffer  
 `Commands`        | Commands  
 `Maps`            | Normal mode mappings  
 `Helptags`        | Help tags [1]  
-----------------+-------------------------------------------------------------------  

結論
----

fzf需要在終端中執行，在vim中使用時需要另外啓動一個xterm實例，UI的割裂感很強，而且xterm本身的操性你懂的。另外Tags模式需要兩次回車。所以暫不會用它取代ctrlp和unite。
