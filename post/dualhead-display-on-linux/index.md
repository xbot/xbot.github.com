# Linux下双屏显示的设置

<p>RandR是对X11的一个扩展协议，允许在不重启X的情况下修改输出的一些参数并使之生效，xrandr是该协议的命令行界面，它的功能之一就是用来设置双屏显示。</p>

<p>缺省情况下，外接显示器后启用的是屏幕复制模式，即两个屏幕显示完全相同的内容。使用命令<code>xrandr -q</code>查看当前的输出状态：</p>

<blockquote>
  <p>Screen 0: minimum 320 x 200, current 1920 x 1080, maximum 8192 x 8192
  LVDS1 connected 1280x800+0+0 (normal left inverted right x axis y axis) 261mm x 163mm
    1280x800       60.0<em>+   60.0     50.0 <br />
    1024x768       75.1     70.1     60.0 <br />
    800x600        72.2     75.0     60.3     56.2 <br />
    768x576        75.0     72.0     60.0 <br />
    640x480        72.8     75.0     60.0     59.9 <br />
  VGA1 connected 1920x1080+0+0 (normal left inverted right x axis y axis) 509mm x 286mm
    1920x1080      60.0</em>+
    1680x1050      60.0 <br />
    1280x1024      75.0     60.0 <br />
    1440x900       59.9 <br />
    1280x960       60.0 <br />
    1024x768       75.1     70.1     60.0 <br />
    832x624        74.6 <br />
    800x600        72.2     75.0     60.3     56.2 <br />
    640x480        72.8     75.0     66.7     60.0 <br />
    720x400        70.1 <br />
  HDMI1 disconnected (normal left inverted right x axis y axis)
  DP1 disconnected (normal left inverted right x axis y axis)
  HDMI2 disconnected (normal left inverted right x axis y axis)
  DP2 disconnected (normal left inverted right x axis y axis)
  DP3 disconnected (normal left inverted right x axis y axis)</p>
</blockquote>

<p>可见，笔记本电脑的屏幕输出（<em>LVDS1</em>）的显示分辨率是1280x800，外接显示器的输出（<em>VGA1</em>）的分辨率是1920x1080。两个输出在整个虚拟屏幕（<em>名称：Screen 0；当前大小：1920x1080；最大：8192x8192</em>）上的坐标都是+0+0。所以在外接显示器中可以看到屏幕左上角有一个1280x800分辨率的小屏幕。</p>

<p>屏幕复制模式最常用于外接投影仪，现在我需要两个屏幕分别显示在各自的显示器中，并在两个屏幕中显示不同的内容（<em>也就是Windows下设置双屏显示时选择的扩展模式</em>）。</p>

<p>由于我将笔记本电脑放在外接显示器的左边，所以为了操作的自然，使用命令<code>xrandr --output VGA1 --right-of LVDS1</code>将外接显示器的输出在虚拟屏幕上的位置设置在笔记本电脑的输出的右边，示意图如下：</p>

<p><a href="https://picasaweb.google.com/lh/photo/twh2zzOQbv1YrOA_WPPxMg?feat=embedwebsite"><img src="https://lh5.googleusercontent.com/-otYnRD5mkv4/Tjqox_Fjl4I/AAAAAAAABzI/FxTmXNf-lk0/s640/xrandr%2525E8%2525A8%2525AD%2525E7%2525BD%2525AE%2525E9%25259B%252599%2525E5%2525B1%25258F%2525E9%2525A1%2525AF%2525E7%2525A4%2525BA.png" height="215" width="640" /></a></p>

<p>这样，两个显示器中就可以显示不同的内容了。由于属于同一个虚拟屏幕，当然也可以将一个窗口跨过边界从一个显示器拖到另一个显示器中。再使用命令<code>xrandr -q</code>查看现在的输出状态：</p>

<blockquote>
  <p>Screen 0: minimum 320 x 200, current 3200 x 1080, maximum 8192 x 8192
  LVDS1 connected 1280x800+0+0 (normal left inverted right x axis y axis) 261mm x 163mm
    1280x800       60.0<em>+   60.0     50.0 <br />
    1024x768       75.1     70.1     60.0 <br />
    800x600        72.2     75.0     60.3     56.2 <br />
    768x576        75.0     72.0     60.0 <br />
    640x480        72.8     75.0     60.0     59.9 <br />
  VGA1 connected 1920x1080+1280+0 (normal left inverted right x axis y axis) 509mm x 286mm
    1920x1080      60.0</em>+
    1680x1050      60.0 <br />
    1280x1024      75.0     60.0 <br />
    1440x900       59.9 <br />
    1280x960       60.0 <br />
    1024x768       75.1     70.1     60.0 <br />
    832x624        74.6 <br />
    800x600        72.2     75.0     60.3     56.2 <br />
    640x480        72.8     75.0     66.7     60.0 <br />
    720x400        70.1 <br />
  HDMI1 disconnected (normal left inverted right x axis y axis)
  DP1 disconnected (normal left inverted right x axis y axis)
  HDMI2 disconnected (normal left inverted right x axis y axis)
  DP2 disconnected (normal left inverted right x axis y axis)
  DP3 disconnected (normal left inverted right x axis y axis)</p>
</blockquote>

<p>使用xrandr做的设置只对当前的X会话有效，重启后恢复原样。使设置持久化的方法为修改xorg.conf或设置自动执行命令。据说有一些图形界面封装的xrandr的前端，完善的桌面环境如GNOME和KDE应该也是可以在图形界面中设置的，不过我所使用的XFCE目前似乎只能启用/禁用复制模式，而不支持在图形界面中设置扩展模式。</p>

<p>下面的脚本用来简化双屏显示的设置：</p>

```bash
#!/bin/bash

# Restart trayer and cairo-dock which I use on my desktop
restart_widget()
{
    killall trayer
    killall cairo-dock

    # Another script which starts trayer and set some options
    mytrayer
    cairo-dock &
}

set_dualhead()
{
    xrandr --output LVDS1 --auto
    xrandr --output VGA1 --auto
    xrandr --output VGA1 --right-of LVDS1
    restart_widget
}

set_lvds()
{
    xrandr --output VGA1 --off
    xrandr --output LVDS1 --auto
    restart_widget
}

set_vga()
{
    xrandr --output LVDS1 --off
    xrandr --output VGA1 --auto
    restart_widget
}

if [ $# -eq 0 ]; then
    set_dualhead
    exit
fi

case "$1x" in
    "lvdsx") set_lvds;;
    "vgax") set_vga;;
    *) echo 'Unknown parameter !';;
esac
```

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

