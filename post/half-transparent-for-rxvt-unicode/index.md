# rxvt-unicode的半透明方案

<!-- urxvt的半透明方案 -->
<p>&nbsp;&nbsp;&nbsp;&nbsp;rxvt是个快速且节省内存的模拟终端，原版rxvt对中文等非字母语言的支持不好，所以它有许多修改版，rxvt-unicode-ml是比较适合中国人使用的rxvt修改版。</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;rxvt轻量但不简单，它有许多设置选项和启动参数，用户可以在命令行使用“<font color="#036803">rxvt -help</font>”查看其常用启动参数，使用“<font color="#036803">rxvt --help</font>”查看更为详细的启动参数。</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;通过修改用户主目录下的“<font color="#a73800">.Xresources</font>”文件可以配置urxvt的行为，从而不必每次都加启动参数，我的"<font color="#a73800">.Xresources</font>"文件内容如下：</p>
<blockquote><font color="#036803">Rxvt.background:white<br>
Rxvt.foreground:black<br>
Rxvt.colorBD:yellow<br>
Rxvt.colorUL:green<br>
Rxvt.multichar_encoding:gb2312<br>
Rxvt.scrollBar:Fault<br>
Rxvt.scrollBar_right:True<br>
Rxvt.scrollBar_floating: True<br>
Rxvt.scrollstyle: next<br>
Rxvt.saveLines:10000<br>
Rxvt.color0:black<br>
Rxvt.color1:red3<br>
Rxvt.color2:springgreen<br>
Rxvt.color3:wheat3<br>
Rxvt.color4:navy<br>
Rxvt.color5:magenta4<br>
Rxvt.color6:steelblue1<br>
Rxvt.color7:gray85<br>
Rxvt.color8:gray10<br>
Rxvt.color9:SkyBlue3<br>
Rxvt.color10:chartreuse3<br>
Rxvt.color11:lightgoldenrod2<br>
Rxvt.color12:SkyBlue1<br>
Rxvt.color13:pink1<br>
Rxvt.color14:lightblue1<br>
Rxvt.color15:#dbeff9<br>
Rxvt.font:xft:Vera Sans YuanTi Mono :size=10,xft:Monospace:size=10<br>
Rxvt.menu:/etc/X11/rxvt.menu<br>
Rxvt.preeditType:Root<br>
Rxvt.geometry:192×174<br>
Rxvt.transparency:255</font></blockquote>
<p>&nbsp;&nbsp;&nbsp;&nbsp;这个配置文件是白底黑字，当然可以修改为全透明或者半透明，不过，只要在启动时加入启动参数就可以实现全透明：</p>
<blockquote><font color="#036803">urxvt -tr</font></blockquote>
<p>&nbsp;&nbsp;&nbsp;&nbsp;也可以实现半透明：</p>
<blockquote><font color="#036803">urxvt -fg lightgray -bg black -bc -tr -tint lightgray -sh 60 -sr</font></blockquote>

