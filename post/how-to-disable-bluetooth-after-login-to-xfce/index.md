# 登入XFCE后自动关闭蓝牙的方法

<p>Blueman不能记忆蓝牙的关闭状态，每次登入桌面都会自动打开蓝牙，既费电又不安全。</p>

<p>一种方法是禁止bluetooth服务自动启动，缺点是使用蓝牙时不方便，还要手工启动bluetooth服务。</p>

<p>另一种方法是登入桌面后自动关闭蓝牙，即使用如下命令：</p>

```bash
sudo rfkill block bluetooth
```

<p>但XFCE不能定制自动启动程序的顺序，所以可以写下面这么个脚本，然后添加到自动启动程序列表中：</p>

```bash
#!/bin/bash

blueman-applet &
sleep 5
sudo rfkill block bluetooth
```

<p>当然，还要取消自动启动程序列表中原来的Blueman。</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

