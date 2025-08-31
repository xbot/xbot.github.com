---
title: 用neocomplete换掉了YouCompleteMe
slug: replace youcompleteme with neocomplete
date: 2017-07-15 10:29:48
tags:
- Vim
- 最佳实践
- 计算机
---

在尝试了多种方案后，我又一次换掉了自动补全插件。

工具应该是能提高生产力的，而不是相反，很长一段时间以来，YouCompleteMe带给我的却是个疑惑，真不知道这个东东到底是提高了我的生产力，还是反之。自动补全在提高输入速度和准确度上都有助益，但是有时候补全选项弹出的又很慢。

测试了nvim-complete-manager+LanguageClient-neovim+LanguageServer-php-neovim、deoplete+phpcd、deoplete+padawan、neocomplete+phpcomplete-extended，效果都非常不理想。最后选择了neocomplete+phpcomplete，速度可以接受，表现比YCM稳定，功能该有的都有。

### 相关阅读：

* [How to Make YouCompleteMe Compatible with UltiSnips](/post/make-youcompleteme-ultisnips-compatible/)
* [Vim的终极自动补全插件：NeoComplCache](/post/neocomplcache-vim/)

