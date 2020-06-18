# rxvt-unicode快速上手

<!--rxvt-unicode快速上手-->
<p>
&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.rxvt.org/">rxvt</a>是我最喜欢的虚拟终端，它有以下优点：
</p><p>
&nbsp;&nbsp;&nbsp;&nbsp;<b>轻量</b>：体积小，启动速度快，占用系统资源极低<br/>
&nbsp;&nbsp;&nbsp;&nbsp;<b>美观</b>：各种外观（如颜色、字体、半透明等）均可定制<br/>
&nbsp;&nbsp;&nbsp;&nbsp;<b>功能强</b>：具备一个终端工具应当有的各种实用功能
</p><p>
&nbsp;&nbsp;&nbsp;&nbsp;然而它很难被初学者接受，一是因为它的缺省外观很难看，而自身又不提供图形界面的配置工具，只能通过修改配置文件来设置，然而网上相关的资料却非常少；二是因为在rxvt中使用复制和粘贴非常“不方便”，它并不支持人们已经习惯的Ctrl+C和Ctrl+V的复制、粘贴方式，这也是网上关于rxvt问得最多的问题；最后一个门槛是它对中文等东亚文字的支持不好。
</p><p>
&nbsp;&nbsp;&nbsp;&nbsp;其实rxvt以上三个所谓的门槛都是初学者对它的误解。首先rxvt的配置文件非常简单，只要学过英语的人都能看明白，通过简单的配置就可以使之变得非常漂亮，丝毫不逊色于<a href="http://en.wikipedia.org/wiki/Konsole">Konsole</a>、<a href="http://en.wikipedia.org/wiki/GNOME_Terminal">Gnome-terminal</a>等主流终端。
</p><p>
&nbsp;&nbsp;&nbsp;&nbsp;其次，rxvt虽然不支持Ctrl+C和Ctrl+V的复制、粘贴，但是它有自己的一套操作方式。Linux下普遍使用的图形界面均为<a href="http://www.x.org/">X11</a>，而X11支持一种独特的复制粘贴方式，即如果你在另一个程序比如文本编辑器中使用鼠标拖动来高亮一段文字后，不用进行任何操作，此时选中的内容已经复制到剪贴板中了，随后在rxvt中单击鼠标中键即可将选中内容粘贴到其中，此外，如果鼠标没有中键，可以同时按下左右键以达到同样的效果，还可以使用Shift+Insert组合键来完成粘贴；反之，从rxvt中向外复制内容同样如此。
</p><p>
&nbsp;&nbsp;&nbsp;&nbsp;最后，虽然rxvt对东亚文字支持不好，但是rxvt有几个修改版，其中一个就是<a href="http://software.schmorp.de/pkg/rxvt-unicode.html">rxvt-unicode</a>。顾名思义，rxvt-unicode支持unicode编码，因此对东亚文字的支持完全没有问题。安装完成后，使用urxvt即可打开之。
</p><p>
&nbsp;&nbsp;&nbsp;&nbsp;此外，rxvt-unicode还有一个很好的特性，它可以工作在C/S模式。使用urxvtd启动rxvt的后台程序，此后任何时候使用urxvtc即可打开一个rxvt终端，所有的rxvtc共用一个urxvtd，因此在同时运行的终端比较多的时候，在内存占用上要比全部使用urxvt少一些。不过据我粗略计算，如果打开的终端不是非常多（比如说几十个），那么使用普通模式和使用C/S模式对内存的占用没有太大的差距，因为普通模式下的urxvt占用内存本身就非常少。在我看来使用C/S模式的最大好处是启动速度比普通模式要快一些。
</p><p>
&nbsp;&nbsp;&nbsp;&nbsp;关于rxvt的美化和设置，可以参看我以前写的一篇<a href="http://0x3f.org/?p=316">文章</a>。下图是我现在使用的rxvt的截图：
</p><p>
<a href="http://picasaweb.google.com/lenin.lee/Weblog/photo#5173912470992319602"><img src="http://lh5.google.com/lenin.lee/R81t-tR19HI/AAAAAAAAASc/DNDprQnOY-A/s400/urxvt.png" /></a>
</p><p>
&nbsp;&nbsp;&nbsp;&nbsp;以下是我当前的rxvt配置文件的内容：
</p><p>
<blockquote>
!Xft.dpi:96
!fontforge.FontView.FontFamily:wenquanyi bitmap song

Rxvt.geometry:192×144
Rxvt.background:#2e2e2e
Rxvt.foreground:antiquewhite
Rxvt.colorBD:yellow
Rxvt.colorUL:green
Rxvt.multichar_encoding:utf-8
Rxvt.scrollBar:Fault
Rxvt.scrollBar_right:True
Rxvt.scrollBar_floating: True
Rxvt.scrollstyle: next
Rxvt.saveLines:10000
Rxvt.color0:black
Rxvt.color1:red3
Rxvt.color2:springgreen
Rxvt.color3:wheat3
Rxvt.color4:navy
Rxvt.color5:magenta4
Rxvt.color6:steelblue1
Rxvt.color7:gray85
Rxvt.color8:gray10
Rxvt.color9:SkyBlue3
Rxvt.color10:chartreuse3
Rxvt.color11:lightgoldenrod2
Rxvt.color12:SkyBlue1
Rxvt.color13:pink1
Rxvt.color14:lightblue1
Rxvt.color15:#dbeff9

Rxvt.font:xft:Vera Sans YuanTi Mono :size=10,xft:Monospace:size=10

Rxvt.menu:/etc/X11/rxvt.menu
Rxvt.preeditType:Root
Rxvt.transparency:255
</blockquote>
</p>

