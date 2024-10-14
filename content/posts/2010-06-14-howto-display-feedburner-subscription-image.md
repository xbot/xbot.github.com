---
layout: post
title: 中转feedburner订阅数图标
slug: howto display feedburner subscription image
date: 2010-06-14 00:00:00
category: 计算机
tags:
- feedburner
- PHP
- 博客
- 编程
status: publish
comments: true
---

利用国外服务器中转feedburner的图标：

```php
<?php
/*
 * 将$fburl替换为自己的feedburner订阅数图片地址，然后将博客中的图片地址换成此脚本的URL即可
 */
$fburl = 'http://feeds.feedburner.com/~fc/leninlee?bg=99CCFF&amp;fg=444444&amp;anim=0';
$fbfl = 'fb.gif';
$fp = fopen($fburl, 'rb');
if ($fp) {
    $fp_local = fopen($fbfl, 'wb');
    if ($fp_local) {
        while (!feof($fp)) {
            fwrite($fp_local, fread($fp, 1024*8), 1024*8);
        }
        fclose($fp_local);
    }
    fclose($fp);
}
header('Location: '.$fbfl);
?>
```

或者：

```php
<?php
header('Content-Type: image/gif');
readfile('http://feeds.feedburner.com/~fc/leninlee?bg=99CCFF&fg=444444&anim=0');
?>
```
