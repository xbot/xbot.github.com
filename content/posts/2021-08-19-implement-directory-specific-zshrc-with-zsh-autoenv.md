---
title: 用 zsh-autoenv 实现目录级别的 zshrc
slug: Implement Directory Specific Zshrc With Zsh Autoenv
date: 2021-08-19 23:34:25+08:00
tags:
- zsh
- 工具
- 软件
- 计算机
---

[zsh-autoenv](https://github.com/Tarrasch/zsh-autoenv) 可以实现当进入特定的目录时加载特定的 zshrc 文件，并当切换到其它目录时取消其中的变更。后者在当前的版本下，对环境变量、命令别名和函数有效。

使用方法为：

在目录（如 `~/project` ）下创建文件 `.autoenv.zsh` :

```zsh
autostash TEST_VAR='This is a test variable.'

autostash alias test_cmd='echo "This is a test alias."'

autostash test_func
test_func() {
    echo "This is a test function."
}
```

出于安全考虑， zsh-autoenv 会对 `.autoenv.zsh` 做哈希校验，所以第一次或者做变动后进入该目录时，都会要求确认是否接受此文件最新的内容。

此外，如果进入的目录不存在 `.autoenv.zsh`， zsh-autoenv 会自动向上查找最近的同名文件并加载，上溯的层级可以通过配置项定义。

最后，它提供了一个命令 `autoenv-edit` 用来快速打开和编辑 `.autoenv.zsh` 文件。
