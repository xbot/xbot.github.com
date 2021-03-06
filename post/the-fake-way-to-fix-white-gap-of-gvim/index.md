# 解决gVim白边问题的伪方法

<p>gVim的窗口大小与行数、列数、字体宽度等都有关系，并不像一般的图形界面一样可以完全地最大化或全屏，如果几个因素组合后与屏幕分辨率不符合，就会在屏幕的左边和底部出现灰色的白边。</p>

<p>这是gVim非常著名的一个问题，它使最大化的gVim窗口显得非常难看。目前仅有一种变通的解决方法，就是把白边部分的颜色设置成和gVim主题背景色一致的颜色：</p>

<p>
```bash
# 修改~/.gtkrc-2.0，加入如下内容：
style "vimfix" {
  bg[NORMAL] = "#DBDBD2" # this matches my gvim theme 'Normal' bg color.
}
widget "vim-main-window.*GtkForm" style "vimfix"
```
</p>

<p>由于并非真正地去掉白边，所以只能算是个伪方法。</p>

<p>Windows下的方法见<a href="../fullscreen-for-gvim-on-windows/">这里</a>。</p>

