---
title: "从 Hexo 到 Hugo"
slug: migrate from hexo to hugo
date: 2020-06-19T17:41:52+08:00
categories:
- 青梅煮酒
tags:
- Hexo
- Hugo
- 博客

---

一切源于我这个颜党的喜新厌旧。

总想找到一个极简而不失现代感的主题，在这个过程中，发现越来越多比较贴近我口味的主题都是适配 Hugo 的。趁最近有时间，就换了过来。

<!--more-->

------

## 创建一个空站点

```bash
# 安装 hugo
brew install hugo

# 创建空站点
hugo new site Blog

# 安装 LoveIt 主题
cd Blog
git init
git submodule add https://github.com/dillonzq/LoveIt.git themes/LoveIt
cp -f themes/LoveIt/exampleSite/config.toml .

# 配置 config.toml

# 在本机预览效果
hugo server -D
```

## 写一篇新文章

```bash
hugo new posts/test.md
```

Hugo 需要自己指定文章的相对路径和文件名，这一点不如 Hexo 自动化程度高。

## 迁移文章

>  注意：以下命令和脚本仅适用于本博客，不加判断地使用可能会修改不需要变动的内容。

### 统一文章后缀

历史原因，用过的文件后缀不止一种，这次把`.mkd`和`.markdown`统一成`.md`。

```bash
rename -s .mkd .md -s .markdown .md *
```

### 删除不兼容的 Front Matter

```bash
gsed -i '/^type:\s*post/d' *
```

### 修改不规范的 Front Matter

我用单分类、多标签的方式管理文章，所以以前都是把分类名直接写在`categories:`后面了，Hugo 要求必须使用连接符前缀另起一行。

```bash
perl -pi -e 's/(?<=^categories:)/\n-/g' *
```

也是因为历史的原因，有的文章没有日期时间，有的格式也不统一。Hugo 对日期时间的格式要求比较严格。这里把所有没有秒的时间后补全。

```bash
perl -pi -e 's/(?<=^date:\s\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}$)/:00/g' *
```

### 替换文章摘要标签

Hugo 不允许“more”两侧有空格：

```bash
gsed -ri 's/!-- more --/!--more--/g' *
```

### 补充和转换复杂内容

此外还有诸如日期时间缺失、转换 Octopress 标签到 Markdown 格式等问题，另外为了保持 Permalink 不变，准备每篇文章都补充一个Front Matter `slug`。所以写了一个 PHP 脚本：

```php
#!/usr/bin/env php
<?php

if ($argc < 2) {
    file_put_contents('php://stderr', "需输入一个合法的文件路径\n");
    exit(1);
}

for ($i=1; $i < $argc; $i++) {
    handle_file($argv[$i]);
}

function handle_file($filePath)
{
    $content = file_get_contents($filePath);
    $lines = explode("\n", $content);

    $isDateMissing = empty(preg_grep('/^date:\s*/', $lines));
    if ($isDateMissing) {
        append_date($filePath, $lines);
    }

    $isSlugMissing = empty(preg_grep('/^slug:\s/', $lines));
    if ($isSlugMissing) {
        append_slug($filePath, $lines);
    }

    convert_hexo_tags($lines);

    file_put_contents($filePath, implode("\n", $lines));
}

function append_date($filePath, &$lines)
{
    // 获取文件名中的日期
    $arr = [];
    preg_match('/\d{4}-\d{2}-\d{2}/', basename($filePath), $arr);
    if (empty($arr)) {
        file_put_contents('php://stderr', "未能从文件名中找到日期\n");
        exit(1);
    }

    foreach ($lines as $idx => $line) {
        if (strpos($line, 'title:') === 0) {
            $lines = array_merge(
                array_slice($lines, 0, $idx + 1),
                ["date: {$arr[0]} 00:00:00"],
                array_slice($lines, $idx + 1)
            );
            break;
        }
    }
}

function append_slug($filePath, &$lines)
{
    if (! preg_match('/(?<=\d{4}-\d{2}-\d{2}-).*(?=\.)/', basename($filePath), $matches)) {
        file_put_contents('php://stderr', "未能从文件名中找到slug\n");
        exit(1);
    }

    foreach ($lines as $idx => $line) {
        if (strpos($line, 'title:') === 0) {
            $lines = array_merge(
                array_slice($lines, 0, $idx + 1),
                ["slug: " . str_replace('-', ' ', $matches[0])],
                array_slice($lines, $idx + 1)
            );
            break;
        }
    }
}

function convert_hexo_tags(&$lines)
{
    foreach ($lines as $idx => $line) {
        if (preg_match('/(?<={%\simg\s).*(?=\s\d+\s%})/', $line, $matches)
            || preg_match('/(?<={%\simg\s).*(?=\s%})/', $line, $matches)
        ) {
            $lines[$idx] = "![]({$matches[0]})";
        }

        if (preg_match('/(?<={%\scodeblock\slang:).*(?=\s[a-zA-Z0-9.]+\s%})/', $line, $matches)
            || preg_match('/(?<={%\scodeblock\slang:).*(?=\s%})/', $line, $matches)
            || preg_match('/(?<={%\scodeblock).*(?=\s%})/', $line, $matches)
        ) {
            $lines[$idx] = "```{$matches[0]}";
        }

        if (preg_match('/{%\sendcodeblock\s%}/', $line)) {
            $lines[$idx] = "```";
        }
    }
}
```

执行：

```bash
ls content/posts | sed "s:^:`pwd`/content/posts/:" | xargs ./convert.php
```

## 实现全文检索

默认的 Lunr 方式开箱即用，但速度较慢，而且中文分词做得不好。所以使用 [Algolia](https://www.algolia.com/) 实现。

注册账号并选用免费方案后，在“API Keys”页面复制“Application ID”、“Search-Only API Key”和“Admin API Key”。

在项目根目录下创建 `.env` 文件：

```ini
ALGOLIA_APP_ID=XXXXXX
ALGOLIA_ADMIN_KEY=XXXXXX
ALGOLIA_INDEX_NAME=0x3f.org
ALGOLIA_INDEX_FILE=public/index.json
```

`APP_ID` 和 `ADMIN_KEY` 是前面复制的三项中的值，`INDEX_NAME` 是 Algolia 上的索引名称，随便起一个就行。`INDEX_FILE` 是生成站点时自动生成的索引文件的路径，一般不需要修改。

安装自动提交索引到 Algolia 的脚本：

```bash
npm install atomic-algolia -g
```

执行提交：

```bash
atomic-algolia
```

因为我所用的主题是支持 Algolia 的，所以把 “Application ID”、“Search-Only Key”和索引名称填到配置文件中对应的项后就可以使用搜索了。

## 添加评论功能

用了很多年 Disqus ，这次打算换用 Github Issues ，选择了 [Utterances](https://utteranc.es/) 。

主题原生支持，注册账号后简单配置即可。

## Favicon

原来简单粗暴地用了唯一在的图标文件，这次改用 [Favicon Generator](https://realfavicongenerator.net/) 生成一套对多平台浏览器优化的图标。

把全套文件放到 `static/` 目录下即可。

## 部署到 Github Pages

Github Pages 支持多种站点生成方式，由于 Hexo 默认使用 master 分支管理生成的静态资源，而站点源码存放在 source 分支，所以我打算沿用这个方案，在继续使用 master 分支存放静态资源的同时，新增一个 hugo 分支，管理源码。

### 手动部署

Hugo 会将生成的静态资源存放在项目根目录下的`public`目录中，所以需要将 master 分支检出到该目录：

```bash
git worktree add -B master public origin/master
```

生成站点并部署：

```bash
hugo
cd public && git add —all
git commit
git push -f origin master
```

很快就能看到新网站了。

### 自动部署

Hugo 并没有提供 Hexo 那样的自动部署命令，所以要么把上述手动部署的命令写成脚本，要么使用自动部署工具，恰好 Github Actions 可以很好地实现。

创建 `.github/workflows/main.yml` 文件：

```yaml
name: github pages

on:
  push:
    branches:
      - hugo

jobs:
  deploy:
    runs-on: ubuntu-18.04
    steps:
      - uses: actions/checkout@v2
        with:
          submodules: true  # Fetch Hugo themes (true OR recursive)
          fetch-depth: 0    # Fetch all history for .GitInfo and .Lastmod

      - name: Setup Hugo
        uses: peaceiris/actions-hugo@v2
        with:
        #   hugo-version: '0.71.1'
          hugo-version: 'latest'
          # extended: true

      - name: Build
        run: hugo --minify

      - name: Deploy
        uses: peaceiris/actions-gh-pages@v3
        with:
          github_token: ${{ secrets.GITHUB_TOKEN }}
          publish_dir: ./public
          publish_branch: master

      - name: Update Algolia Index
        env:
          ALGOLIA_APP_ID: XXXXXX
          ALGOLIA_ADMIN_KEY: ${{ secrets.ALGOLIA_ADMIN_KEY }}
          ALGOLIA_INDEX_NAME: 0x3f.org
          ALGOLIA_INDEX_FILE: public/index.json
        run: |
          sudo apt-get -yqq install npm
          sudo npm install atomic-algolia -g
          atomic-algolia

```

注意替换 `ALGOLIA_APP_ID` 的真实值，并把 Algolia 的“Admin API Key”添加到 Github 项目设置中的“Secrets”里，名称为“ALGOLIA_ADMIN_KEY”。

以后再在 hugo 分支推送新的提交时，Github Actions 就会自动执行部署操作。

------

从06年在中国博客网上开始写文章，到后来换到个人搭建并售卖的 Wordpress，五年颠沛流离的博客生涯终于在12年结束并[定居到 Github 上](/post/migrate-blog-to-octopress/)，之后 Octopress 一用就又是五年。17年，因为 Octopress 转换速度太慢，[换成了 Hexo](/post/migrate-octopress-to-hexo/)。

我想，这次折腾之后，应该能再安分五年吧。

