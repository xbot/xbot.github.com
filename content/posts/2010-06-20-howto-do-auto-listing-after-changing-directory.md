---
layout: post
title: cd到目录下后自动ls的方法
slug: howto do auto listing after changing directory
date: 2010-06-20 00:00:00
tags:
- BASH
- Shell
- 计算机
status: publish
comments: true
meta:
  _edit_last: '1'
  views: '982'
  _wp_old_slug: ''
---
每cd到一个目录下就ls，这成了我的习惯。以下Bash函数和别名可以实现cd到一个目录下就自动执行ls命令：

```bash
cdl() {
    cd "${1}";
    ls;
}
alias cd=cdl
```

将上述内容添加到用户主目录中的.bashrc中即可。
