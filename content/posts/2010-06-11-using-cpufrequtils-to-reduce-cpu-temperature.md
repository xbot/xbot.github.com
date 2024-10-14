---
layout: post
title: 低碳生活：使用 cpufrequtils降低CPU温度
slug: using cpufrequtils to reduce cpu temperature
date: 2010-06-11 00:00:00
tags:
- Archlinux
- 计算机
status: publish
comments: true
meta:
  _edit_last: '1'
  views: '1606'
---
cpufrequtils可以根据不同的方案自动调节CPU的频率，从而达到在系统空闲时降低CPU温度、节省电力的目的。

Archlinux官方wiki已经介绍得很明白了：<a href="http://wiki.archlinux.org/index.php/Cpufrequtils_(简体中文)">http://wiki.archlinux.org/index.php/Cpufrequtils_(简体中文)</a>

使用后效果明显，CPU温度有所下降，下面是cpufrequtils在<a href="http://0x3f.org/?tag=arch">Arch</a>上的工作情况： 

```
[lenin@archer ~]$ cpufreq-info 
cpufrequtils 007: cpufreq-info (C) Dominik Brodowski 2004-2009
Report errors and bugs to cpufreq@vger.kernel.org, please.
analyzing CPU 0:
  driver: acpi-cpufreq
  CPUs which run at the same hardware frequency: 0 1
  CPUs which need to have their frequency coordinated by software: 0
  maximum transition latency: 10.0 us.
  hardware limits: 800 MHz - 2.53 GHz
  available frequency steps: 2.53 GHz, 2.53 GHz, 1.60 GHz, 800 MHz
  available cpufreq governors: ondemand, performance
  current policy: frequency should be within 800 MHz and 1.60 GHz.
                  The governor "ondemand" may decide which speed to use
                  within this range.
  current CPU frequency is 800 MHz.
analyzing CPU 1:
  driver: acpi-cpufreq
  CPUs which run at the same hardware frequency: 0 1
  CPUs which need to have their frequency coordinated by software: 1
  maximum transition latency: 10.0 us.
  hardware limits: 800 MHz - 2.53 GHz
  available frequency steps: 2.53 GHz, 2.53 GHz, 1.60 GHz, 800 MHz
  available cpufreq governors: ondemand, performance
  current policy: frequency should be within 800 MHz and 1.60 GHz.
                  The governor "performance" may decide which speed to use
                  within this range.
  current CPU frequency is 1.60 GHz.
```
