---
layout: post
title: "IE中使用IFrame上傳文件報錯——TypeError:拒絕訪問"
date: 2013-08-03 01:03
comments: true
categories: 計算機
tags:
- IE
- Javascript
- Dojo
---
場景為：

點擊一個圖標，打開文件選擇對話框，選擇好文件後即時上傳。

之前的實現方式是在一個隱藏表單中放一個file類型的input元素，通過調用input.click()打開對話框，同時監聽input.onchange，最後通過dojo/request/iframe上傳文件。

這個實現方式在Chrome和Firefox裡都沒問題，但是在IE裡報錯：“**TypeError:拒絕訪問**”。原因是IE要求必須通過點擊file類型的input的按鈕打開選擇對話框，否則就報這個錯誤。

解決辦法是修改input元素的樣式，或者直接用dojox.form.Uploader替代。

_因為這個破問題又血戰到半夜，只支持IE9+的世界你們好嗎，還支持IE7+的屌絲傷不起……不起……起……啊。**IE不死，吾難未已！！！**_
