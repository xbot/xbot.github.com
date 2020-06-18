# 用树苺派搭NAS


为了让盒子能直接播放[远程下载](/post/remote-downloading-with-raspberry-pi/)的电影，继续在树苺派上搭NAS。

安装samba，然后配置：

```ini /etc/samba/smb.conf
[nas]
path = /media/sda1
valid users = @users
force group = users
create mask = 0660
directory mask = 0771
read only = no
```

把Linux用户添加到samba并设置密码：

```bash
smbpasswd -a pi
```

盒子上的Kodi硬解有问题，用ES+MX Player替代。电脑上用Kodi。

