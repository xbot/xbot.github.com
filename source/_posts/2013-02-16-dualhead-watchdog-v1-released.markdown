---
layout: post
title: "DualHead Watchdog v1.0 Released"
date: 2013-02-16 08:56
comments: true
categories: 青梅煮酒
---
**DualHead Watchdog** is a Linux desktop app which runs commands or scripts after display settings are changed.

It is especially usefull for users who prefer window managers to those huge desktop environments. For these people, wallpapers often cannot be ajusted automatically when the screen size is changed or dual-head display is started. With this app, people can run custom commands to reset the background image when the display settings are changed. 

For example, I use i3wm in my daily life and I use dual-head display, my laptop has a screen resolution as 1366x768 and my outer display is 1920x1080. So when I start the dual-head display, the background image in the outer display is tiled with 1366x768 images. So I put the following commandline into this app:

    feh --bg-scale ~/.wallpaper

"~/.wallpaper" is a soft link to my wallpaper. So when dual-head display is started, this command will be called and my wallpaper will be reset to fit the new screen size.

More than that, users can make this app do everything when display settings are changed. At the moment, there will be a 'monitors-changed' and a 'size-changed' event, users can specify which event to listen to for each command.

{% img http://pic.yupoo.com/leninlee/CE7yNP6h/medium.jpg %}

Features
--------

* A KISSy app which does custom jobs on display changes.
* Event-driven, which is resource-saving.
* Can support as many commands as you can set.
* Each command can be enabled or disabled alone.
* Each command can listen to either the monitors-changed event or the size-changed event, or both.

Links
-----

* Project:      https://github.com/xbot/DualHead-Watchdog

FAQ
---

* **How to modify settings ?**
  * Right click the tray icon and select 'Settings'.
* **How to add an entry to the command list ?**
  * Right click the command list and select 'Add new entry'.
* **How to delete an entry in the command list ?**
  * Right click the entry and select 'Delete'.
* **Why doesn't the command take effect ?**
  * Make sure that you have selected the right event to listen and the command is enabled.

Change Log
----------

**version 1.0.0 (2013-02-06 Wednesday 15:14:24)**

* Initial release.
