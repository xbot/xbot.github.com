---
layout: post
title: "自动重映射键盘"
date: 2015-04-17 18:02
comments: true
categories: 计算机
tags:
- Linux
- udev
- 键盘
---

每次键盘拔出再插入时，键盘映射都会失效，要重新执行映射，而且要对不同的键盘应用不同的映射方案。试过直接添加udev规则，即使指定X Display和Xauthority也不成功。所以用pyudev写个脚本（[最新版本](https://github.com/xbot/shell/blob/master/udev.py)）：

{% codeblock lang:python udev.py %}
#!/usr/bin/env python2
# encoding: utf-8

"""
File:        udev.py
Description: udev monitor script.
Author:      Donie Leigh
Email:       donie.leigh at gmail.com
"""

import glib, os, time
from pyudev import Context, Monitor

PID_FILE = "/tmp/udev_monitor.pid"

def remap_pokerii(device):
    """ Do keyboard remapping when PokerII is plugged in.
    """
    if device.get('ID_VENDOR_ID') == '0f39' \
            and device.action == 'add':
        time.sleep(1)
        os.system('setxkbmap')
        os.system('xmodmap ~/.Xmodmap')

def remap_filco(device):
    """ Do keyboard remapping when Filco is plugged in.
    """
    if device.get('ID_VENDOR_ID') == '04d9' \
            and device.action == 'add':
        time.sleep(1)
        os.system('setxkbmap')
        os.system('xmodmap ~/.Xmodmap')

def is_pid_running(pid):
    """ Check if the given pid is running.
    :pid: int
    :returns: bool
    """
    try:
        os.kill(pid, 0)
    except OSError:
        return False
    return True

def write_pid_or_die():
    """ Write the current pid into pid file or exists if there is already a instance running.
    :returns: void
    """
    if os.path.isfile(PID_FILE):
        pid = int(open(PID_FILE).read())
        if is_pid_running(pid):
            print("Process {0} is still running.".format(pid))
            raise SystemExit
        else:
            os.remove(PID_FILE)

    open(PID_FILE, 'w').write(str(os.getpid()))

def main():
    try:
        from pyudev.glib import MonitorObserver
        def device_event(observer, device):
            remap_pokerii(device)
            remap_filco(device)
    except:
        from pyudev.glib import GUDevMonitorObserver as MonitorObserver
        def device_event(observer, action, device):
            remap_pokerii(device)
            remap_filco(device)

    context = Context()
    monitor = Monitor.from_netlink(context)

    monitor.filter_by(subsystem='usb')
    observer = MonitorObserver(monitor)

    observer.connect('device-event', device_event)
    monitor.start()

    glib.MainLoop().run()

if __name__ == '__main__':
    write_pid_or_die()
    try:
        main()
    except KeyboardInterrupt:
        print("Game over.")
{% endcodeblock %}

有个坑，监测到键盘插入事件后要等一秒再应用映射，否则不成功。

这里只用了设备的Vendor ID，可以直接用lsusb看。看更多的设备属性的命令如下：

{% codeblock lang:bash %}
# 监视设备变动
udevadm monitor --environment --udev

# 查看设备属性
udevadm info -a -n [device name]

# 查看文件所属设备的属性
udevadm info -a -p [file name]
{% endcodeblock %}
