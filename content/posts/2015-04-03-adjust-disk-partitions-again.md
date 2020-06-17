---
layout: post
title: "再次调整磁盘分区"
date: 2015-04-03 14:44:00
comments: true
categories:
- 计算机
tags:
- linux
---

自从[上次](/post/adjust-disk-partitions-in-archlinux/)调整磁盘分区，一直把根目录和主目录分别挂在一个物理分区下，即使系统挂了或者换发行版也不影响主目录。最近根分区很紧张，干脆把两个分区合并了。

先用UNetBootin安装Puppy Linux到U盘，需要手工修改U盘里的syslinux.cfg，把“pmedia=**cd**”改成“pmedia=**usbflash**”，然后用U盘启动。

把主目录的内容完整复制到移动硬盘：

{% codeblock lang:bash %}
# 挂载主目录
mkdir /mnt/oldhome
mount -t ext4 /dev/sda2 /mnt/oldhome

# 挂载移动硬盘
mkdir /mnt/bakdisk
mount -t ext4 /dev/sdc1 /mnt/bakdisk

# 复制主目录
cp -a /mnt/oldhome /mnt/bakdisk/

# 取消挂载主目录
umount /mnt/oldhome
{% endcodeblock %}

用gparted删除主目录分区，合并到根分区。然后恢复主目录：

{% codeblock lang:bash %}
# 挂载根分区
mkdir /mnt/newroot
mount -t ext4 /dev/sda1 /mnt/newroot

# 恢复主目录
cp -a /mnt/bakdisk/* /mnt/newroot/

# 修改fstab，取消主目录的挂载
vim /mnt/newroot/etc/fstab

# 取消挂载
umount /mnt/bakdisk
umount /mnt/newroot
{% endcodeblock %}
