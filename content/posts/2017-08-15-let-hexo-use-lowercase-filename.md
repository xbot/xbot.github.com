---
title: 让hexo使用小写文件名
slug: let hexo use lowercase filename
date: 2017-08-15 13:25:50
tags:
- hexo
- 计算机
---
hexo在生成tag目录时默认保持大小写，如果tag名称掺杂了大小写，会导致用tag索引文章的链接无法访问。解决的办法是强制hexo使用小写的文件或目录名，修改`_config.yml`：

```yml
filename_case: 1
```

即便如此，还要把之前生成的结果从托管服务器上删除，不过在Mac这样的不区分大小写的文件系统中，直接部署新生成的结果是无效的，这时需要删除hexo目录下的`.deploy_git`目录并重新部署：

```bash
rm -rf .deploy_git

hexo clean
hexo d -g
```
