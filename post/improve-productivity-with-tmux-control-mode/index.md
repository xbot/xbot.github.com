# 用Tmux的控制模式提高生产力


Tmux的控制模式（Control Mode）可以把tmux的窗口映射为本地虚拟终端的窗口，也就是说，用户可以像操作本地虚拟终端一样操作tmux。这对备受tmux的emacs风格的热键绑定折磨或需要嵌套tmux的人来说非常有用。

在支持这个特性的虚拟终端（例如「iTerm」）里，执行`tmux -CC`即可。当然也可以`tmux -CC a`恢复会话。


