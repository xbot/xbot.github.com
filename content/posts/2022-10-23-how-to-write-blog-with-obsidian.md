---
title: 用 Obsidian 写博客的方法
slug: How to write blog with Obsidian
date: 2022-10-23 17:09:28+08:00
tags:
- Obsidian
- 最佳实践
- 计算机
toc: false
---

我用 Hugo + GitHub Pages 写博客，之前一直通过命令新建文章，然后在编辑器里一项一项修改 Front Matters ，最后再通过命令提交上去。有一天突然觉得这样太麻烦了，应该可以用图形界面简化一下。

Typora 收费之后，Obsidian 可能是最好的免费 Markdown 编辑器了。这里用到三个插件：QuickAdd 、 Templater 和 Obsidian Git 。

在 Hugo 目录下创建两个子目录 `templates` 和 `scripts` ，设置 Templater 的选项 `Template folder location` 为 `templates` 。

然后在 `templates` 目录下创建模板 `New Post.md`：

```markdown
---
title: "{{VALUE:articleTitle}}"
slug: "{{VALUE:articleSlug}}"
date: {{VALUE:articleTimestamp}}
categories: ["{{VALUE:articleCategory}}"]
tags:
toc: false
draft: true
---


```

在 `scripts` 下创建脚本 `create_new_post.js`：

```javascript
module.exports = async (params) => {
    QuickAdd = params;

    const title = await QuickAdd.quickAddApi.inputPrompt("Blog - Title");
    var slug = await QuickAdd.quickAddApi.inputPrompt("Blog - Slug");
    const category = await QuickAdd.quickAddApi.checkboxPrompt(["计算机", "青梅煮酒", "行见"], ["计算机"]);

    if (typeof slug === 'undefined') {
        slug = title;
    }

    QuickAdd.variables["articleTitle"] = title;
    QuickAdd.variables["articleSlug"] = slug;
    QuickAdd.variables["articleFilename"] = slug.toLowerCase().replace(/[^A-Za-z0-9\s]/g, '').replace(/\s+/g, '-');
    QuickAdd.variables["articleCategory"] = category;
    QuickAdd.variables["articleTimestamp"] = QuickAdd.quickAddApi.date.now('YYYY-MM-DDTHH:mm:ssZ');

    console.log(QuickAdd.variables);
};
```

然后在 QuickAdd 里创建一个新的 Macro ，先添加脚本 `create_new_post` （位置 1），再创建一个 Template Choice （位置 2）：

![2022-10-23-17-57-51-RZsGhP](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-10-23-17-57-51-RZsGhP.png)

然后配置这个 Template Choice ：

![2022-10-23-18-01-23-Y0XuTx](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-10-23-18-01-23-Y0XuTx.png)

接着创建一个类型为 `Macro` 的 Choice ：

![2022-10-23-18-05-07-gG6BW9](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-10-23-18-05-07-gG6BW9.png)

最后配置 Obsidian Git ：

-   `Commit message on manual backup/commit` 填 `Content updates.`
-   选中 `List filenames affected by commit in the commit body`

这样就可以通过以下步骤创建和发布新文章了：

1. 执行 `QuickAdd: Run QuickAdd` 命令。
2. 选择 `Create New Post` 。
3. 填写文章标题等各项内容。
4. 写完后执行 `Obsidian Git: Commit all changes` 。
5. 执行 `Obsidian Git: Push` 。