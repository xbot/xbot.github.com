---
title: Git的衍合
slug: git rebase
date: 2017-02-22 18:44:37
categories:
- 计算机
tags:
- git
- 笔记
---

合并分支有两种方式，merge和rebase。merge是根据三方合并的差异，创建一个新的提交。rebase是将上游分支的各个提交在比较差异后在下游分支上重演一遍。

rebase适合对没有推送到远程的提交对象做合并，这样可以保持一个整洁的提交历史。若对已推送的提交对象使用rebase，可能导致其它已经拉取并创建了新的提交的人不得不重新合并，进而导致提交历史变得很混乱。

基本的rebase：

```bash
git rebase master unstable
git checkout master
git merge unstable
```

复杂的情况：存在三个分支，unstable基于master的某个提交创建，feature基于unstable的某个提交创建，要把feature合并到master而不合并unstable。更多分支时方法也一样。

```bash
git rebase --onto master unstable feature
git checkout master
git merge feature
```

rebase过程中如果存在冲突，合并后用`git add`标记已解决，再用`git rebase --continue`继续。如果解决冲突后仍然不能继续，可能是当前补丁的内容在下游分支已存在，可以用`git rebase --skip`跳过。
