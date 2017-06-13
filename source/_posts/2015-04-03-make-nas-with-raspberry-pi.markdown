---
layout: post
title: "用樹苺派搭NAS"
date: 2015-04-03 15:13
comments: true
categories: 計算機
tags:
- 樹苺派
- Geek
- 智能家居
---

為了讓盒子能直接播放[遠程下載](/post/remote-downloading-with-raspberry-pi/)的電影，繼續在樹苺派上搭NAS。

安裝samba，然後配置：

{% codeblock lang:ini /etc/samba/smb.conf %}
[nas]
path = /media/sda1
valid users = @users
force group = users
create mask = 0660
directory mask = 0771
read only = no
{% endcodeblock %}

把Linux用戶添加到samba並設置密碼：

{% codeblock lang:bash %}
smbpasswd -a pi
{% endcodeblock %}

盒子上的Kodi硬解有問題，用ES+MX Player替代。電腦上用Kodi。
