---
layout: post
title: "小確幸，用Vim編輯當前命令行"
date: 2016-12-26 11:58
comments: true
categories: 計算機
tags:
- linux
- cli
- mac
---

修改很長的命令是件痛苦的事，在Linux下，可以通過快捷鍵`Ctrl+X Ctrl+E`調用`$EDITOR`快速編輯當前命令行的內容，保存退出後，結果會呈現在光標下。

不過在Mac OS的iTerm2下，似乎是因為`Ctrl+X`被佔用而不能生效。誤打誤撞地發現了另外一個方法，使用oh-my-zsh並且開啓了vi mode的話，先進入vi mode，然後輸入`v`，同樣可以實現這樣的功能。
