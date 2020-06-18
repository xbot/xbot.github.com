# Dokan SSHFS：Windows下通过SSH挂载Linux主机为本地分区

<p>Linux一般使用Samba向Windows共享资源。另一个办法就是使用SSH。</p>

<p><a href="http://dokan-dev.net/en/">Dokan</a>是一个为Windows提供实现新文件系统的开发库，而<a href="http://dokan-dev.net/en/download/#sshfs">Dokan SSHFS</a>是在此基础上实现的挂载SSH通道为本地分区的文件系统。必须先安装Dokan，然后再安装Dokan SSHFS。</p>

<p><a href="http://picasaweb.google.com/lh/photo/d6J9vTU8cvK2KEplEo0ZaQ?feat=embedwebsite"><img src="http://lh4.ggpht.com/_ceUJ_lBTHzc/TThWJn7FrzI/AAAAAAAABic/Et7kroeInF8/s400/sshfs.png" height="338" width="400" /></a></p>

<p>如果从安装SSH服务做起，步骤是：</p>

<ol>
<li>在Linux主机上安装openssh</li>
<li>修改Linux主机的<strong>/etc/hosts.allow</strong>，加入<code>sshd: ALL</code>，以允许外部访问</li>
<li>启动sshd守护进程</li>
<li>在Windows主机上安装Dokan和Dokan SSHFS</li>
<li>通过SSHFS的图形配置工具挂载SSH通道</li>
</ol>

<p>这种方式的好处是简单，但是存在安全隐患，不管怎样，公布SSH连接绝非好事，这意味著接入用户可以远程控制Linux主机，尤其是当SSH账户的权限较高时。因此，这种方式只适用于虚拟机或家庭网络，例如对Host-Guests共享支持不佳的KVM虚拟机来说就是个好的选择。即便如此，也要注意相应的Windows主机的安全防范，否则，入侵者可以通过Windows主机做跳板来进入Linux主机。</p>

