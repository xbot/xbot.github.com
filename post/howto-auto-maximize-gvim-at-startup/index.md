# 启动GVim时自动最大化窗口的方法

根据帮助文档，gvim在windows下的最大化是通过模拟打开窗口菜单并点击最大化菜单项实现的，而在Linux下的方法较为灵活。

下面的方法是在vim中通过调用wmctrl实现最大化的方法：

```vim
if has('win32')
    au GUIEnter * simalt ~x
else
    au GUIEnter * call MaximizeWindow()
endif

function! MaximizeWindow()
    silent !wmctrl -r :ACTIVE: -b add,maximized_vert,maximized_horz
endfunction
```

当然也可以通过配置窗口管理器规则实现自动最大化，但上面的方法更灵活。

