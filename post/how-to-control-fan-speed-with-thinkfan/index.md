# 在Archlinux下使用thinkfan控制Thinkpad x220的风扇转速

<p>Archlinux目前对Thinkpad风扇的自动控制效果并不好，气温上升后很容易出现一直在三千多转一直跑的问题。thinkfan是目前用的比较多的自动控制风扇转速的程序，但Google到的配置thinkfan的文章大多语焉不详，可能是跟具体的发行版有关，因为貌似有些发行版中安装了thinkfan后会自动生成缺省的配置文件，但在目前的AUR中的包被安装后却没有生成任何配置文件。</p>

<p>下面是我在Archlinux下配置的步骤，实际效果很好，现在只要不看视频、不编译程序、不启动Chromium，一般是两千九百转的速度，安静多了。</p>

<h2>lm_sensors</h2>

<p>安装lm_sensors并执行命令初始化：</p>

```bash
# 一路回车
sudo sensors-detect
```

<p>将sensors加入rc.conf中DAEMONS中，开机启动。</p>

<p>启动sensors服务：</p>

```bash
sudo rc.d start sensors
```

<h2>thinkpad_acpi</h2>

<p>修改文件“<strong>/etc/modprobe.d/thinkpad_acpi.conf</strong>”：</p>

```
options thinkpad_acpi fan_control=1
```

<p>我这里虽没有把thinkpad_acpi加到rc.conf的MODULES中，但lsmod也是可以看到它的，说明还是自动启用了。要使上面这项配置生效，必须重启电脑，我这里重启thinkpad_acpi模块时报错说该模块正在被使用。</p>

<h2>thinkfan</h2>

<p>安装thinkfan并修改文件“<strong>/etc/default/thinkfan</strong>”：</p>

```
START=yes
```

<p>修改文件“<strong>/etc/thinkfan.conf</strong>”：</p>

```
sensor /sys/class/hwmon/hwmon0/temp1_input

(0,  0, 55)
(1, 48, 60)
(2, 50, 61)
(3, 52, 63)
(4, 56, 65)
(5, 59, 66)
(7, 63, 32767)
```

<p>将thinkfan加入rc.conf的DAEMONS中，开机自动启动。</p>

<p>启动thinkfan：</p>

```bash
sudo thinkfan
```

<h2>查看状态</h2>

```bash
# 看CPU温度和风扇转速
sensors

# 看风扇详细信息
cat /proc/acpi/ibm/fan
```

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

