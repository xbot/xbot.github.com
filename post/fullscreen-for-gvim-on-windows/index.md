# Windows下GVim的全屏

下载gvim的一个扩展“<a href="https://github.com/derek/gvimfullscreen_win32">gvimfullscreen_win32</a>”，并解压缩。

将<strong>gvimfullscreen.dll</strong>复制到gvim安装目录下，与<strong>gvim.exe</strong>同目录。

修改gvim配置文件<strong>_vimrc</strong>，在其中添加如下内容：

```vim
if has('win32')
    map <F11> <Esc>:call libcallnr("gvimfullscreen.dll", "ToggleFullScreen", 0)<CR>
endif
```

此后，即可使用<strong>F11</strong>键开关gvim的全屏状态。

