# UltraBlog.vim v3.5.0: 内建浏览器

<p>此前预览文章是通过调用系统默认的网络浏览器实现的，缺点是这些功能完备的浏览器启动速度不甚理想，尤其是火狐，谁用谁知道。</p>
<p>前两天看了TuxRadar的一个Podcast，几分钟内就可以用pywebkitgtk拼装出一个五脏俱全的网络浏览器，pywebkitgtk这个东西实在是非常的好用。不过我实测后发现这东西加载页面的速度比较慢，而且貌似是单线程的，因为图片都是放在最后才加载，不知道是这个绑定本身的问题，还是有API可以实现的。于是突然想到拿这个东西实现一个简单的浏览器，很适合在UB里预览文章。</p>
<p>增加了一个选项<strong>“ub_use_ubviewer”</strong>，默认值是1，即默认使用这个内建的浏览器预览文章。当这个选项的值被设为0时，仍然使用系统的默认浏览器，并且在Vim启动时不会加载内建的浏览器。这样做一是为了多一种选择，再一个也可以避开烦人的GTK警告：</p>
<blockquote>
<p>** (gvim:13629): WARNING **: Trying to register gtype 'GMountMountFlags' as enum when in fact it is of type 'GFlags'</p>
<p>** (gvim:13629): WARNING **: Trying to register gtype 'GDriveStartFlags' as enum when in fact it is of type 'GFlags'</p>
<p>** (gvim:13629): WARNING **: Trying to register gtype 'GSocketMsgFlags' as enum when in fact it is of type 'GFlags'</p>
</blockquote>
<p>这些警告是GTK或其它一些程序库的Bug造成的，虽然只在虚拟终端中启动Vim时会显示并且不影响使用，但总会有警告恐惧症患者会觉得这种东西很闹心。如果这样，就把这个选项的值设成0，用回巨型浏览器好了。</p>
<p>本次更新的全部内容如下：</p>
<ul>
<li>Feature: Add a tiny web browser to do previewing, which starts much faster than full-functional browsers like firefox and chromium. The later ones are still supported. The matter that whether or not to use the new previewer is controlled by a new option <strong>ub_use_ubviewer</strong>.</li>
<li>Bugfix: Issue 7: Keywords highlighting is disabled in item lists, even if searches are made manually.</li>
</ul>
<p>关于UB的详细信息在<a href="http://0x3f.org/?p=1894">这里</a>。</p>
<p>这是那个Podcast：</p>
<p><embed src='http://player.youku.com/player.php/sid/XMzg5MTc1OTI4/v.swf' quality='high' width='480' height='400' align='middle' allowScriptAccess='sameDomain' type='application/x-shockwave-flash'></embed></p>
<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

