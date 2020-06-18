# 用PlantUML实现ERD


很长一段时间，我们都在用MySQLWorkbench画ER图。但是这个东西有一些缺陷，导致体验很不好。

首先它生成的文件是二进制的，做不了版本控制。这意味着在版本迭代的过程中做数据结构评审的体验很糟糕。其次，离线文件难以管理，不利于团队协作。第三，图形界面虽然简单，但并不等价于高效。用它画图的成本还是比较高的。

PlantUML是个基于文本、灵活且强大的UML画图工具，高度可定制，而且可以集成到多种开发工具中。主流的IDE、文本编辑器都可以集成PlantUML，以便用自己最熟悉的工具画图并实时预览。基于文本的特性使得版本控制和Code Review很容易实现。如果VCS用的是Gitlab，可以更进一步，借助PlantUML Server实现实时预览。即使对于像Github这种暂时没有提供支持的Web平台，也有变通的解决方案。

<!--more-->

基于以上情况，我封装了一个用PlantUML画ER图的模板。用这个模板画图的源码如下：

```plantuml
@startuml

!include https://raw.githubusercontent.com/xbot/plantuml-erd/master/src/Library.iuml

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
posts --> comments

Table(post_logs, 文章日志表) {
    PRIMARY_KEY
    TIMESTAMPS
    Column("post_id", PK_TYPE, 1, "", "文章ID")
    Column("user_id", PK_TYPE, 1, "", "操作者ID")
    Column("action", UN("TINYINT"), 1, "0", "操作类型", "1:创建; 2:修改; 3:删除")
    Column("data", "TEXT", 0, "", "操作详情")
}
posts --> post_logs

@enduml
```

效果：

![Example 1](http://www.plantuml.com/plantuml/proxy?cache=no&src=https://raw.githubusercontent.com/xbot/plantuml-erd/master/examples/Posts.md)

具体的源码和例子可以参考[Git仓库](https://github.com/xbot/plantuml-erd)。

下面说说怎么集成到Gitlab。

首先需要一个可用的PlantUML Server。官方提供了一个现成的、开放的服务（`http://www.plantuml.com/plantuml/`）。如果对响应速度或私密性有要求，就需要部署私有的服务了。

[官方](https://github.com/plantuml/plantuml-server)提供了多种部署方式，我用的是最简单的docker方式。

首先克隆官方仓库。

然后在仓库根目录下执行命令：

```shell
docker run -d -p 18080:8080 plantuml/plantuml-server:jetty
```

其中“18080”是服务器提供对外服务的端口。

最后，把上述服务地址配置到Gitlab里即可，选项路径为`管理区域→Settings→PlantUML`。

之后，可以创建Markdown文件，格式为：

~~~markdown
```plantuml
PlantUML源码
```
~~~

当在Gitlab中点击该文件时，就会显示实时渲染的图了。

对于Github这种暂时不支持集成PlantUML的平台，可以利用PlantUML Server提供的代理服务实现，拼接一个如下所示的URL并当作图片链接插入网页即可：

```
http://www.plantuml.com/plantuml/proxy?cache=no&src=https://raw.githubusercontent.com/xbot/plantuml-erd/master/examples/Posts.md
```


