# Archlinux升级到GNOME2.30后的光标主题问题

<p>Archlinux下的GNOME升级到2.30后，若启用了Compiz，则光标主题只能使用X默认的主题，无法自定义。尚不清楚是GNOME新版本自身的问题还是仅限于Arch发行版。</p>

<p>在官方提供解决方案或更新之前，可以先使用Xdefault或Xresource文件实现。</p>

<p>以后者为例，编辑用户主目录下的<strong>.Xresource</strong>，在文件最后添加如下内容：</p>

<blockquote>
  <p>Xcursor.theme: faber-anthracite-shd-m</p>
</blockquote>

<p><strong>faber-anthracite-sdh-m</strong>是我使用的光标主题的文件夹名。</p>

<p>然后执行如下命令：</p>

<blockquote>
  <p>xrdb ~/.Xresource</p>
</blockquote>

<p>最后注销并重新登录即可。</p>

