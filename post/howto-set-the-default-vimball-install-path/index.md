# 修改Vimball默认安装路径

<p>在Windows下，我希望把vba文件安装到$VIM/vimfiles目录下，而不是用户主目录中。</p>

<p>Vim在安装vba文件时默认从$VIMRUNTIME中取出第一个路径作为安装路径，使用如下命令查看当前设置：</p>

<p>
```vim
:echo &rtp
```
</p>

<p>在Windows下，Vim默认将用户主目录放在了$VIMRUNTIME的第一个，因此才会将vba安装到用户主目录。</p>

<p>修改方法为在vimrc中加入如下设置：</p>

<p>
```vim
set rtp-=$HOME/vimfiles
```
</p>

