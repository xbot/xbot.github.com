---
layout: post
title: 使Thinkpad的静音按钮能被识别的方法
slug: howto make thinkpad mute button detected on linux
date: 2010-08-09 00:00:00
tags:
- Archlinux
- GNOME
- ThinkPad
- XFCE
- 内核
- 桌面环境
- 计算机
status: publish
published: true
comments: true
meta:
  _edit_last: '1'
  views: '1000'
  _wp_old_slug: ''
---
我的型号是X200，问题表现为在GNOME或XFCE下静音按钮无效，使用xev也捕获不到输入信号，而音量增加和减小按钮可以正常使用且有OSD。

解决办法是在grub的启动菜单中，给内核加上参数<strong>acpi_osi=“Linux”</strong>，如： 

```bash
# (0) Arch Linux
title  Arch Linux
root   (hd0,2)
kernel /boot/vmlinuz26 root=/dev/sda3 resume=/dev/sda4 ro acpi_osi="Linux"
initrd /boot/kernel26.img
```

从查到的资料看，<strong>acpi_osi</strong>参数是用来指定操作系统接口的，据说有些硬件都只针对Windows做了测试或优化，对于这些硬件，如果将内核的操作系统接口指定为Linux，则有可能会出问题。所以内核从2.6.23版本开始，此参数的默认值被改成了“<strong>!Linux</strong>”，以保证更广泛的兼容性和稳定性。

而根据<a href="http://www.thinkwiki.org/wiki/Mute_button">这里</a>的说法，thinkpad一直以来在对Linux的兼容程度上有很好的口碑，故可以放心添加这个参数。 

另外，xfce下使用OSD需要安装xfce4-volumed。
