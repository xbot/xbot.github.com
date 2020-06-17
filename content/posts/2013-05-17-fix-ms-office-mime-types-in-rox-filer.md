---
layout: post
title: "解决ROX-Filer文件类型识别问题"
date: 2013-05-17 12:52:00
comments: true
categories:
- 计算机
tags:
- Linux
- ROX-Filer
- Office
- MIME
---
默认情况下，ROX-Filer会将“\*.docx”、“\*.xlsx”、“\*.pptx”文档识别为zip压缩包。由于ROX中与文件关联的行为实际上都是和文档类型挂钩的，所以对这些文档的操作会遇到很大麻烦，而且没有变通方法。

实际上，Linux对文件类型的识别比Windows灵活。Windows只能通过文件名的后缀判断文件类型，随便创建一个文本文件，然后将后缀“.txt”改成“.exe”，Win就会傻乎乎地认为这是个二进制的可执行程序。这种做法很傻，而且很不安全，试想，如果将一个病毒程序的后缀改成“.jpg”，就可以骗过大多数用户，然后通过某种手段执行它，后果会怎样？更傻的是，XP以上版本默认是隐藏后缀的，那攻击者连改后缀都可以省了。颤抖吧，神一样的Win！

Linux不但可以通过后缀识别文件类型，更重要的，还可以通过文件开头的几个字节实现这一点，这就比前一种方式精确、安全很多。当然，Linux还支持更多的文件类型识别方法。

前面说过，在ROX-Filer下，所有行为都绑定到文件类型上，也就是MIME Types，ROX通过这种方式实现了将可灵活自由定制的文件操作与自身解耦，从而在确保自身稳定的同时实现对高度灵活的自由定制的支持，这是一个很值得学习的实现方式。因此，既然文件的后缀没有问题，ROX仍将它们识别为ZIP格式，原因是什么？显然，最大的嫌疑集中在第二种识别方式上。

一个公开的秘密是，Office文档本身其实就是一个ZIP压缩包，里面包含了描述文档的XML、多媒体文件等成分，只不过MS贱贱地把压缩包的后缀改成了docx之类的东东，加上Windows只能通过后缀识别文件类型，所以很多人不知道这一点。了解了这个，问题的原因就跃然纸上了，既然都是ZIP压缩包，那第二种文件类型识别方式也就区分不出Office文件类型与ZIP压缩包了。

能号出病因，就有方子治病。

既然ROX使用多种文件类型识别方式，那必然有一个优先级的关系，否则就会乱套。打开ROX的MIME Editor：

{% img http://pic.yupoo.com/leninlee/CRNkfq0g/medium.jpg %}

找到MIME类型“application/zip”：

{% img http://pic.yupoo.com/leninlee/CRNkgaEK/medium.jpg %}

打开zip的属性对话框：

{% img http://pic.yupoo.com/leninlee/CRNkfOsQ/medium.jpg %}

可以看到“Contents matching”这一块里，通过检查文件头部的几个字节是否为“PK\003\004”来判断，并且此项判断标准的优先级是40。下面所要做的，就是找到相应文件类型，添加一项条件相同的“Contents matching”，并把优先级设得大于40。
