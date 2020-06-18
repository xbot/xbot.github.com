# 调整Arch的磁盘分区

<p><h2>简述</h2></p>

<p>目标：移除Windows 7，整块硬盘完全分配给Archlinux。</p>

<p>原分区结构：</p>

<p>
<table class="art_tbl">
<tr><th class="art_tbl_th">分区</th><th class="art_tbl_th">类型</th><th class="art_tbl_th">挂载</th><th class="art_tbl_th">文件系统</th><th class="art_tbl_th">容量</th></tr>
<tr><td class="art_tbl_td">/dev/sda1</td><td class="art_tbl_td">主分区，启动分区</td><td class="art_tbl_td">Windows 7的安装分区</td><td class="art_tbl_td">ntfs</td><td class="art_tbl_td">30G</td></tr>
<tr><td class="art_tbl_td">/dev/sda2</td><td class="art_tbl_td">扩展分区</td><td class="art_tbl_td">-</td><td class="art_tbl_td">-</td><td class="art_tbl_td">-</td></tr>
<tr><td class="art_tbl_td">/dev/sda5</td><td class="art_tbl_td">逻辑分区</td><td class="art_tbl_td">Windows的D盘</td><td class="art_tbl_td">ntfs</td><td class="art_tbl_td">40G</td></tr>
<tr><td class="art_tbl_td">/dev/sda3</td><td class="art_tbl_td">主分区</td><td class="art_tbl_td">/</td><td class="art_tbl_td">ext3</td><td class="art_tbl_td">78G</td></tr>
<tr><td class="art_tbl_td">/dev/sda4</td><td class="art_tbl_td">交换分区</td><td class="art_tbl_td">swap</td><td class="art_tbl_td">-</td><td class="art_tbl_td">2G</td></tr>
</table>
</p>

<p>调整后分区结构：</p>

<p>
<table class="art_tbl">
<tr><th class="art_tbl_th">分区</th><th class="art_tbl_th">类型</th><th class="art_tbl_th">挂载</th><th class="art_tbl_th">文件系统</th><th class="art_tbl_th">容量</th></tr>
<tr><td class="art_tbl_td">/dev/sda1</td><td class="art_tbl_td">主分区，启动分区</td><td class="art_tbl_td">/</td><td class="art_tbl_td">ext3</td><td class="art_tbl_td">30G</td></tr>
<tr><td class="art_tbl_td">/dev/sda2</td><td class="art_tbl_td">主分区</td><td class="art_tbl_td">/home</td><td class="art_tbl_td">ext3</td><td class="art_tbl_td">120G</td></tr>
<tr><td class="art_tbl_td">/dev/sda3</td><td class="art_tbl_td">交换分区</td><td class="art_tbl_td">swap</td><td class="art_tbl_td">-</td><td class="art_tbl_td">2G</td></tr>
</table>
</p>

<p><h2>步骤</h2></p>

<ol>
```bash
sudo grub-install /dev/sda
```
```bash
sudo vi /boot/grub/menu.lst
```
# (2) Arch Linux
title  Arch Linux
root   (hd0,0)
kernel /boot/vmlinuz26 root=/dev/sda1 resume=/dev/sda4 ro acpi_osi="Linux"
initrd /boot/kernel26.img
</blockquote></li>
  <li><a href="http://0x3f.org/?p=1699">安装U盘启动的Puppy Linux</a></li>
```bash
mkfs.ext3 /dev/sda1
```
```bash
# 先点击桌面上sda1和sda3的盘符，使之被挂载到/mnt下

cd /mnt/sda3
cp -a * /mnt/sda1
```
```bash
vi /mnt/sda1/etc/fstab
```
# 修改必要的挂载点，如将根目录的挂载点由sda3改为sda1：
/dev/sda1           /         ext3      defaults,noatime       0       1
</blockquote></li>
  <li>重启系统，并引导到新分区上的系统中</li>
```bash
sudo grub-install /dev/sda
```
  <li>重启并进入Puppy，使用gparted删除除sda1以外的所有分区，然后在空闲的空间上创建ext3格式的主分区sda2和交换分区sda3</li>
```bash
cp -a /mnt/sda1/home/* /mnt/sda2
rm -rf /mnt/sda1/home/*
```
```bash
vi /mnt/sda1/etc/fstab
```
# 将fstab中磁盘分区的挂载点配置为如下内容
/dev/sda1        /          ext3      defaults,noatime         0        1
/dev/sda2        /home      ext3      defaults,noatime         1        2
/dev/sda3        swap       swap      defaults                 0        0
```bash
vi /mnt/boot/grub/menu.lst
```
# 将原来启动项中的sda3修改为sda1，由于配置过休眠，也将resume参数中的sda4改为sda3，即交换分区
</blockquote>
  <li>重启系统</li>
</ol>

<p><h2>后记</h2></p>

<p>从<a href="http://0x3f.org/?tag=arch">Arch</a><a href="http://0x3f.org/?p=819">诞生</a>时只装XP，到<a href="http://0x3f.org/?p=836">装上Archlinux</a>后双系统并存，再到<a href="http://0x3f.org/?p=1180">尝试Win7</a>，两年的时间里，一直有很多原因让Windows像只寄生虫一样顽强地活著。最近硬盘空间越来越紧张，早前装的Win7也很长时间不用了，于是时隔三年之后，我又成了个纯粹的Archer。</p>

