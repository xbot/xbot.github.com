---
title: 鼠须管的美化
slug: beautify squrrel input method
date: 2018-04-21 20:52:42
categories:
- 计算机
tags:
- 最佳实践
- 工具
- 软件
---
效果如下：
![Ulysses_2018-04-21 20-43-42@2x](https://wx2.sinaimg.cn/large/006tNbRwly1fwvx9h8tgtj30n20bqaah.jpg)

尽管鼠须管的Github仓库一直在更新，但释出版已经很久没有更新了，所以下面的美化基于网络上个人编译的版本。[这篇文章](https://scomper.me/gtd/-shu-xu-guan-de-diao-jiao-bi-ji)里有该版本和「花园明朝」字体的下载链接，按照文中的说明替换鼠须管的程序。（_注意：官方当前释出版本鼠须管不支持下文通过patch自定义配置的方式，会造成原配置信息丢失。也不支持图示的样式。_）

然后参考[这篇文章](https://scomper.me/gtd/shu-xu-guan-shu-ru-fa-de-xin-pei-se)，把新的颜色方案移植到用户配置中。

图示样式使用「dust」方案。原色彩方案的字体设置的有点小，需要把font\_point和label\_font\_point分别从14和10修改成18和14。原方案的第一顺序字体是「HYQiHei-55S Book」，要使用图示的花园明朝字体，可以不安装或者从配置信息中去掉该字体。由于原方案中已存在的配置项不能通过patch的方式覆盖，所以只能直接修改squirrel.yaml文件。

