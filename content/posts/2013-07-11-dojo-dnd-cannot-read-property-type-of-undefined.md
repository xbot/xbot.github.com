---
layout: post
title: "Dojo DnD: Cannot read property 'type' of undefined"
date: 2013-07-11 22:44:00
comments: true
categories:
- 计算机
tags:
- Dojo
- Javascript
- 编程
---
场景如下：

假设有两个Widget：ItemListWidget和ItemWidget，后者要被创建多份并追加到前者内部，同时ItemListWidget要作为dojo/dnd/Source，每个ItemWidget作为一个单元可被拖拽到另外一个dojo/dnd/Target容器中。

问题是，当Source被创建后，再添加到ItemListWidget的ItemWidget实例在被拖拽时会报如下错误：

> Uncaught TypeError: Cannot read property 'type' of undefined

在Chrome开发工具中点开这个错误，显示以下内容：

![](http://pic.yupoo.com/leninlee/D0dD4euT/cULuA.png)

从方法的注释或API中可以看到，checkAcceptance()是用来判断当前拖拽对象是否在这个target接受的范围之内，接受规则用Source和Target的构造参数中的“accept”定义。在这个方法里通过Source.getItem()方法拿到的对象是null，上述错误就是从这儿报出来的。

接合API和Reference Guide发现，每个Source中的可拖拽项目在Source中都要有一个对应的对象，这个对象至少包括两个属性：“data”和“type”。在[Reference Guide](http://dojotoolkit.org/reference-guide/1.9/dojo/dnd.html)中，对这两个属性有详细说明，简言之，data是向Target传递的数据，type是被Target用来判断拖拽个体是否属于接受范围的依据。

当Source实例被创建时，已经存在于ItemListWidget中的ItemWidget实例会被自动创建对应的上述对象，但之后加入的不会。解决的办法是调用Source.setItem()方法为每一个新加入的ItemWidget关联相应的对象，或在ItemWidget中添加一个构造参数，用于指定Source，并在postCreate()方法中为当前ItemWidget实例关联相应的对象:

```javascript
define([
    "dojo/_base/declare",
    "dijit/_WidgetBase",
], function(declare, _WidgetBase){
    return declare("ItemWidget", [_WidgetBase], {

        // 本Widget实例所属的Source实例
        dndSrc: null,
        
        postCreate: function() {
            this.inherited(arguments);
            // 不需要传递数据时，可忽略data参数
            if (this.dndSrc !== null)
                this.dndSrc.setItem(this.id, {type:["text"]});
        }
    });
});
```
