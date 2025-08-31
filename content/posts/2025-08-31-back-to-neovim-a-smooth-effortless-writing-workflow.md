---
title: "回到 NeoVim —— 一个顺畅无阻的写作工作流"
slug: "back-to-neovim-a-smooth-effortless-writing-workflow"
date: 2025-08-31T15:33:35+08:00
draft: false
tags:
- 青梅煮酒
- NeoVim
- Vim
- Obsidian
---
最近把写作的主场切回 NeoVim 了 —— 之前用 Obsidian 配合插件 QuickAdd 、 Templater 、 Git 体验也不错，不过涉及到更复杂的文本编辑、批量操作还是 Vim 效率更高。用了一些新的插件和脚本，体验又上了个小台阶。

<iframe width="560" height="315" src="https://www.youtube.com/embed/qITBUGvcGbA?si=k3Y7BV7yyAbOVwml" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

现在写东西很省心：终端敲一句 hugonew 新建文章，文件自动建好并通过 NeoVim 打开，字体自动调大 4 号，看着不费眼。需要插图的地方按下快捷键就能自动上传图片到图床并生成链接。

专注模式也会自动启用，周围内容会弱化。写完退出，字号又自动切回去，不用手动调。最后发布也很简单，执行命令 hugopost，后面提交、发布都是自动的。

此外还整理了一下图床，把所有图片转换成了 AVIF 格式，空间占用减少了三分之二，加载速度也快了不少。

这种完全按自己习惯搭的流程，写东西时一点不卡壳，舒服到有时候坐下来就想多码几行字～

用到的插件和工具有：

- 专注模式：folke/zen-mode.nvim + folke/twilight.nvim
- 预览：iamcco/markdown-preview.nvim
- 根据元数据搜索：tkancf/telescope-markdown-frontmatter.nvim
- 批量搜索和替换：wincent/ferret
- 写作助手：github/copilot.vim
- 文件管理：elihunter173/dirbuf.nvim
- 文件管理和内容搜索：Yggdroot/LeaderF 或 nvim-telescope/telescope.nvim
- 显示图片：folke/snacks.nvim
- 上传图片：nvim-picgo + picgo-core + picgo-plugin-avif + picgo-plugin-rename-file
