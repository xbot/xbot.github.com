# rxvt-unicode的真透明

<div class="illustration_right">
<a href="http://picasaweb.google.com/lh/photo/J2FyvgBhXopEgPlX9whJ3w?feat=embedwebsite"><img src="http://lh5.ggpht.com/_ceUJ_lBTHzc/Saghr1cRaoI/AAAAAAAAA0o/fye5JZl3Pxc/s288/2009-02-28-012206_1280x800_scrot.png" /></a>
</div>

<p>nacre同学说，urxvt是可以实现真透明的。起初我以为是要用<a href="http://www.forchheimer.se/transset-df/">transset-df</a>来实现，但这个东西一般需要手动操作，不具有太大的实用性，而且会把整个窗口透明化。不过后来发现真的不需要用它来画蛇添足，有<a href="http://wiki.archlinux.org/index.php/Xcompmgr">xcompmgr</a>足矣。</p>

<p>因为我用<a href="http://en.wikipedia.org/wiki/Openbox">openbox</a>，要实现窗口的阴影和动画效果，xcompmgr是必须的，我把它设成了开机自启动。比起xcompmgr默认的参数值，下面这条定制的命令实现了简洁的阴影和合理的渐隐渐显时间，因此提供了一个各方面都比较均衡、合理的桌面体验：</p>

<blockquote>
  <p>xcompmgr -Ss -n -Cc -fF -I-10 -O-10 -D1 -t-3 -l-4 -r4</p>
</blockquote>

<p>然后在.Xresources中添加以下两行：</p>

<blockquote>
  <p>URxvt.depth:32
  URxvt.background:rgba:0000/0000/0000/dddd</p>
</blockquote>

<p>最后当然要执行一下：</p>

<blockquote>
  <p>xrdb ~/.Xresources</p>
</blockquote>

<p>此后直接启动urxvt即可。</p>

<p>这里面最有意思的就是background项的配置，它有两种形式，一种是：</p>

<blockquote>
  <p>URxvt.background:[80]black</p>
</blockquote>

<p>还有一种就是前面提到的形式。</p>

<p>第一种形式中，中括号里的数字表示半透明度对应的百分比，括号外是颜色名称；第二种形式提供了比第一种更多的色彩选择，四组数字都是十六进制数，前三组是RGB颜色值，最后一组是半透明度，数值越大，透明度越低。</p>

<p>在查阅“man urxvt”的时候，发现urxvt的man pages里的内容真是异常丰富，大部分功能都讲解得言简意赅。以往还抱怨这个东西在网上连个健全的文档都找不到，原来全在这儿呢，真不知道以往无数次地man的时候为什么没有注意到这些，难道man了rxvt了？</p>

<p><em>PS：这样实现urxvt的真半透明后，貌似xcompmgr实现的阴影在urxvt身上就消失了，求解中……</em></p>

