---
title: 用Raspberry Pi实现音频Airplay
slug: implement airplay with raspberry pi
date: 2017-04-17 10:19:51
tags:
- 树莓派
- 最佳实践
- 计算机
---

![](http://ww1.sinaimg.cn/large/006tKfTcly1fepi05fa8ej31kw1kwx6q.jpg)

用树莓派做音频airplay效果不错，老书架音箱又可以用起来了。

实现方式是shairplay-sync，利用HDMI转VGA线上的3.5mm音频输出孔，因为我的树莓派2B的3.5mm音视频输出口没有合适的转接头。

我用的是Arch Linux ARM，需要手动开启HDMI输出。修改`/boot/config.txt`：

```ini
hdmi_force_hotplug=1
hdmi_drive=2
config_hdmi_boost=4
dtparam=audio=on
```

重启后，用alsamixer把树莓派音量调整到最大值。

AUR里没有针对ARM的shairplay-sync，需要手动编译：

```bash
git clone https://github.com/mikebrady/shairport-sync.git
```

```bash
# 生成configure
autoreconf -i -f
```

```bash
# 针对systemd编译

./configure --sysconfdir=/etc --with-alsa --with-avahi --with-ssl=openssl --with-metadata --with-soxr --with-systemd


# END
```

```bash
# 如果shairport-sync用户不存在，新增用户和用户组

getent group shairport-sync &>/dev/null || sudo groupadd -r shairport-sync >/dev/null

getent passwd shairport-sync &> /dev/null || sudo useradd -r -M -g shairport-sync -s /usr/bin/nologin -G audio shairport-sync >/dev/null


# END
```

airplay默认的服务名和树莓派的hostname一致，可以到`/etc/shairport-sync.conf`里修改：

```conf
general = {
    name = "pi";
}
```

## FAQ

### ALSA lib confmisc.c:767:(parse_card) cannot find card '0'
树莓派没声音，尝试用mpg123播放mp3时报这个错。解决办法是在`/boot/config.txt`里增加`dtparam=audio=on`。

