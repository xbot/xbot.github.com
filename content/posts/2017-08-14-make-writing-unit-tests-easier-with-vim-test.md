---
title: 用vim-test简化单元测试的编写
slug: make writing unit tests easier with vim test
date: 2017-08-14 16:43:59
categories:
- 计算机
tags:
- 最佳实践
- 编程
- 单元测试
- vim
---
vim-test允许在Vim中直接执行一个或多个单元测试，并使用预设的或自定义的执行策略。

例如，命令`:TestNearest`可以执行当前测试文件中离光标最近的一个测试方法。而`:TestFile`、`:TestSuite`和`:TestLast`分别执行整个测试文件、测试项目和最近一次执行过的测试。这在编写测试代码时能很大地提高效率。

执行策略是执行测试的方式和环境。例如缺省状态下，将使用`:!`执行测试命令，这会从Vim切换回终端。而在MacVim下，可以选择在iTerm或者系统自带的Terminal中执行。
