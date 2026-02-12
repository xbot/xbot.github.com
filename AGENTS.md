始终使用简体中文回复

# Blog 仓库 - AI 代理指南

这是一个基于 Hugo 的静态博客网站，使用 Minima 主题。

## 项目概述

- **框架**: Hugo v0.155.3+extended
- **主题**: minima (当前), LoveIt/ivy (备用)
- **语言**: 简体中文 (zh-cn)
- **部署**: GitHub Pages (通过 CNAME: 0x3f.org)

## 构建命令

### 本地开发
```bash
hugo server -D --disableFastRender
```
- `-D`: 包含草稿
- `--disableFastRender`: 禁用快速渲染以便实时预览更改

### 生产构建
```bash
hugo --gc --minify
```
- `--gc`: 清理未使用的缓存文件
- `--minify`: 压缩输出

### 主题构建 (LoveIt)
```bash
cd themes/LoveIt
npm run build      # 完整构建
npm run start      # 开发服务器
```

## 目录结构

```
├── content/
│   ├── posts/          # 博客文章 (Markdown)
│   ├── about/          # 关于页面
│   └── search/         # 搜索页面
├── layouts/            # 覆盖模板 (当前基本为空)
├── static/             # 静态资源 (图片、favicon 等)
├── archetypes/         # 文章模板
├── themes/             # Hugo 主题 (Git 子模块)
│   ├── minima/         # 当前主题
│   ├── LoveIt/         # 备用主题
│   └── ivy/            # 备用主题
└── config.yaml         # Hugo 配置
```

## 代码风格指南

### 文章命名规范

**必须遵循** `YYYY-MM-DD-title.md` 格式：
- 示例: `2025-01-12-kindle-11th-gen-jailbreak-issues.md`
- Hugo 会自动提取日期，标题使用连字符分隔的小写英文

### Front Matter (YAML)

每篇文章必须包含以下 front matter：

```yaml
---
title: "文章标题"         # 必需，中文或英文
slug: url-slug           # 可选，URL 中的标识
date: 2025-01-12T21:31:26+08:00  # 必需，ISO8601 格式
tags:                    # 可选，标签列表
  - 标签1
  - 标签2
categories:             # 可选，分类列表
  - 分类1
series:                 # 可选，系列文章
  - 系列名称
toc: false               # 可选，是否显示目录 (默认 false)
draft: false             # 必需，是否为草稿
description: ""         # 可选，文章摘要
link: ""                 # 可选，外部链接 (重定向到此 URL)
---
```

### 内容格式



#### 标题层级
- 使用 `#` 作为一级标题 (仅在文档开头使用一次)
- 使用 `##` 作为文章主标题
- 避免跳级使用 (如 `#` 后直接用 `###`)

#### 图片引用
```markdown
![alt text](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/图片文件名)
```
- 图片托管在 GitHub 上的 `xbot/image-hosting` 仓库
- 优先使用 `.avif` 格式 (现代、高效)

#### 代码块
```markdown
\```bash
command_here
\```
```
- 指定语言以启用语法高亮 (VSCode Dark+ 主题)
- 主题支持自动复制代码到剪贴板

### Markdown 扩展

Minima 主题支持以下 Hugo 扩展：

#### 数学公式 (KaTeX)
```yaml
---
math: true
---
```
```markdown
行内公式: $E=mc^2$
块级公式:
$$
\int_{a}^{b} f(x) dx
$$
```

#### 流程图 (Mermaid)
```yaml
---
diagram: true
---
```
```markdown
\```mermaid
graph TD
    A[Start] --> B{End?}
    B -->|No| A
    B -->|Yes| C[Finish]
\```
```

#### 短代码
- **Font Awesome 图标**: `<i class="fa-solid fa-camera"></i>`
- **Admonitions**: 使用标准的 Hugo admonition 短代码

### 中文排版

- 标题使用中文时不要翻译日期后的英文 slug
- 示例: `2025-01-12-kindle-11th-gen-jailbreak-issues.md` 但 title 是中文
- 保持中文全角标点在中文环境中
- 代码、URL 等英文内容周围使用合适的空格

### Git 提交规范

提交消息风格:
```
Content updates.
```

**注意事项**:
- 仓库有三个 `hugo` 相关的远程子模块
- 当前在 `main` 分支，提交历史简洁
- 不要直接修改 `themes/` 中的主题代码 (使用 fork 和 PR 或 layout 覆盖)

## 测试

Hugo 本身没有测试框架。验证步骤：

1. **语法检查**: 运行 `hugo` 命令，确保无错误
2. **链接检查**: LoveIt 主题提供 `npm run check` (htmlproofer)
3. **本地预览**: 使用 `hugo server -D` 检查渲染效果
4. **多语言测试**: 切换 `languageCode` 验证 i18n

## 常见任务

### 创建新文章
```bash
hugo new posts/$(date +%Y-%m-%d)-article-title.md
```
编辑生成的 front matter 和内容。

### 更改主题
编辑 `config.yaml`:
```yaml
theme: "minima"  # 或 "LoveIt" 或 "ivy"
```

### 主题自定义
1. 优先使用 `config.yaml` 中的 `params` 覆盖主题默认值
2. 在 `layouts/` 目录下创建同名模板覆盖主题模板
3. 添加自定义 CSS/JS 到 `static/` 目录

### 图片优化
- 使用 WebP/AVIF 格式
- 压缩图片 (推荐使用 `imagemin` 或 `squoosh`)
- 响应式图片使用 Hugo 的 `image processing` 功能

## 外部服务集成

当前配置的集成:

- **评论**: Utterances (基于 GitHub Issues)
- **搜索**: Fuse.js (客户端搜索)
- **分析**: Google Analytics (UA-93233020-1)
- **RSS**: 自动生成 (`/index.xml`)

## 主题特定配置

### Minima 主题特性
- 深色模式切换
- 多语言支持 (i18n)
- 代码高亮 (VSCode Dark+)
- 数学公式 (KaTeX)
- 流程图 (Mermaid)
- 评论系统 (Disqus/Utterances/Giscus)
- 搜索 (Fuse.js)

### LoveIt 主题特性
- 功能更丰富，适合需要更多定制化的场景
- 支持更多第三方服务 (Algolia 搜索、ECharts 图表、APlayer 音乐播放器等)
- 主题开发需要构建流程 (`npm run build`)

## 注意事项

1. **Git 子模块**: `themes/` 是子模块，修改主题需要:
   - 进入主题目录
   - 推送到自己的 fork
   - 提交 PR 或更新子模块引用

2. **CNAME 文件**: 不要删除 `static/CNAME`，它定义了 GitHub Pages 域名

3. **草稿文章**: 以 `draft: true` 标记，不会在生产构建中显示

4. **日期格式**: 始终使用 ISO8601 格式，包含时区: `T21:31:26+08:00`

5. **Markdown 渲染**: `goldmark.renderer.unsafe: true` 已启用，允许内联 HTML

6. **代码高亮**: `lineNos: true` 已启用，代码块会显示行号
