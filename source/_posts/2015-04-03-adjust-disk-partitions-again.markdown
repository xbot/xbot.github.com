---
layout: post
title: "再次調整磁盤分區"
date: 2015-04-03 14:44
comments: true
categories: 計算機
tags:
- linux
---

自從[上次](/post/adjust-disk-partitions-in-archlinux/)調整磁盤分區，一直把根目錄和主目錄分別掛在一個物理分區下，即使系統掛了或者換發行版也不影響主目錄。最近根分區很緊張，乾脆把兩個分區合併了。

先用UNetBootin安裝Puppy Linux到U盤，需要手工修改U盤裡的syslinux.cfg，把“pmedia=**cd**”改成“pmedia=**usbflash**”，然後用U盤啟動。

把主目錄的內容完整複製到移動硬盤：

{% codeblock lang:bash %}
# 掛載主目錄
mkdir /mnt/oldhome
mount -t ext4 /dev/sda2 /mnt/oldhome

# 掛載移動硬盤
mkdir /mnt/bakdisk
mount -t ext4 /dev/sdc1 /mnt/bakdisk

# 複製主目錄
cp -a /mnt/oldhome /mnt/bakdisk/

# 取消掛載主目錄
umount /mnt/oldhome
{% endcodeblock %}

用gparted刪除主目錄分區，合併到根分區。然後恢復主目錄：

{% codeblock lang:bash %}
# 掛載根分區
mkdir /mnt/newroot
mount -t ext4 /dev/sda1 /mnt/newroot

# 恢復主目錄
cp -a /mnt/bakdisk/* /mnt/newroot/

# 修改fstab，取消主目錄的掛載
vim /mnt/newroot/etc/fstab

# 取消掛載
umount /mnt/bakdisk
umount /mnt/newroot
{% endcodeblock %}
