---
layout: post
title: "用 Dojo 实现拖放操作的最佳实践"
slug: practical drag and drop implementation with dojo
date: 2013-11-05 16:58:00
comments: true
categories:
- 计算机
tags:
- Dojo
- Javascript
- 编程
---
在研究 Dojo 实现拖放操作时，我花了很多时间处理一些细节问题，比如创建自定义样式的拖拽物件以及将光标放置在拖拽开始位置等。这些细节在实现个性化的拖放操作时非常重要，但是我几乎没有找到直接相关的资料可供参考。

创建自定义样式的拖拽物件
------------------------

Dojo 缺省的拖拽物件样式很丑，通过覆盖官方文档里列出的几个 CSS 的 class 可以有限地调整部分样式，如果需要更多个性化，就需要使用自定义的物件模板。

通过重载 Source 对象的 creator 方法可以实现这一点。这个方法会在创建拖拽物件的时候被调用，如果 hint 参数的值为“avatar”就表示将被创建的是被拖拽物件，此时可以使用预先定义好的模板 avatarTmpl 创建物件的 node 。

```javascript
this.dndSrc = new Source(this.itemList.domNode, {
    copyOnly:true,
    selfAccept:true,
    creator: function(item, hint) {
        var n;
        if (hint == 'avatar') {
            n = domConstruct.toDom(lang.replace(avatarTmpl, item));
        }
        return {node:n, data:item, type:['text']};
    }
});
```

置光标位置于拖拽起始处
----------------------

在拖拽开始后，Dojo 默认将光标置于被拖拽物件的左上角，而一般把光标置于拖拽开始时相对于物件的位置处显得比较自然。

实现方式是先记录拖拽开始时光标的位置，然后设置 dojo.dnd.Manager 的两个位移属性。

```javascript
on(this.domNode, 'mousedown', lang.hitch(this, this._setDndOffset))

_setDndOffset: function(evt){
    // summary: 鼠标按下时将光标相对于组件的位移设置为dojo.dnd.Manager的位移
    //          即使光标在拖动开始后位于拖动开始时的位置
    var cPos = Functions.getCursorPosition(evt);
    var nPos = domGeometry.position(this.domNode);
    Manager.manager().OFFSET_X = nPos.x - cPos.x;
    Manager.manager().OFFSET_Y = nPos.y - cPos.y;
},
```

使鼠标事件穿透被拖拽物件
------------------------

将光标置于物件开始被拖拽时的位置后，物件本身会挡住 mouseover 事件，导致 Target 不能获知物件被拖拽到自己上方，以致能拖不能放。

解决方法是通过 CSS 使鼠标事件穿透被拖拽物件。

```javascript
.dojoDndAvatar {
    pointer-events: none; /*Chrome, FF下使鼠标事件穿透*/
    background:transparent; /*IE下使鼠标事件穿透*/
}
```
