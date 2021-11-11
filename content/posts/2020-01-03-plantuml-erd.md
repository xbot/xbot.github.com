---
title: 用 PlantUML 实现 ERD
slug: plantuml erd
date: 2020-01-03 17:01:41
categories:
- 计算机
tags:
- 编程

---

很长一段时间，我们都在用 MySQLWorkbench 画 ER 图。但是这个东西有一些缺陷，导致体验很不好。

首先它生成的文件是二进制的，做不了版本控制。这意味着在版本迭代的过程中做数据结构评审的体验很糟糕。其次，离线文件难以管理，不利于团队协作。第三，图形界面虽然简单，但并不等价于高效。用它画图的成本还是比较高的。

PlantUML 是个基于文本、灵活且强大的 UML 画图工具，高度可定制，而且可以集成到多种开发工具中。主流的 IDE 、文本编辑器都可以集成 PlantUML ，以便用自己最熟悉的工具画图并实时预览。基于文本的特性使得版本控制和 Code Review 很容易实现。如果 VCS 用的是 Gitlab ，可以更进一步，借助 PlantUML Server 实现实时预览。即使对于像 Github 这种暂时没有提供支持的 Web 平台，也有变通的解决方案。

<!--more-->

基于以上情况，我封装了一个用 PlantUML 画 ER 图的模板。用这个模板画图的源码如下：

```plantuml
@startuml

!include https://raw.githubusercontent.com/xbot/plantuml-erd/master/src/Library.iuml

' avoid problems with angled crows feet
skinparam linetype ortho

''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
' Entities
'
' Relationships
' one-to-one relationship
'     user -- user_profile : "A user only \nhas one profile"
' one to may relationship
'     user --> session : "A user may have\n many sessions"
' many to many relationship
' Add mark if you like
'     user "1" --> "*" user_group : "A user may be \nin many groups"
'     group "1" --> "0..N" user_group : "A group may \ncontain many users"
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''

Table(posts, 文章表) {
    PRIMARY_KEY
    TIMESTAMPS
    Column("title", "NVARCHAR[30]", 1, "", "标题")
    Column("status", UN("TINYINT"), 1, "1", "状态", "1:草稿; 2:已发布")
    Column("source", UN("TINYINT"), 1, "", "来源：1，原创；2，转载")
    Column("category_id", PK_TYPE, 1, "", "分类")
    Column("created_by", PK_TYPE, 1, "0", "创建者ID")
    Column("published_at", "DATETIME", 0, "", "发表时间")
    Column("content", "TEXT", 1, "", "内容")
}

Table(comments, 评论表) {
    PRIMARY_KEY
    TIMESTAMPS
    Column("post_id", PK_TYPE, 1, "", "文章ID")
    Column("user_id", PK_TYPE, 1, "", "用户ID")
    Column("content", "NVARCHAR[255]", 1, "", "评论内容")
}

Table(post_logs, 文章日志表) {
    PRIMARY_KEY
    TIMESTAMPS
    Column("post_id", PK_TYPE, 1, "", "文章ID")
    Column("user_id", PK_TYPE, 1, "", "操作者ID")
    Column("action", UN("TINYINT"), 1, "0", "操作类型", "1:创建; 2:修改; 3:删除")
    Column("data", "TEXT", 0, "", "操作详情")
}

posts ||..o{ comments
posts ||..|{ post_logs

@enduml
```

效果：

![2021-11-17-21-56-14-nLLRJnD157xlhnZtHJ3q1k454aC3JMoGfYchaOXffjsXtR2TsSpCWXimmI0NBuX48YfUq8WXqK0q1kJIy6VORNdYBpYpIw6srSI4n7dPcJFd-ywSSxwPxM4SKcuLZK3WaevpXgKXKE3SP5tXC8NZeRpE2rRMOeZc2EO8yr2E5CDtiuI7JGDYYGmYgeMBa754mupcmjVrB8NK3kaUwsK0nuYk0PEIh86A38mBHW1nta0Qo54o](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2021-11-17-21-56-14-nLLRJnD157xlhnZtHJ3q1k454aC3JMoGfYchaOXffjsXtR2TsSpCWXimmI0NBuX48YfUq8WXqK0q1kJIy6VORNdYBpYpIw6srSI4n7dPcJFd-ywSSxwPxM4SKcuLZK3WaevpXgKXKE3SP5tXC8NZeRpE2rRMOeZc2EO8yr2E5CDtiuI7JGDYYGmYgeMBa754mupcmjVrB8NK3kaUwsK0nuYk0PEIh86A38mBHW1nta0Qo54o.svg)

具体的源码和例子可以参考 [Git 仓库](https://github.com/xbot/plantuml-erd)。

下面说说怎么集成到 Gitlab。

首先需要一个可用的 PlantUML Server 。官方提供了一个现成的、开放的服务（`http://www.plantuml.com/plantuml/`）。如果对响应速度或私密性有要求，就需要部署私有的服务了。

[官方](https://github.com/plantuml/plantuml-server)提供了多种部署方式，我用的是最简单的 docker 方式。

首先克隆官方仓库。

然后在仓库根目录下执行命令：

```shell
docker run -d -p 18080:8080 plantuml/plantuml-server:jetty
```

其中“18080”是服务器提供对外服务的端口。

最后，把上述服务地址配置到 Gitlab 里即可，选项路径为`管理区域→Settings→PlantUML`。

之后，可以创建 Markdown 文件，格式为：

~~~markdown
```plantuml
PlantUML源码
```
~~~

当在 Gitlab 中点击该文件时，就会显示实时渲染的图了。

对于 Github 这种暂时不支持集成 PlantUML 的平台，可以利用 PlantUML Server 提供的代理服务实现，拼接一个如下所示的 URL 并当作图片链接插入网页即可：

```
http://www.plantuml.com/plantuml/proxy?cache=no&src=https://raw.githubusercontent.com/xbot/plantuml-erd/master/examples/Posts.md
```

