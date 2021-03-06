# VirtualBox的Host Interface网络接入方式的配置

<p><a href="http://zh.wikipedia.org/wiki/VirtualBox">VirtualBox</a>安装后默认的网络接入方式是<a href="http://zh.wikipedia.org/wiki/%E7%BD%91%E7%BB%9C%E5%9C%B0%E5%9D%80%E8%BD%AC%E6%8D%A2">NAT</a>，也是最简单的一种。但NAT网络中的虚拟机系统不具有和主机同一网段的IP地址，不能和主机直接通信。这对于开发者来说是不适用的。</p>

<p>Host Interface网络接入方式可以使虚拟机系统获得与主机同一网段的IP地址，因此也使得虚拟机系统像主机所在的局域网中的一台真实的计算机一样，可以和其它计算机通信。</p>

<p>对于Linux平台的VirtualBox，可以有两种方式实现Host Interface网络。一是使用系统网桥，二是使用VirtualBox内建的机制。</p>

<p>第一种方式复杂但通用，尤其是对于2.1.0以前的版本来说，这是唯一的途径。从2.1.0开始，VirtuaBox内建了支持Host Interface的机制，这极大地简化了此类型网络的配置。</p>

<p>首先，加载<strong>vboxnetflt</strong>模块：</p>

<blockquote>
  <p>sudo modprobe vboxnetflt</p>
</blockquote>

<p>然后在VirtualBox中配置虚拟机的网络连接方式，选择“<strong>Bridged Adapter</strong>”。</p>

<p>最后启动虚拟机即可。</p>

<p>另外，若虚拟机系统是精简版的Windows，网卡驱动可能不会自动安装，这时需要另外下载网卡的驱动并安装。</p>

<p>为方便起见，可将vboxnetflt模块加入到开机自动启动的模块列表中，每种发行版设置自启动模块的位置和方法不一样，在Archlinux下，是在<strong>/etc/rc.conf</strong>文件中的<strong>modules</strong>行中设置。</p>

<p><em>参考文章：<a href="http://wiki.archlinux.org/index.php/VirtualBox">Archlinux Wiki: VirtualBox</a></em></p>

