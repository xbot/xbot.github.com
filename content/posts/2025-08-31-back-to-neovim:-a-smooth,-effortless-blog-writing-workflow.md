---
title: "回到 NeoVim —— 一个顺畅无阻的博客写作工作流"
slug: "back-to-neovim-a-smooth-effortless-blog-writing-workflow"
date: 2025-08-31T15:33:35+08:00
draft: true
tags:
- NeoVim
- Obsidian
---
最近把博客写作的主场切回 NeoVim 了 —— 之前用 Obsidian 配合插件 QuickAdd 、 Templater 、 Git 体验也不错，不过涉及到更复杂的文本编辑、批量操作还是 Vim 效率更高。用了一些新的插件和脚本，体验又上了个小台阶。

![20250831153402010-e5f5889a80484cf4e6b089c3b5ead415](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20250831153402010-e5f5889a80484cf4e6b089c3b5ead415.avif)

现在写东西很省心：终端敲一句 hugopost 新建文章，文件自动建好并通过 NeoVim 打开，字体自动调大 4 号，看着不费眼。需要插图的地方按下快捷键就能自动上传图片到图床并生成链接。

专注模式也会自动启用，周围内容会弱化。写完退出，字号又自动切回去，不用手动调。最后发布也很简单，执行命令 hugopost，后面提交、发布都是自动的。

此外还整理了一下图床，把所有图片转换成了 AVIF 格式，空间占用减少了三分之二，加载速度也快了不少。

这种完全按自己习惯搭的流程，写东西时一点不卡壳，舒服到有时候坐下来就想多码几行字～


