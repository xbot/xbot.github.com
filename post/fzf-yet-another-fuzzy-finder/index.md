# FZF: 又一个文件模糊查询工具


[fzf](https://github.com/junegunn/fzf)是个独立的命令行工具，索引速度很快，可以通过[fzf-vim](https://github.com/junegunn/fzf.vim)配合使用。

特性
----

功能支持还是比较全的：

| Command        | List                                                               |
|----------------|--------------------------------------------------------------------|
| Files [PATH]   | Files (similar to  `:FZF` )                                        |
| GitFiles       | Git files                                                          |
| Buffers        | Open buffers                                                       |
| Colors         | Color schemes                                                      |
| Ag [PATTERN]   | {ag}{5} search result (ALT-A to select all, ALT-D to deselect all) |
| Lines          | Lines in loaded buffers                                            |
| BLines         | Lines in the current buffer                                        |
| Tags           | Tags in the project ( `ctags -R` )                                 |
| BTags          | Tags in the current buffer                                         |
| Marks          | Marks                                                              |
| Windows        | Windows                                                            |
| Locate PATTERN | `locate`  command output                                           |
| History        | `v:oldfiles`  and open buffers                                     |
| History:       | Command history                                                    |
| History/       | Search history                                                     |
| Snippets       | Snippets ({UltiSnips}{6})                                          |
| Commits        | Git commits (requires {fugitive.vim}{7})                           |
| BCommits       | Git commits for the current buffer                                 |
| Commands       | Commands                                                           |
| Maps           | Normal mode mappings                                               |
| Helptags       | Help tags [1]                                                      |

结论
----

fzf需要在终端中执行，在vim中使用时需要另外启动一个xterm实例，UI的割裂感很强，而且xterm本身的操性你懂的。另外Tags模式需要两次回车。所以暂不会用它取代ctrlp和unite。

