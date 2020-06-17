---
layout: post
title: UBUNTU 6.10 Edgy Eft 入门全程导用
slug: ubuntu 610 tutorial
date: 2007-04-08 00:00:00
categories:
- 计算机
tags:
- Ubuntu
- 教程
---

<a HREF="http://forum.ubuntu.org.cn/viewtopic.php?t=32798&amp;highlight=" TARGET="_blank">http://forum.ubuntu.org.cn/viewtopic.php?t=32798&amp;highlight=</a>

目录 
----

一、安装Edgy与硬盘的分区  
（1）安装与分区--------------------------------------------------------------------------------------------3  
（2）多硬盘需要注意的一点---------------------------------------------------------------------------------3  
（3）Linux下硬盘分区的概念-------------------------------------------------------------------------------4  
二、改变账户操作理念----------------------------------------------------------------------------------4  
三、修改源并配置网络  
（1）源及其使用方法----------------------------------------------------------------------------------------5  
（2）配置ADSL网络----------------------------------------------------------------------------------------6  
四、系统的中文化与输入法  
（1）系统的中文化------------------------------------------------------------------------------------------6  
（2）中文字体的美化----------------------------------------------------------------------------------------6  
（3）中文输入法的安装与配置------------------------------------------------------------------------------8  
（4）主要程序界面的汉化-----------------------------------------------------------------------------------11  
（5）Abiword中文乱码的解决办法-------------------------------------------------------------------------11  
五、多媒体播放器的安装与配置  
（1）万能播放器 mplayer----------------------------------------------------------------------------------12  
（2）音频播放器 audacious 及 mp3 文件 tag 乱码的解决-------------------------------------------------12  
（3）媒体解码库 gstreamer--------------------------------------------------------------------------------13  
六、网络工具的安装与配置  
（1）Opera 的安装与配置及中文输入法问题的解决---------------------------------------------------------13  
（2）即时通讯程序------------------------------------------------------------------------------------------14  
（a）集成即时通讯工具 gaim-------------------------------------------------------------------------14  
（b）最逼真的 QQ 工具------------------------------------------------------------------------------15  
（c）功能最强大的 QQ 工具 Eva---------------------------------------------------------------------16  
（d）多协议即时通讯工具 Kopete-------------------------------------------------------------------16  
（3）新闻阅读----------------------------------------------------------------------------------------------16  
（4）邮件客户端--------------------------------------------------------------------------------------------17  
（5）下载工具----------------------------------------------------------------------------------------------17  
（6）p2p 工具----------------------------------------------------------------------------------------------17  
七、编辑器、编译环境与词典  
（1）编辑器------------------------------------------------------------------------------------------------17  
（2）编译环境----------------------------------------------------------------------------------------------17  
（3）词典---------------------------------------------------------------------------------------------------18  
八、NVIDA 显卡驱动与 Beryl 的安装  
（1）nvida 显卡驱动的安装--------------------------------------------------------------------------------18  
（2）安装 Beryl--------------------------------------------------------------------------------------------20  
（3）Splash Screen 的安装--------------------------------------------------------------------------------22  
九、Wine 的安装---------------------------------------------------------------------------------------22  
十、虚拟机 Vmware-----------------------------------------------------------------------------------23  
十一、磁盘分区的挂载  
（1）挂载一个原先存在而没有挂载的分区------------------------------------------------------------------23  
（2）新加入一块还没有分区的全新的硬盘------------------------------------------------------------------24  
十二、游戏  
（1）linux 下面的游戏-------------------------------------------------------------------------------------25  
（2）win 下的游戏-----------------------------------------------------------------------------------------25  
十三、一些常用工具  
（1）读写 ntfs 分区----------------------------------------------------------------------------------------25  
（2）java 日记本-------------------------------------------------------------------------------------------26  
（3）图片管理 Picasa--------------------------------------------------------------------------------------26  
（4）光碟烧录 GnomeBaker-------------------------------------------------------------------------------26  
（5）CHM 阅读器------------------------------------------------------------------------------------------26  
（6）数学程序----------------------------------------------------------------------------------------------27  
十四、Linux 下程序的安装  
（1）deb 软件包的安装------------------------------------------------------------------------------------27  
（2）rpm 软件包的安装------------------------------------------------------------------------------------27  
（3）使用源码进行安装------------------------------------------------------------------------------------28  
（4）绿色软件---------------------------------------------------------------------------------------------29  
十五、其它 GUI 的安装  
（1）KDE--------------------------------------------------------------------------------------------------29  
十六、进阶技巧  
（1）关于 Grub 的 FAQ------------------------------------------------------------------------------------29  
（2）桌面图标隐藏、显示设置------------------------------------------------------------------------------31  
（3）在/var/cache/apt/archives 里面发现大量原来使用 apt-get 下来的 deb 包，太占空间了，能不能删掉？31  
（4）gvim 的字体太难看，怎样设置为雅黑?---------------------------------------------------------------31  
（5）为什么键盘上的左右箭头不能用了？------------------------------------------------------------------31  
（6）有些常用命令记不住怎么办?--------------------------------------------------------------------------32  
（7）快速搜索文件-----------------------------------------------------------------------------------------32  
（8）“不打开”音乐播放器预听 mp3---------------------------------------------------------------------33  

一、安装Edgy与硬盘的分区 

（1）安装与分区 

安装就不用多说了，使用LiveCD安装非常简单，如果你装过Windows系统，这个将比它更简单。 

首先将LiveCD的安装盘放入光驱，在BIOS里面设置成从光驱启动，使用LiveCD光盘启动后，系统桌面上有个“Install”的图标，双击后启动安装程序，一步一步地配置选项就行了。 

关键是硬盘分区的问题。 

如果你是想在硬盘上原有的一个分区上安装ubuntu，那么首先应确保这个分区上的重要数据都已被转移，然后在选择磁盘分区的时候选择手动分区，再将这个分区删除，在删除分区后得到的空间上新建一个swap分区，这个相当于windows下面的虚拟内存，一般要求不小于你的内存容量，我的内存是256M，划分的swap是512M。 

然后在剩余的空间上建立一个主分区，用来安装系统所必需的文件，这个分区建议不低于5个G，当然，如果你的硬盘容量很大，还可以划分主分区以外的逻辑分区。 

（2）多硬盘需要注意的一点 

如果你有两块以上的硬盘，并想在不同的硬盘上安装不同的系统，那么需要注意一点，就是硬盘的连接顺序。 

每块硬盘都在其电源线与数据线之间有两排共八个针，每两个针是一组，通过一个接头用来决定磁盘的一些功能。其中有一组是用来决定该磁盘是不是主盘的，有接头的是主盘，没有接头的是从盘，主机中只有一块硬盘的无需在意这个，有没有这个接头都能用，但若是有多块硬盘，那么必需只有一块是主盘，否则有两块主盘的话系统启动会出问题。 

说了这么多，现在该切入正题了。 

对于多块硬盘，如果先前使用的是windows，那么它对硬盘接在总线上的顺序没什么要求。但对于linux，它要求主盘必须接在总线上距离主板上IDE接口近的那个接口上，而从盘接在总线上离IDE接口远的地方。拿我来说，我有两块硬盘，原来就一块主盘，就随便接在了总线上的末端，后来增加一块从盘时，就接在了剩下的那个位于总线中间的接口上，在windows下没有出过任何问题。后来装了ubuntu,发现总是正在操作，突然就死了机，系统没有任何反应，按了主机上的reset键后，当出现启动画面时，又死了，有时需要从windows启动，然后再使用热启动，有时候要关了机再开，然后才可以进入系统。 

起先我还以为是edgy不稳定，后来发现是硬盘接口惹的祸，于是把主盘和从盘的接口顺序对调了一下，问题迎刃而解。 

（3）Linux下硬盘分区的概念 

与windows的fat、fat32和ntfs文件系统格式不同，linux的ext2和ext3文件系统非常的高明和优秀，它具有比windows文件系统更好的安全性、稳定性和低碎片率，它产生的碎片率一般不高于0.4%,几乎不影响系统性能。所以，windows下面那套多分几个区以方便文件管理的思想可以扔到火星上去了。 

另外linux对磁盘分区的管理采取“挂载”的概念，它把所有的分区都挂载到主分区的一个一个的文件夹上，任何一个文件在硬盘上的路径都是按文件夹的路径划分，而不是windows下的先按C、D、E、F分区，再按文件夹路径的方法。 

具体的说，例如我的一首音乐文件在windows下的存放格式为“C:\Music\Classic\一意孤行.mp3”,在linux下就可能是“/home/lans/Music/Classic/一意孤行.mp3”。 

所以在划分完磁盘分区后，还有一个挂载磁盘分区的步骤，也就是将划分好的分区挂到某个目录下，swap分区无须挂载，主分区应挂载在“/”下面，其它分区可自行选择目录，建议将“/home”挂到主分区外的别的逻辑分区上（如果你分区的时候除了主分区还划分了别的逻辑分区的话），因为/home下面是你的帐户所在的主目录，而你经常使用和存放的文件大多数也都放在这里，即使以后要重装系统，也不必担心会丢失。 

二、改变帐户操作理念 

在windows下面，大家一般使用的都是有超级用户权限的帐户。也就是说，使用者可以为所欲为，你可以随意地、不受限制地修改硬盘中的所有数据。这就给了木马病毒以可乘之机，另外用户的任何失误都可能造成难以挽回的灾难，如果平常使用一般权限的帐户的话，由于windows本身的问题，又多有不便。 

在linux下面，也有这样的超级用户，叫“根用户”，帐户名为“root”,但是由于linux的功能太强大了，一条命令就可以毁了整个系统。所以一般不使用，而是使用在安装系统时由用户自己设定的帐户，这个帐户没有超级用户的权限，只拥有对主目录（即”/home/用户名”）目录下文件的读写权限，对此外的文件只有读权限，没有写权限。 

不过不必担心会遇到像windows下的困难，下面会看到，我们对系统的操作会大量使用控制台这个东西，它就相当于windows下的命令行。不过linux的命令行实在太强大了，远非DOS那个玩具可比，所以结合命令行的灵活强大和GUI的美观方便使得linux才是操作系统中的王道。 

此外，如果对所做的操作很有把握，有时候使用root权限在图形界面下工作会比较方便，这需要使用root帐户登录，ubuntu默认关闭root在登录界面登录，所以需要开启它。 

System -> Administration -> Login Screen Setup 

系统 -> 系统管理 -> 登录屏幕设置 

Login Screen Setup 

登录屏幕设置 

Security Tab -> Options -> Allow root to login with GDM ('''Checked''') 

安全 -> 选项 -> 允许 root 通过 GDM 登录（“勾选”） 

另外，还要修改root的密码，打开“系统”－“系统管理”－“用户和组” 

在里面修改root的密码。 

三、修改源并配置网络 

（1）源及其使用方法 

什么是源呢。 

在windows下面，要安装程序的话，首先要选择一个你喜欢的软件下载站，在搜索引擎里面输入该软件的名字，搜到后还要选择一个快速的下载链接，使用你喜欢的下载工具，下载回来以后再执行该安装程序。很多时候还要重新启动系统，等等。 

但是在linux下，这一切将会变得十分简单，你只需要在系统自带的“新立得”软件包管理器里选择想要使用的程序，然后连接上网即可。如果你事先知道想要安装的程序的名字，那么更简单，你只需要在控制台输入一条简单的命令即可。剩下的事，什么都不用你管了。这一切都是因为系统会自动从源里下载并自动安装这个软件。 

系统安装后，自带的有一个源，但是由于需要选择一个下载速度快的源，另外安装别的一些软件还需要有特殊的源，所以需要修改一下源。 

打开控制台输入命令： 

sudo gedit /etc/apt/sources.list 
sudo 就是 super user do 的意思 

如果你安装时选择的是中文的话，那么缺省的源是位于国内的ubuntu亚洲官方源，可以在源文件上添加多个源，但速度慢的源最好不要加，否则速度将会非常的慢。wiki上的快速指南上有很多源可以选择。需要注意的是不同版本的ubuntu有不同的源，千万不要选错了源，ubuntu6.10的代号是edgy，所以打开wiki上edgy的快速指南就行了。 

添加了选定的源后，保存并退出gedit。然后连上网，在控制台输入： 

sudo apt-get update 

以更新软件列表。 

sudo apt-get dist-upgrade 

这个是为了更新系统 

（2）配置ADSL网络 

对于一直在线的用户，网络配置很容易，这里不作赘述，下面介绍一下ADSL上网的要点。 

在控制台执行 

sudo pppoeconf 

然后按着它的提示一步一步操作即可，需要注意的是在输入ADSL帐户的时候，输入框里面缺省的有几个字，先把它们删掉再输入。后面它会提示你是不是开机自动连接上网，如果你的ADSL是按时收费，一定要选择no。 

以后如果要上网，只需执行 

pon dsl-provider 

如果要断开连接，执行 

poff 

四、系统的中文化与输入法 

（1）系统的中文化 

ubuntu是一个国际化的操作系统，它可以兼容多个语言平台，不像windows那样还要分成多个语言的版本。 

如果在安装操作系统的时候已经选择了安装中文，那么系统安装后就是简体中文的。如果安装时没有在意而装了英文版，或者想在多个语言版本之间切换，那么可又打开菜单中“系统”里面的“系统管理”，然后点选里面的“语言支持”选择即可。 

（2）中文字体的美化 

ubuntu的中文界面并不太美观，似乎是开源的字体还不完善，为了养眼，不得不使用一些商业字体来美化界面了。有一种也是比较流行的字体美化方案是使用苹果机的Mac系统的圆体，但是我试过后发现它只能在大于等于十四号的字体才会显示为圆体，而正常的10号却显示的是宋体，圆体虽好看，但只好望洋兴叹。这里介绍一下我的美化方案：方正黑体＋微软雅黑 

方正黑体可以到百度上搜一下，微软雅黑建议到[远景论坛](www.vistafans.com)下载。下面以我的系统为例,我的帐户名是lans,下载回来的东西都放在主目录下面： 

分别是“FZHTJW.ttf””msyh.ttf”“msyhbd.ttf”“segoeui.ttf”“segoeuib.ttf”“segoeuipr.ttf”“segoeuiprb.ttf”,将它们放在主目录下新建的font目录下面 

执行 

sudo mv /home/lans/font/* /usr/share/fonts/truetype 

将其挪入系统的字体文件夹 

备份现有的 fonts.conf 

sudo cp /etc/fonts/fonts.conf /etc/fonts/fonts.conf.bak 

以后若要恢复原来的配置，只需执行 

sudo cp /etc/fonts/fonts.conf.bak /etc/fonts.conf 

即可 

修改字体配置文件 

sudo gedit /etc/fonts/fonts.conf 

在里面加入下面的内容： 

```html
<alias> 
<family>Segoe UI</family> 
<prefer> 
<family>Segoe UI</family> 
<family>Microsoft YaHei</family> 
</prefer> 
</alias> 
<alias> 
<family>FZHei-B01S</family> 
<prefer> 
<family>FZHei-B01S</family> 
</prefer> 
</alias> 
```

在“系统”－“首选项”－“字体”里选择：应用程序字体－方正黑体－10；窗口标题－微软雅黑粗体－10；其余都是微软雅黑－10即可。如果是LCD显示器，为了更好的显示效果，建议在字体渲染里面选择“次象素平滑”。并安装类似于windows下面的cleartype的显示优化程序,执行 

sudo gedit /etc/apt/sources.list 

添加以下的源 

deb http://www.elisanet.fi/mlind/ubuntu edgy fonts 

deb-src http://www.elisanet.fi/mlind/ubuntu edgy fonts 

然后sudo apt-get update 

sudo apt-get dist-upgrade 

然后刷新字体缓存 

sudo fc-cache -f -v 

执行完后，按Ctrl+Alt+BackSpace重新启动X-window即可。 

（3）中文输入法的安装配置 

a、scim 

如果你在安装的时候就一直在线，那么中文输入法已经完全安装，按Ctrl+Space即可使用。若不是这样，那么系统安装的不是一个完整的中文输入法。 

中文输入法比较有名的有两个，系统默认安装的是scim，我们就说这个。 

执行 

sudo apt-get install scim-pinyin im-switch scim-tables-zh scim-qtimm 

其中scim-pinyin和scim-tables-zh可选其一，也可都安装，前者是拼音输入方案，后者包括常用的二笔和五笔等方案。 

安装成功之后按Ctrl+At+BackSpace重启X-window即可使用。 

b、fcitx 

fcitx是一款很好用的中文输入法，听到很多人说scim与系统中许多软件存在冲突，我在使用中没有发现什么问题，但是使用fcitx后确实爱不释手，因为这个工具功能上要比scim强得多，很像我以前在windows下面最喜欢使用的极点中文。 

sudo apt-get install im-switch fcitx 

如果是使用上面的方法安装，直接执行下面第二步，如果是使用下载的安装包安装，执行下面： 

1、如果是使用下载的安装包安装，还应该执行下面的命令： 

sudo chmod -R 755 /usr/share/fcitx 

任意位置新建立一个文件叫 fcitx，内容如下： 

XIM=fcitx 

XIM_PROGRAM=/usr/bin/fcitx 

XIM_ARGS="" 

GTK_IM_MODULE=XIM 

QT_IM_MODULE=XIM 

DEPENDS="fcitx" 

复制到 /etc/X11/xinit/xinput.d/ 下以保证im-switch 能找到它 

2、执行 代码: 

sudo im-switch -s fcitx 

和 代码: 

im-switch -s fcitx 

将二个帐号的输入法都换成 fcitx 

3，如果是在英文local 下而无法调出输入法（XIM）(如果是中文local 不必进行此操作)修正步骤如下： 

编辑 /etc/gtk-2.0/gtk.immodules 文件（sudo gedit /etc/gtk-2.0/gtk.immodules），在xim 的 local 增加 en 也就是说： 代码: 

"/usr/lib/gtk-2.0/2.4.0/immodules/im-xim.so" 

"xim" "X Input Method" "gtk20" "/usr/share/locale" "ko:ja:th:zh" 

改成 代码: 

"/usr/lib/gtk-2.0/2.4.0/immodules/im-xim.so" 

"xim" "X Input Method" "gtk20" "/usr/share/locale" "en:ko:ja:th:zh" 

保存退出，重启x-window就好了。 

其它参考： 

1，不希望使用一大堆用不着的输入法，可以关闭它： 

sudo gedit /usr/share/fcitx/data/tabels.conf 

把里面不要的输入法整段删除，至于双拼可以在下面说的config 里关闭 

2，在打字时要输入,.号经常按不出来，可以这样： 

~/.fcitx 目录下有一个 config 文件（启动至少一次fcitx 才会自动建立）可以配置翻页键把里面的 ,. 二个删除，不然在出联想时打不出,. 号 

3，不想让它一直显示在那，可以这样： 

~/.fcitx 目录下有一个 config 文件可以配置 代码: 

主窗口隐藏模式=1 

4，在英文local 下 fcitx 打出的字全方块，可以这样： 

修改上面说的这个config 里的 代码: 

显示字体(中)=字体名 

5，使用ctrl+左shift键可以切换输入方案 

6，其它应用技巧可以到fcitx的主页下载其最新版的使用说明书（pdf文档）。 

（4）主要程序界面的汉化 

据说如果安装系统的时候一直在线，那么不但输入法会完整安装，而且各种软件的简体中文包都会安装，如果像我这样不是一直在线的，那么需要到源上下载相应的中文包，才可以实现openoffice、firefox界面的中文显示。 

sudo apt-get install mozilla-firefox-locale-zh-cn openoffice.org-help-zh-cn openoffice.org-l10n-zh-cn 

(5)Abiword 中文乱码的解决办法 

Abiword是个很好的轻量级的文字编辑排版工具，我最欣赏它的是打开文档的速度和支持文档格式的种类多样性。但是安装后的Abiword默认只支持英文，输入中文会显示乱码，想显示正常的话还要设置字体为中文字体，每次都要设置，太麻烦。 

究其原因，是因为Styles的设置文件在/usr/share/AbiSuite-2.4/templates/下面，如果你使用的系统语言是简体中文，那么对应的abiword设置文件是normal.awt-zh_CN。可以使用以下方法来修改： 

sudo gedit /usr/share/AbiSuite-2.4/templates/normal.awt-zh_CN 

将Times New Roman替换为Microsoft YaHei（或者其它你喜欢的字体），然后保存即可。 

如果喜欢使用vim编辑，使用以下命令： 

sudo vim /usr/share/AbiSuite-2.4/templates/normal.awt-zh_CN 

然后在正常模式下输入冒号，键入以下命令： 

%s/\<Times New Roman\>/Microsoft YaHei/g 

再输入冒号，输入wq，回车即可。 

五、多媒体播放器的安装与配置 

（1）万能播放器mplayer 

这是个linux下面很流行的播放器，系统安装完后自带的有音频和视频播放器，但是都没有很全的解码器，所以很多东西都不能播放. 

从源上下载并安装mplayer： 

sudo apt-get install mplayer 

然后安装解码器包： 

sudo apt-get install w32codecs 

这时候，你就可以使用mplayer播放大多数媒体文件了。需要注意的是，如果打开媒体文件时提示视频设备有问题，那么可以在设置界面换一个视频驱动即可，直到没有问题为止。 

mplayer皮肤的安装：假设下载回来的是名字是skin_name.tar.bz2的压缩包： 

sudo tar jxvf /路径名/skin_name.tar.bz2 

sudo mkdir -p ~/.mplayer/skins #如果你是第一次安装皮肤，只需执行一次 

sudo cp /路径名/skin_name ~/.mplayer/skins/ && mv ~/.mplayer/skins/{skin_name,the_other_name} #mplayer皮肤列表中将显示为the_other_name 

（2）音频播放器audacious及mp3文件tag乱码的解决 

a、audacious 

mplayer虽是万能的，但是听音频文件最好还是用专门的音乐播放器，这里推荐现在开始流行的audacious. 

sudo apt-get install audacious 

安装audacious的解码器 

sudo apt-get install audacious-plugins audacious-plugins-extra audacious-plugins-extra-console 

安装完成，现在可以使用它来听多种格式的音频文件了。 

另外，audacious的播放列表默认使用UTF-8的编码，但大多数mp3使用的是GBK编码，所以播放列表上会出现乱码，我只发现一种不彻底解决的办法，就是打开audacious的设置界面，在插件栏目里面选择mpeg插件，在其首选项里面的“标题”里把“ID3格式”后面填上“GBK”，保存即可。这样以后再往播放列表里面添加歌曲的时候虽说刚开始还是乱码，但是当开始播放这首音乐的时候就会变成正常的标题，目前似乎还没有更好的方法。 

b、mp3的tag乱码 

使用工具：ID3iconv 0.2.1 

使用方法：cd 到你要的文件夹下，用这个命令转换： 

java -jar ~/id3iconv-0.2.1.jar -e GBK /路径/*.mp3 

注意：我这边用 -e 指定 mp3 原来的编码是 GBK 的。繁体中文就改为 Big5 

如果有很多个子文件夹，又不想一个一个 cd 到里面转换，可以配合 find 命令来解决： 

find . -iname "*.mp3" -execdir java -jar ~/id3iconv-0.2.1.jar -e GBK {} \; 

find 命令中的 -execdir 参数是让转换的程序在 找到的文件的 那个目录里执行， 也就相当于一个一个 cd 到里面再转换了。 

（3）媒体解码库gstreamer 

gstreamer是针对系统自带的音频和视频播放器的解码包，打开“新立得”软件包管理器在里面搜索gstreamer，你会得到一大堆结果，在里面选择你需要的解码包即可。 

六、网络工具的安装与配置 

（1）Opera的安装与配置及中文输入法问题的解决 

Opera是linux上另一款可以和Firefox比美的浏览器，占用系统资源比firefox低，性能比firefox要好，而且是唯一一个得到国际认证的浏览器，我尤其喜欢它极其强大的快捷键设置。但是源里现在似乎没有，需要从网上下载。 

打开[www.opera.com]()，如果你在linux下使用firefox浏览的话，那么第一个页面应该就是opera的linux版，注意选择ubuntu下载包。 

下载下来之后双击运行，将会用deb解包器打开，点选上面的安装即可。下面介绍使用控制台安装的命令：（假设下载回来的文件名是opera.deb） 

sudo dpkg -i opera.deb 

卸载的时候使用： 

sudo dpkg -r opera 

dpkg是专门用来处理”.deb”格式的安装包的命令，更深的应用可在控制台下输入 

dpkg –help 

来了解。 

下面介绍opera界面的汉化，安装完成后只有英文语言文件，输入以下命令获取9.01版本的中 文语言文件： 

wget -c http://www.opera.com/download/lng/901/ouw901_zh-cn.lng 

gedit ouw*.lng 

ctrl+H 

9.01->9.10 

在opera浏览器的语言设置中选中*_zh-cn.lng即可 

opera安装后如果遇到中文输入法不能使用的问题，输入以下命令修改文件： 

sudo gedit /usr/bin/opera 

在开头添加: 

export QT_IM_MODULE=XIM #使 SCIM 能够输入中文 

export LC_ALL=zh_CN #使 Opera 能够以你选择的字体显示中文 

现在可以正常使用了。 

（2）即时通讯程序 

(a)集成即时通讯工具gaim 

系统已默认安装了集成即时通讯工具Gaim,但是系统默认安装的gaim不带QQ插件，现在打开“新立得”，在里面搜索gaim，然后卸载它。 

再到Gaim中国下载集成了QQ的gaim版本，网址是：www.gaimcn.com 

下载下来的是个deb包，使用刚才介绍的安装方法安装即可，安装完毕后就可以使用了，里面已集成有QQ协议。 

Gaim使用中你会发现，如果你使用的是多帐号，那么它们要么同时在线，如果你设置状态为隐身，那么所有的帐号都被隐身，如果你希望设置帐号之间使用不同的状态，那么点击软件界面最下方的状态选择处，最下面有新建的按钮，新建一个状态，你可以设置有的帐号隐身，有的帐号在线，然后保存这个状态，以后选择相应的状态即可选择谁在线，谁隐身，需要注意的是你设置的时候要在这个新建的状态中为每个帐号选择一个状态，即你新建的其实是一个状态组合。 

(b)最逼真的QQ工具 

LumaQQ是linux下最逼真的QQ工具。 

它需要java运行时环境的支持，所以要先安装java运行时环境： 

sudo apt-get install sun-java5-jre 

设置当前默认的java解释器: 

sudo update-alternatives --config java 

执行后会出现类似如下的画面: 

```
There are 4 alternatives which provide `java'. 
Selection Alternative 
----------------------------------------------- 
*+ 1 /usr/lib/jvm/java-gcj/jre/bin/java 
2 /usr/bin/gij-wrapper-4.1 
3 /usr/bin/gij-wrapper-4.0 
4 /usr/lib/jvm/java-1.5.0-sun/jre/bin/java 
Press enter to keep the default[*], or type selection number: 
```

输入 有包含 "sun" 的行的前面的数字。如上面显示，则输入 4，然后回车确定。 

然后到LumaQQ官方站点下载相应的压缩包，解压，然后将目录移入/opt目录： 

sudo mv /路径/LumaQQ /opt 

建立快捷方式： 

sudo gedit /usr/share/applications/LumaQQ.desktop 

在新增的文件内加入下面这几行 

```ini
[Desktop Entry] 
Name=LumaQQ 
Comment=QQ Client 
Exec=/opt/LumaQQ/lumaqq 
Icon=/opt/LumaQQ/QQ.png 
Terminal=false 
Type=Application 
Categories=Application;Network; 
```

保存编辑过的文件,到(应用程序 -> Internat -> LumaQQ)执行之。 

（c）功能最强大的QQ工具Eva 

Eva是KDE下面的一个QQ客户端，这里需要说明的是GNOME与KDE下的程序在彼此环境里是可以通用的，我使用后就毅然抛弃其它几种QQ客户端，因为它的界面不但小巧好看，而且最重要的是它的功能是linux下面最强的。 

Eva可以接收图片、自定义表情，进行屏幕截图，传送图片，传送文件，可以说和官方QQ相比，除了视频、语音之外，它具备了所有实用的功能。 

sudo apt-get install eva 

（d）多协议即时通讯工具Kopete 

Kopete也是KDE下面的工具，它类似GNOME下的gaim，但是我觉得不管是界面还是功能，都比gaim要好，如果你喜欢使用MSN,yahoo msg,ICQ等，可以安装这个东西： 

sudo apt-get install kopete 

（3）新闻阅读 

如果你希望阅读RSS聚合新闻，那么还要安装RSS新闻阅读器，这里推荐liferea: 

sudo apt-get install liferea 

另外我现在使用的一款KDE下的工具也很好： 

sudo apt-get install akregator 

（4）邮件客户端 

系统自带的evolution已经是很好的邮件客户端了，需要指出的是，在evolution里面设置gmail的帐号不用另外设置其pop和smtp的端口号，因为gmail的端口号与别的邮箱不同，在windows下面需要另外设置。 

（5）下载工具 

比较有名的是d4x，现在新兴的有个MultiGet,如果使用d4x的话，输入命令： 

sudo apt-get install d4x 

如果希望使用MultiGet,请到论坛下载，源里似乎没有。 

（6）p2p工具 

sudo apt-get install amule #win下的emule用这个代替 

sudo apt-get install Azureus #win下的bitcomet用这个代替 

七、编辑器、编译环境与词典 

（1）编辑器 

Linux下面的编辑器十分强大，功能也十分丰富，是windows下面的那些文本编辑器所不能比拟的。比较有名的是vim和emacs. 

这里以vim为例，使用源安装，输入命令： 

sudo apt-get install gvim 

vim的功能非常丰富和强大，最好找一些专门的资料去学习，另外它自带的也有很详细的帮助文件，只不过是英文的，英文好的可以看一下。 

（2）编译环境 

Linux是开源的，所以有很多发行版，像redhat,redflag,suse,ubuntu等等，这些发行版虽然都是使用的是linux内核，但是由于种种原因，系统的环境变量不尽相同，使用在一个系统上打包的安装包在另一个系统上安装程序，往往会出现问题，但是在所有的linux发行版中安装程序有一个普适的方法，就是使用源代码安装，这就需要编译环境，另外如果你想在linux下面编程，也需要它。 

输入命令： 

sudo apt-get install build-essential bin86 kernel-package libqt3-headers libqt3-mt-dev 

（3）词典 

星际译王是个很好的词典工具，它的功能相当于windows下面的金山词霸。 

sudo apt-get install stardict 

这里安装的只是星际译王的词典程序，它有很多词典供你选择安装，你可以到源上去选择： 

sudo apt-get install stardict* 

然后选择你喜欢的词典安装即可。也可以到其官方站点选择词典下载后安装。 

http://stardict.sourceforge.net/ 

将下载的词典解压，然后复制或移动到/usr/share/stardict/dic 

八、NVIDA显卡驱动与Beryl的安装 

Beryl是linux下的一个主题管理程序，说它相当于windows下面的windowblinds不知道恰当不恰当。安装了beryl之后，linux的桌面变得比之windows vista有过之而无不及，喜欢漂亮界面的可以试用一下。 

当然，使用beryl的代价是牺牲系统性能，使用beryl的机子工作效率会比之前低一些。而且有人反映装了beryl之后会使系统变得非常慢，不知道是硬件的原因还是别的什么东西，不过大多数人应该不会出现什么问题，我的机子配置并不高，好几年前配的了，但是一直使用beryl都没有什么问题，速度也完全可以接受。 

下面介绍的是NVIDA的显卡下，beryl的安装。因为我用的就是nvida的显卡。 

另外需要说明的是下面的内容借鉴了论坛里面“NV 1.0-9629驱动+edgy+aiglx+Beryl方法汇总”的帖子，在此对作者表示感谢。 

（1）nvida显卡驱动的安装 

首先，要确认你的系统是全新安装的ubuntu6.10,而不是从6.06升级上来的，否则可能会出问题。 

其次，到http://www.nvidia.com/object/unix.html下载对应32/64位版本的驱动，假设放在/home/lans/下面， 

删除原有驱动(假设你安装过源里面的nvidia-glx驱动，如果没有装过或曾经手动安装驱动的可以跳过）： 

sudo apt-get --purge remove nvidia-glx 

然后灰复X的设置 

sudo cp /etc/X11/xorg.conf.backup /etc/X11/xorg.conf 

重启，确定起动正常 

然后，执行命令： 

sudo gedit /etc/default/linux-restricted-modules-common 

在最后的双引号里面添加nv两个字，添加后就是“nv” 

预防起动X失败 

sudo cp /etc/X11/xorg.conf /etc/X11/xorg.conf.mybackup 

如果起动X失败，可以用sudo cp /etc/X11/xorg.conf.mybackup /etc/X11/xorg.conf来灰复X设置，然后起动 

以下为网络上留传的要点，原贴作者多台机器安装没有使用，我安装的时候也没有用，如果不 行，可以尝试（但是会删除受限制模块，导致某些别的硬件无法使用： 

sudo apt-get --purge remove nvidia-settings nvidia-kernel-common 

sudo rm /etc/init.d/nvidia-* 

下面开始安装（对于曾经手动安装nv驱动的，可以在安装的时候选择卸载旧版的驱动）： 

请拿出一张纸和一支笔，记录下一下的命令，因为我们要去到纯终端里面安装 

注销后，按Ctrl+Alt+F1，输入用户名和密码，登录后关闭gdm： 

sudo /etc/init.d/gdm stop 

然后开始安装（假设下载的文件放在/home/lans/目录下） 

sudo sh /home/lans/NVIDIA-Linux-x86-1.0-9629-pkg1.run(如果你下载的是64位驱动 sudo sh /home/lans/NVIDIA-Linux-x86_64-1.0-9629-pkg1.run） 

进入安装界面后，首先接受协议，选“接受” 

可能会有提示已经安装了旧的驱动（视乎你自己是否有手动安装过），是否删除，选yes就是了， 一般会提示缺少模块，问是否网上下载，选“no”， 提示需要自己编译模块，选“ok”，然后编译安装开始， 最后提示需要修改xorg.conf，是否允许，选yes，完成安装，选ok。 

然后回到终端界面,重启gdm: 

sudo /etc/init.d/gdm start 

现在可以重启试试驱动是否已经装好，在“应用程序-》系统工具-》NVIDIA X server setting里面可以看到驱动的信息 

提示：每次更新内核后都需要重新安装nv的驱动！ 

（2）安装Beryl 

1.添加源 ： 

sudo gedit /etc/apt/sources.list 

加入以下源的其中一个 ： 

deb http://www.beerorkid.com/compiz edgy main-edgy 

deb http://media.blutkind.org/xgl/ edgy main-edgy 

deb http://compiz-mirror.lupine.me.uk/ edgy main-edgy 

deb http://ubuntu.compiz.net/ edgy main-edgy 

如果是64位的： 

deb http://www.beerorkid.com/compiz edgy main-edgy main-edgy-amd64 

deb http://media.blutkind.org/xgl/ edgy main-edgy main-edgy-amd64 

deb http://compiz-mirror.lupine.me.uk/ edgy main-edgy main-edgy-amd64 

deb http://ubuntu.compiz.net/ edgy main-edgy main-edgy-amd64 

保存后退出 

然后加入钥匙，在控制台输入：（下面的任选其一） 

wget http://www.beerorkid.com/compiz/quinn.key.asc -O - | sudo apt-key add - 

wget http://media.blutkind.org/xgl/quinn.key.asc -O - | sudo apt-key add - 

wget http://compiz-mirror.lupine.me.uk/quinn.key.asc -O - | sudo apt-key add - 

wget http://ubuntu.compiz.net/quinn.key.asc -O - | sudo apt-key add - 

刷新库 

sudo apt-get update 

sudo apt-get dist-upgrade 

2.安装beryl 

sudo apt-get install beryl emerald emerald-themes 

修改xorg.conf 

sudo gedit /etc/X11/xorg.conf 

在 Section "Screen" 里面添加 

Option "AddARGBGLXVisuals" "True" 

保存并退出. 

下面为可选(我没有使用)： 

在 Section "Device" 里面添加 

Option "TripleBuffer" "true" 

添加如下一项：（本人没有添加） 

Section "Extensions" 

Option "Composite" "Enable" 

EndSection 

保存退出 

3.最后,在系统-》首选项-》会话-》起动程序 里面添加一项”beryl-manager“ 

注销 

alt+ctrl+backspace，重启X 

再登录，就可以看到beryl的效果了。 

附：错误解决 

如果出现窗口框闪烁，可以在系统-》首选项-》会话-》起动程序 里面添加一项”emerald –replace“ 

beryl 除了自己附带的主题，还可以到 

http://browse.deviantart.com/customizat ... linuxutil/ 

http://gnome-look.org/ 

去下载。 

（3）Splash Screen的安装 

a、Ubuntu Splash 

即输入用户名和密码后进入桌面时显示的那个长方形图形，喜欢换皮的可以在以下路径修改： 

/usr/share/pixmaps/splash/ubuntu-splash.png 

b、OpenOffice Splash 

即运行openoffice时显示的那个图片，在以下路径： 

/usr/lib/openoffice2/program/openintro_ubuntu.bmp 

c、gimp splash 

/usr/share/gimp/2.0/images 

九、Wine的安装 

wine用来在linux下面运行一些windows下面的程序，不是所有的windows的程序都可以使用wine来运行，只是一部分。 

输入以下命令从源里安装wine： 

sudo apt-get install wine 

为了方便使用，建议安装EasyWine,这是个wine的辅助工具。 

可以到linux公社下载。 

安装完毕后，在控制台输入 

EasyWine即可启动程序，注意大小写，linux严格区分大小写。需要注意的是，如果运行时出现错误，就使用以下命令运行EasyWine, 

bash EasyWine 

通过以下命令可以设置wine的参数： 

winecfg 

运行程序时使用以下命令： 

wine /程序路径/程序名称.exe 

十、虚拟机VMware 

如果有些事需要在windows下面做而又嫌装双系统麻烦的可以使用这个虚拟机。可以到linux公社下载，然后安装。linux公社上面有它的注册码，安装完后在“帮助”菜单里面输入注册码。 

解压后在控制台执行： 

cd /路径 

即进入该目录 

执行./vmware-install.pl 

十一、磁盘分区的挂载 

如果你在装系统时有的分区没有挂载，或者装完系统后磁盘分区有所变更，那么需要使用一些命令来重新挂载分区。 

（1）挂载一个原先存在而没有挂载的分区 

例如，我要挂载第二块磁盘第一分区(对应的是/dev/目录下面的hdd1,第二第三分区对应的是什么应在“系统管理”里面参看“设备管理器”) 

1.建立挂载目录 

一般把分区挂在/media下面，建立hdd1目录，此目录名字任意 

sudo mkdir /media/hdd1 

2、挂载分区 

分区为ntfs格式： 

sudo mount -t ntfs /dev/hdd1 /media/hdd1 

分区为fat32格式： 

sudo mount -t vfat /dev/hdd1 /media/hdd1 

3、自动挂载分区 

按照第二步的方法挂载的话，下次启动系统时不会自动挂载，要想让系统自动挂载该分区，输入以下命令： 

sudo cp /etc/fstab /etc/fstab_backup 

sudo gedit /etc/fstab 

在打开的文件里最后一行加入： 

/dev/hdd1 /media/hdd1 ntfs utf8,umask=0222 0 0 

如果是fat32分区，输入： 

/dev/hdd1 /media/hdd1 vfat utf8,umask=000 0 0 

保存并退出，重新启动系统即可。 

（2）新加入一块还没有分区的全新的硬盘 

论坛里有相应的帖子，此处不再赘言。 

十二、游戏 

（1）linux下面的游戏 

linux志在成为一个稳定高效的操作平台，由于其应用方向和用户人群的特定性，所以这上面的游戏都是一些休闲类、智力类的小游戏。这并不是说linux就做不出大型的高质量的游戏来，比如DOOM，据说CS就是对DOOM的拙劣模仿。 

1、泡泡龙 

sudo apt-get install frozen-bubble 

2、百战天虫 

当然不是官方的东西，一个2D模仿百战天虫的东东 

sudo apt-get install wormux 

3、linux下的雷电 

看起来不错，但是我装上后速度非常慢，不知道什么原因。 

sudo apt-get install chromium 

4、等等 

在菜单中的“添加删除程序”中还有很多，可自行选择。 

（2）win下的游戏 

如果你安装了wine,那么windows下面的一些游戏你仍然可以玩，众所周知的就是魔兽争霸了，具体的设置方法见论坛上的相关帖子，由于我不常玩儿游戏，这里不作介绍，相信你选择linux也不是为了玩儿游戏的。 

十三、一些常用工具 

（1）读写ntfs分区 

ubuntu默认只支持对ntfs分区的读操作，而不支持写操作，现在使用ntfs-3g已经可以实现对ntfs分区的写操作。 

需要特别说明的是，现在ntfs-3g还没有完美支持这项功能，据我使用的体会而言，在ntfs上建立文件和文件夹是没有问题的，但是删除文件的时候，回收站没有提示，文件其实已经移入该分区的”.Trash-用户名”目录里面了，只能手工清除了。另外，对中文的支持也不完美，有时候会出现带有中文的文件及文件夹均无法显示的问题。 

sudo apt-get install ntfs-3g 

sudo gedit /etc/fstab 

修改你的ntfs分区那一行，原先的"ntfs"改为"ntfs-3g"即可。例如我的是 

/dev/hda5 /media/hda5 ntfs-3g efaults,nls=utf8,umask=007,gid=46 0 0 

sudo gedit /etc/modules 

在文件末尾加上一行内容"fuse" 

重启生效，或是使用命令重新挂载 

sudo modprobe fuse 

sudo umount -a 

sudo mount -a 

如果出现问题，只需将上面的ntfs-3g替换为ntfs然后再重启或重新挂载即可。 

（2）java日记本 

一直觉得linux下面写日记没有什么好东西，后来想起来原来在windows下就用过一个java日记本，还不错。 

首先你应该确认已经安装了java运行时环境，如果没有，请参看lumaqq的安装部分。 

我在网上没有找到linux下面的压缩包，于是下了一个windows下面的安装程序，在虚拟机里安装后，将其文件夹复制出来。 

执行里面的jdiary.sh就行了，如果没有运行，那是因为该脚本是在win下编写的，里面的回车bash不认所致，使用gedit打开该脚本，然后把里面的回车全部删除，再重新回车即可。 

（3）图片管理Picasa 

Google的这款图片管理软件相信很多人都知道吧，到它的主页下载并安装即可。 

（4）光碟烧录GnomeBaker 

sudo apt-get install gnomebaker 

（5）CHM阅读器 

很多电子书都是chm格式的，如果需要阅读，可以使用这个工具，在“新立得”或“添加删除程序”里面找吧。 

有些chm文件阅读时会出现乱码或者左边的目录显示不出来的问题，这时需要使用别的工具来阅读了，我现在使用的是firefox的一款插件chm reader，你可以到它的插件下载网页下载安装，一次看不完时还可以把当前页面加入收藏夹，下次直接打开收藏夹中链接即可，很方便。 

（6）数学程序 

a、scilab:功能相当于matlab,法国科学院出品。 

sudo apt-get install scilab 

b、Maxima:出身名门，上个世纪六十年代由MIT（麻省理工）开发，原来美军赞助的东东，功能相当于mathematica，我现在就是以它为计算工具，非常好用。 

sudo apt-get install maxima 

安装后的maxima没有图形用户界面，它是在控制台上运行的，当然你可以安装它的图形用户界面： 

sudo apt-get install wxmaxima 

十四、Linux下程序的安装 

（1）deb软件包的安装 

deb是Debian Linux提供的一个包管理器，优点是不被严格的依赖性检查所困扰。ubuntu使用的就是这种软件包。 

安装方法： 

sudo dpkg -i /软件包路径/软件包名字.deb 

卸载方法： 

sudo dpkg -r 软件名字 

另外在ubuntu桌面版中你也可以直接双击该软件包，里面有安装按钮。 

（2）rpm软件包的安装 

RPM全称是Red Hat Package Manager（Red Hat包管理器）。它最初是RedHat开发的软件包，在很多linux发行版上应用很广泛。在ubuntu中，应先将其转化为deb包，然后再安装，转化方法为： 

sudo alien 软件包名称.rpm 

然后再执行sudo dpkg -i 软件包名称.deb 

如果提示不认alien命令，执行： 

sudo apt-get install alien 

（3）使用源码进行安装 

使用源代码安装软件，能按照用户的需要选择定制的安装方式进行安装，而不是仅仅依靠那些在安装包中的预配置的参数选择安装。它可以克服linux发行版众多的问题。 

首先将下载回来的源码解压到一个目录里，然后进入这个目录： 

cd /目录路径 

获得root权限： 

su root 

执行： 

./configure 配置选项 #本步非必须，你下载源码的时候一般在下载页面都会有安装提示，源码包里的Readme和Install文件也会有说明，如果说要执行这一步，那么按照提示操作即可。 

下面编译： 

make 

如果提示出错，那么看看上面./configure后有没有提示有什么东西不存在，如果有，只要执行：sudo apt-get install 该缺失组件名称 即可。 

下面安装： 

make install 

下一步为可选，用来清除安装时产生的临时文件： 

make clean 

卸载时进入该源码目录，执行： 

make uninstall 

（4）绿色软件 

解压即可运行的程序。 

解压后cp或mv到任一目录即可。 

十五、其它GUI的安装 

1、KDE 

KDE是和GNOME齐名的一个GUI，它可以和GNOME共存，在登录界面可以选择是从KDE启动还是从GNOME启动，KDE和GNOME下面的程序在彼此上面都可以运行。使用下面命令安装KDE及KDE下常用工具： 

sudo apt-get install kubuntu-desktop 

安装完成后会提示你使用KDM还是GDM，这两个分别是两者之下的桌面管理程序，随便选择一个即可，以后如果想切换，执行以下命令： 

sudo dpkg-reconfigure kdm 

十六、进阶技巧 

（1）关于Grub的FAQ 

1、如何使用liveCD修复Grub？ 

在终端下输入： 

sudo su 

grub 

你应该看到像这样的东西： 

grub> 

接着输入： 

root (hdx,y) 

setup (hdx) 

不要完全照样输入，先弄懂这些命令的含义。 

hd表示硬盘，格式是 

(hd硬盘号,分区号) 

从零开始计算。 

所以（按照日常的习惯，从1开始数），(hd0,0)表示第一个硬盘的第一个分区，(hd0,1)表示第一个硬盘的第2个分区，(hd1,4)表示第2个硬盘的第5个分区。 

（注意，linux系统里是用hda1表示第一个硬盘的第一个分区，不要把它和grub中的表示混起来。） 

root (hdx,y) 

这里的(hdx,y) 是/boot 目录所在的分区（通常情况下，也就是你装ubuntu系统的分区，除非你安装的时候把/boot分出去了。）如果你不知道你把ubuntu装在第几个分区，可以在输入root (hd0, 时按Tab键来补全。（假设你把ubuntu装在第一块硬盘上） 

按了tab后，你将看到一些有关各个分区的信息。通常你可以通过分区的大小和格式看出你把ubuntu系统装在哪里了。 

setup (hdx) 通常情况下，是setup (hd0)。（多半你把ubuntu装在第一块硬盘上。）要注意的是，不要输入setup (hd0,0)。我曾经犯过这个错误。(如果你没把命令抄下来，凭记忆操作的话，可能会犯这种错误。） 

最后，用 

quit 

退出grub,重启系统。 

2、两块硬盘，第一块装ubuntu，第二块装winxp,怎样设置grub使之可引导xp? 

sudo gedit /boot/grub/menu.lst 

然后在最后加上如下内容即可： 

title Microsoft Windows XP Professional 

root (hd1,0) 

makeactive 

map (hd0) (hd1) 

map (hd1) (hd0) 

chainloader +1 

（2）桌面图标隐藏、显示设置 

执行： 

gconf-editor 

在下面路径设置： 

app->nautilus->desktop->vomules_visible 

（3）在/var/cache/apt/archives里面发现大量原来使用apt-get下来的deb包，太占空间了，能不能删掉？ 

执行： 

sudo apt-get clean 

（4）gvim的字体太难看，怎样设置为雅黑? 

首先应确认你已安装雅黑字体，然后执行： 

touch ~/.gvimrc //已存在~/.gvimrc文件就直接执行下面的 

cat >> ~/.gvimrc 

<Enter> 

"设置默认字体<Enter> 

set guifont=Microsoft\ YaHei<Enter> 

<ctrl-c> 

（5）为什么键盘上的左右箭头不能用了？ 

不知道是系统自己的bug还是别的什么原因，这是因为你在“系统”－“首选项”里面设置了快捷键，在快捷键里面删除使用左右箭头的键即可解决问题。 

(6)有些常用命令记不住怎么办? 

如今的GNOME和KDE图形用户界面已经可以完成几乎一切工作了，但是命令行在做很多工作的时候非常方便，所以经常使用的命令比较长或者不容易记住会是件很崩溃的事情，不过可以使用以下方法缩短这些命令: 

在终端中输入: 

sudo gedit /etc/bash.bashrc 

在打开的文件里面加入以下代码: 

alias update='sudo apt-get update' 

alias upgrade='sudo apt-get dist-upgrade' 

alias go='pon dsl-provider' 

这里列出了三个自定义命令:第一个使用update代替单引号中的命令，这样以后在终端中直接输入update就可以更新源中程序的列表了；第二个使用upgrade代替后面单引号中的内容，以后使用upgrade即可在源得到更新后更新系统，第三个使用go来代替后面的ADSL拨号命令。 

自定义命令是可以任意起名字的，但是事前你应该确认系统中没有这个命令，可以在终端中输入你想用的单词并回车，如果提示没有这个命令，才可以自定义。 

（7）快速搜索文件 

当然你可以使用系统自带的图形界面的搜索工具，事实上我也经常使用它；也可以安装类似windows下面的百度硬盘、Google桌面一样的桌面搜索工具“Beagle”；但是我不喜欢前者的低速和后者过于占用内存，以前在windows下，我喜欢使用一款叫“locate32”的搜索工具，它只需先建立一个硬盘中数据的数据库，然后什么时候需要搜索，什么时候打开它就行了。这种工具借鉴了桌面搜索的快速和普通搜索工具的平时不占用系统资源的优点，所以我很喜欢。 

在linux下面，系统同样提供了这样一个工具，它应该是默认就安装上了的。 

首先，在控制台下输入命令： 

sudo updatedb 

键入密码后开始建立数据库，完成后输入： 

locate 关键词 

即可找到硬盘中所有携带该关键词的文件。 

这种工具的缺点也很明显，由于它平时不运行，所以它不能实时捕捉到硬盘上文件的变化，如果你所要找的文件比数据库建立的时间要新，那么可能就找不到。所以要经常利用系统空闲的时间更新数据库。 

（8）“不打开”音乐播放器预听mp3 

windows下面可以在某音乐文件上单击鼠标选中后在文件夹中预听，在linux下会更简单、有趣： 

sudo apt-get install mpg123 

mpg123是linux命令行界面下听mp3的工具，经常工作在CLI（COMMAND LINE INTERFACE）下又喜欢听歌的人是不会陌生的。 

在GNOME（KDE下我没有试）下它会有另一种妙用，安装成功后，打开音乐文件夹，找到一个mp3格式的文件，将鼠标箭头放上去，什么都不用做，音乐会自动播放，当你将鼠标箭头移走时，音乐自动停止。 

这是因为鼠标箭头放在mp3文件上时，系统会自动调用mpg123在后台播放该文件，箭头移走后，mpg123自动关闭，所以不用担心会无谓的占用系统资源。

