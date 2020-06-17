---
title: 把博客字体改成了方正北魏楷书
date: 2017-08-14 16:05:30
categories:
- 计算机
tags:
- 博客
- 笔记
---
我曾说过[方正北魏楷书是最佳的阅读字体](/post/best-practices-of-calibre-on-formatting/)，不过后来感觉长时间阅读的体验还是普通的楷体更好。但这并不妨碍在Reeder、博客这种短时阅读场合突出页面的设计感。

今天把博客（hexo）的webfont改成了方正北魏楷书，用font-spider压缩后，从13M缩减到1.4M。方法是这样的：

首先把完整的字体复制到主题的字体目录中（themes/crisp/source/fonts），文件名为`FZBeiWeiKaiShu-full.ttf`。

在CSS中加入webfont配置：

```css
@font-face {
  	font-family: 'FZBeiWeiKaiShu';
    src: url('../fonts/FZBeiWeiKaiShu.ttf') format('truetype'),
         url('../fonts/FZBeiWeiKaiShu-full.ttf') format('truetype');
}
```

`FZBeiWeiKaiShu.ttf`是压缩后的字体文件名，这样访问时浏览器会优先下载压缩后的字体。

然后把CSS中使用字体的地方都指定为`FZBeiWeiKaiShu`。

在博客根目录下创建脚本`update_fonts.sh`：

```bash
#!/bin/sh

hexo clean
hexo g

find public -name "*.html" |xargs sed -i '' "s#/styles/crisp.css#$HOME/Projects/blog/public/styles/crisp.css#g"
find public -name "*.html"|xargs font-spider

cp -f public/fonts/FZBeiWeiKaiShu.ttf themes/crisp/source/fonts/FZBeiWeiKaiShu.ttf

hexo clean
hexo s -g
```

除非用到新的字符，否则不需要经常更新字体。

font-spider会提示一共使用了多少种字符，我这么多年也只用到三千多种，真是太没文化了。🤥

