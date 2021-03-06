# TTL线在Linux下的用法

虽然由于<a href="http://0x3f.org/?p=1544">我的H108B</a>没有TTL引脚而无法通过TTL线登入猫的<a href="http://en.wikipedia.org/wiki/BusyBox">BusyBox</a>系统，但TTL线在Linux下的使用方法仍然值得一叙，因为除此之外，它还有较为广泛的用途，例如为路由器刷<a href="http://en.wikipedia.org/wiki/OpenWrt">OpenWRT</a>系统等等。

TTL转USB端子是目前较为常见的此类设备，这得益于它的简单易用和便携性。在这类设备上最常见的就是台湾的Prolific Technology生产的<strong>PL2303</strong>：

<a href="http://picasaweb.google.com/lh/photo/fAc0kt6VmnFFTUY7IA-VrA?feat=embedwebsite"><img src="http://lh3.ggpht.com/_ceUJ_lBTHzc/TEHb1u6n7TI/AAAAAAAABbU/FA7aEZPTn-A/s400/C360_2010-07-17%2011-07-17.jpg" /></a>

这种芯片的驱动已被编译进Linux内核，如果使用的是较新版本的内核，则使用该芯片的TTL转USB端子会被自动识别，通常为<strong>/dev/ttyUSB0</strong>设备，使用<strong>lsusb</strong>命令可以查看：

<blockquote>
[lenin@archer ~]$ lsusb
Bus 008 Device 001: ID 1d6b:0001 Linux Foundation 1.1 root hub
Bus 007 Device 001: ID 1d6b:0001 Linux Foundation 1.1 root hub
Bus 006 Device 003: ID 067b:2303 Prolific Technology, Inc. PL2303 Serial Port
Bus 006 Device 001: ID 1d6b:0001 Linux Foundation 1.1 root hub
</blockquote>

将四根杜邦线连接到TTL转USB插头上，并接入相应设备的对应引脚。同一根线两端连接的引脚必须对应，另外，VCC引脚不能接线，否则会烧坏TTL转 USB芯片。

在计算机上使用串口通讯程序进行操控，常见的串口通讯程序有：<a href="http://en.wikipedia.org/wiki/Minicom">minicom</a>，<a href="http://en.wikipedia.org/wiki/Microcom">microcom</a>，picocom，tinyserial，xgcom。

以minicom为例，进入minicom的设置界面：

<blockquote>sudo minicom -s</blockquote>

先设置默认的通讯设备为ttyUSB0并设置硬件流控制（Hardware Flow Control）为No：

<a href="http://picasaweb.google.com/lh/photo/YnqotA0Fo6Z883T7aKvDcg?feat=embedwebsite"><img src="http://lh5.ggpht.com/_ceUJ_lBTHzc/TEMR710O6XI/AAAAAAAABb4/noM3E7ZEiuM/s800/2010-07-17.10%3A25%3A38.%E6%88%AA%E5%8F%96%E9%80%89%E5%8C%BA.01.png" /></a>

<a href="http://picasaweb.google.com/lh/photo/G2S6THC9IqvBChZGkJfyqQ?feat=embedwebsite"><img src="http://lh6.ggpht.com/_ceUJ_lBTHzc/TEMR7zBjcmI/AAAAAAAABb8/Z63_oSfjrnc/s400/2010-07-17.10%3A29%3A04.urxvt.01.png" /></a>

保存为缺省设置：

<a href="http://picasaweb.google.com/lh/photo/lY9b49woVJhLdkPJqViT3Q?feat=embedwebsite"><img src="http://lh4.ggpht.com/_ceUJ_lBTHzc/TEMR8Fjk3cI/AAAAAAAABcA/QnHscEzj1OY/s800/2010-07-17.10%3A29%3A35.%E6%88%AA%E5%8F%96%E9%80%89%E5%8C%BA.01.png" /></a>

启动minicom：

<blockquote>sudo minicom</blockquote>

理论上说，此时即可和设备进行通讯了。

Windows下要安装相应芯片的驱动，通讯程序一般用SecureCRT。

