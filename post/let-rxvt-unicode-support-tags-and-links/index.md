# 为rxvt-unicode开启标签和链接支持

<p>写完<a href="http://0x3f.org/?p=376">urxvt-unicode快速上手</a>，本以为已将urxvt的用法一网打尽，不料AndyWxy网友又找到了两个新的功能：使urxvt启用标签和在urxvt中打开网页链接。</p>

<p>标签功能很实用，一般为了达到复用终端窗口的目的会采用两种方式：一是配合screen使用，另一个就是启用标签。然而前者有一个缺点就是不直观，标签页恰好能弥补这个缺陷。urxvt不愧是个功能强大的终端工具，如果在编译时开启perl支持，则urxvt可启用多标签功能。用法如下：</p>

<p>一是在启动的时候加入命令行参数：</p>

<blockquote>
  <p>urxvt -pe tabbed</p>
</blockquote>

<p>二是在配置文件“.Xresources”中添加如下配置信息：</p>

<blockquote>
  <p>URxvt.perl-ext-common: default,tabbed</p>
</blockquote>

<p>则默认情况下执行urxvt就会打开多标签功能。urxvt的标签支持使用鼠标操作，同时可以使用Ctrl+Shift+左右箭头来切换标签页，使用Ctrl+Shift+向下箭头开启新标签。</p>

<p>另外一个功能就是可以通过在urxvt中的链接上点击鼠标左键来通过设定的浏览器打开之。首先在“.Xresources”文件中添加如下内容：</p>

<blockquote>
  <p>URxvt.urlLauncher: firefox
  URxvt.matcher.button: 1</p>
</blockquote>

<p>然后使用如下命令打开urxvt：</p>

<blockquote>
  <p>urxvt -pe matcher</p>
</blockquote>

<p>即可。也可以在配置文件中添加上述内容之后再添加一行：</p>

<blockquote>
  <p>URxvt.perl-ext-common: matcher</p>
</blockquote>

<p>此后即默认开启在终端窗口中打开链接的功能。注意修改“.Xresources”文件后需要执行如下命令才能使修改后的配置文件生效：</p>

<blockquote>
  <p>xrdb ~/.Xresources</p>
</blockquote>

