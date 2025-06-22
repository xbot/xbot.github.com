---
title: 手欠升级 Kindle 固件后的曲线救国记
slug: kindle firmware upgrade issue workaround
date: 2025-06-22T16:29:22+08:00
tags:
  - 青梅煮酒
  - Kindle
  - KOReader
toc: false
draft: false
---
手又欠了，没有更新 Hotfix 就给抹茶绿刷了 5.18.3 的固件，然后发现 Hotfix 执行没有效果——越狱失效了……

重刷 Update_hotfix_universal.bin 无效……

但是 sh_integration 还能用，所以目前只能通过 Shell 脚本启动 KOReader 了。好在我越狱的目的就只是用 KOReader ，目前发现的问题，一是屏保不能用 KOReader 显示书的封面了，其次更新 KOReader 只能纯手动操作，还有左上角会显示 Kindle 系统状态栏的时间。