# 禁止屏幕在播放视频时自动关闭

<p>貌似Lose系统下看在线视频的时候不会超时自动关闭屏幕，但是我在Arch下就一直晃鼠标、按键盘来著，这严重违反了DRY原则。于是昨天终于写了这个脚本，当全屏播放视频的时候，关闭屏保和显示器的节能特性，否则则激活它们。由于是在X的级别实现，理论上应当适合所有桌面环境。</p>

```bash
#!/bin/bash
# Baby-sitter of the monitor's DPMS

idle_period=60
ss_switch_off=0
ss_is_off=0

while true; do
    # Read DPMS state
    xset -q|grep "DPMS is Disabled" > /dev/null && ss_is_off=1 || ss_is_off=0
    # Get pid of the current window
    active_window_id=`xprop -root | grep "_NET_ACTIVE_WINDOW(WINDOW)" | cut -d" " -f5`
    decimal_id=`xprop -id $active_window_id | grep PID | cut -d" " -f3`
    # Traverse all libflashplayer.so
    for pid in `ps -ef|grep -v grep|grep libflashplayer.so|awk '{print $2}'`; do
        # If the current window is libflashplayer.so
        if [ "$pid" -eq "$decimal_id" ]; then
            ss_switch_off=1
            break
        else
            ss_switch_off=0
        fi
    done
    if [ $ss_switch_off -eq 1 ]; then
        # Turn off DPMS
        echo Turn off DPMS
        if [ $ss_is_off -eq 0 ]; then
            echo Action
            xset s off
            xset -dpms
        fi
    else
        # Turn on DPMS
        echo Turn on DPMS
        if [ $ss_is_off -eq 1 ]; then
            echo Action
            xset +dpms
            xset s on
        fi
    fi
    sleep $idle_period
done
```

<p>似乎对非全屏播放的情况没有什么好方法。</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

