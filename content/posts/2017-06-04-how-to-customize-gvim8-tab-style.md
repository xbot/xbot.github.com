---
title: 自定义GVIM 8标签栏样式的方法
slug: how to customize gvim8 tab style
date: 2017-06-04 12:42:12
categories:
- 计算机
tags:
- vim
- linux
---

GVim 8用的是GTK 3，原来在`~/.gtkrc-2.0`里加样式的方法不能用了。

GTK 3的样式在`~/.config/gtk-3.0/gtk.css`里：

```css
/**
 * Adapt to the nova colorscheme
 */
@define-color VIM_BG_FIX #3C4C55;

window#vim-main-window {
    background-color: @VIM_BG_FIX;
}

window#vim-main-window notebook header {
    background-color: #1E272C;
    border-bottom-width: 0;
}
window#vim-main-window notebook tab {
    border-bottom-width: 0;
}
window#vim-main-window notebook tab label {
    padding-left:5px;
    padding-right:5px;
    padding-top:1px;
    padding-bottom:2px;
    color: #7CBDC6;
}
window#vim-main-window notebook tab:checked label {
    background-color: #7CBDC6;
    color: #1E272C;
}
```

查看GTK 3程序的样式结构的方法是用GTK Inspector：

```bash
GTK_DEBUG=interactive gvim
```

