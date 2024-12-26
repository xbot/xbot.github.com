---
title: Git Reset、Revert和Checkout的区别
slug: differences among reset checkout revert of git
date: 2017-02-09 19:27:47
tags:
- git
- 笔记
- 计算机
---

reset在提交层面，是将HEAD设定到指定的提交，通常用来舍弃最新的几个提交。在文件层面，是将指定的提交中的该文件保存到暂存区，工作区中的文件不变。

reset有三个常用的参数：\--mixed、\--soft和\--hard。

在提交层面，\--mixed是默认值，影响暂存区，不影响工作区；\--soft将HEAD版本保存到暂存区，并将HEAD设定到指定的提交，用来合并提交历史；\--hard既影响暂存区，也影响工作区。

在文件层面，\--soft和\--hard均会报错，不支持这两个选项。

在提交层面，执行过reset后如果需要撤销，使用reflog命令查看之前HEAD的hash，通过reset \--hard恢复到该版本。

checkout在提交层面，用来切换分支或检出到指定的提交。对于后者，如果增加了新的提交，在切换到其它分支后，再切换回来时，这些提交将会丢失，如果希望保存这些提交，在切换到其它分支前，创建一个新的分支。

revert用来通过创建一个新的提交来回滚一个提交，因此，和reset不同，并不会改变提交的历史。

对于回滚一次提交的场景，revert比reset安全，所以revert适用于公共分支，reset适用于私有分支。
