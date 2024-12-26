---
title: Pixel 5 每月更新后重刷 Magisk 的步骤
slug: Steps to Reflash Magisk After Pixel 5 Monthly Update
date: 2022-06-25 17:22:57+08:00
tags:
- Pixel
- Magisk
- Android
- 青梅煮酒
---

1. 下载最新的 [Factory Image](https://developers.google.com/android/images#redfin) 。
2. 解压两次得到 boot.img 。
3. 用数据线连接电脑并允许 USB 调试。
4. 复制 boot.img 到手机

    ```bash
    adb push boot.img /sdcard/Download/
    ```

5. 在手机上的 Magisk 里给 boot.img 打补丁。
6. 执行命令

    ```bash
    # 把打完补丁的文件复制到电脑上
    adb pull /sdcard/Download/magisk_patched-25101_MNbd5.img .

    # 启动到 bootloader
    adb reboot bootloader

    # 查看设备连接正常
    fastboot devices

    # 刷 boot.img
    fastboot flash boot_a magisk_patched-25101_MNbd5.img
    fastboot flash boot_b magisk_patched-25101_MNbd5.img

    # 重启
    fastboot reboot
    ```


### 参考
- [How to Patch Stock Boot Image via Magisk and Flash it using Fastboot](https://www.droidwin.com/patch-stock-boot-image-flash-magisk/)
- [Root Google Pixel 5 Android 12 using Magisk](https://www.androidinfotech.com/33493-root-google-pixel-5-redfin-android-12/)

# 附：更新 Pixel 5 的 Magisk 缩小状态栏的模块的步骤

直接覆盖原模块会导致功能不生效。

1. 移除原模块并重启。
2. [下载](https://forum.xda-developers.com/t/statusbar-size-fix.4185525/)匹配系统版本的模块，复制新压缩包到手机

    ```bash
    adb push LCwaterfall.zip /sdcard/Download/
    ```

3. 安装模块并重启。
