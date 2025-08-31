---
title: 用vim-plug换掉了vim-addon-manager
slug: replaced vam with vim plug
date: 2017-07-31 22:44:16
tags:
- Vim
- 最佳实践
- 计算机
---

四年零两个月前，我在Vim的邮件组中[征询pathogen和vundle的优劣](https://groups.google.com/forum/#!topic/vim_use/P3xpaHr8-do)。一个人建议我试试VAM，语气幽怨——他是它的作者。

试过之后就从pathogen迁移到了VAM，因为它兼有前者和vundle的优点，在当时，这是最好的插件管理器。就这样用了很久，期间还写了点脚本[简化插件的管理](/post/simplify-vim-addon-installation-issues/)，VAM工作得很好。

最近发现越来越多的插件的文档里提到vim-plug，亮点是支持并行安装和更新，这真是解决了VAM最大的痛点：我叠代了十年的vimrc里用了很多插件，串行的VAM每次更新都让我很痛苦。

另外，vim-plug较VAM的另一个优势是简单。这倒不是说后者很难用，只是作者过于追求灵活性，导致有些工作实现起来相对更复杂。例如，有的插件安装之后需要做一些额外的操作，VAM需要自己实现回调函数，然后挂到post-install钩子上。而对于vim-plug，只需要在注册插件的地方增加一个选项。对于插件的懒加载，也是同样。

VAM也有自己的优势。其中之一是可以自动处理依赖关系：当一个要被安装的插件依赖别的插件时，相应的依赖也会被自动安装。还有就是对多源的支持：既可以拉取github上的插件，也可以从vim.org或其它源安装。而vim-plug就只支持github。

所以，vim-plug并没有全面超过VAM，只是没有明显的短板、功能又刚刚够用。

