---
layout: post
title: 制作Puppy Linux的启动U盘
date: 2010-11-11 00:00:00
tags:
- Linux
- 计算机
status: publish
published: true
comments: true
meta:
  _edit_last: '1'
  views: '997'
  _wp_old_slug: ''
---
Puppy Linux的体积只有一百多兆字节，但图形界面实现得很完备，也包含了很多短小精悍的工具，安装和使用都很方便，非常适合用来做日常维护工具盘。

Puppy自带了傻瓜式的启动U盘制作工具，但官方只提供整个发行版的ISO镜像下载，通常需要刻录成光盘并引导系统启动后才可以继续将Puppy安装到U盘。其实也可以不刻录光盘，而是把Puppy安装到硬盘上。

Puppy本身封装得很好，对软硬件环境的要求不高，完全可以安装到一个独立的目录中而不影响其它操作系统。本文基于Linux环境下通过GRUB引导的前提，Windows下应该可以通过GRUB4DOS引导。

1. 挂载Puppy的ISO镜像到一个目录：

{% codeblock lang:bash %}
sudo mount -o loop puppy.iso /media/puppy
{% endcodeblock %}

2. 复制镜像中的所有文件到根目录下的puppy目录：

{% codeblock lang:bash %}
sudo mkdir /puppy
sudo cp -a /media/puppy/* /puppy
{% endcodeblock %}

3. 修改GRUB的引导文件，加入Puppy的引导项：

{% codeblock lang:bash %}
sudo vi /boot/grub/menu.lst
{% endcodeblock %}

<blockquote>
# (2) Puppy
title Puppy
root (hd0,2)
kernel /puppy/vmlinuz root=/dev/ram0
initrd /puppy/initrd.gz
</blockquote>

4. 重启系统并启动到Puppy

5. 使用桌面上的<strong>Setup</strong>中的<strong>Puppy Universal Installer</strong>制作启动U盘。不过虽然这是官方推荐的制作工具，但我的杂牌U盘无论如何也不能引导系统，最后使用另一个备选的工具“<strong>BootFlash USB Installer</strong>”的<strong>USB-HDD</strong>模式制作成功。
