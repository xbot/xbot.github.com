---
layout: post
title: Linux的内存使用机制
slug: linux memory usage
date: 2010-12-24 00:00:00
tags:
- BASH
- Linux
- 计算机
status: publish
comments: true
meta:
  _edit_last: '1'
  views: '764'
  _wp_old_slug: ''
---
在top中，内存使用情况显示为如下格式：

<blockquote>
Mem:   3056828k total,  2624472k used,   432356k free,   185196k buffers
Swap:  2096476k total,        0k used,  2096476k free,  1613592k cached
</blockquote>

根据内存的使用情况，将内存空间划分为四种类型：已使用（used），未使用（free），缓冲区（buffers）和已缓存（cached）。

“已使用”是指目前正被使用的活跃的内存区域。“未使用”是指当前空闲的内存区域。“缓冲区”是用于存放即将写入存储介质的数据的区域。“已缓存”是指曾因需要而被读入内存、但目前已不被使用的数据。Linux通常会在资源使用完毕后保留一部分数据在内存中而不全部释放，这就是“已缓存”区域，这样当这些数据再次被使用时，系统就可以直接从内存中读取。而“缓冲区”用于将对存储介质的写操作集中进行。因此，“缓冲区”和“已缓存”特性对减少存储介质IO和加速系统运行都具有非常重要的作用。

所以，Linux运行一段时间后，通常看起来空闲内存都很小，其实是因为很大一部分被用作“已缓存”区域，这部分内存会在内存资源紧张时被自动释放，也可以通过如下命令手工释放：

```bash
echo 1 > /proc/sys/vm/drop_caches
```
