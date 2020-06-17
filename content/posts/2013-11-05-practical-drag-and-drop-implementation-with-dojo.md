---
layout: post
title: "用Dojo实现拖放操作的最佳实践"
date: 2013-11-05 16:58:00
comments: true
categories:
- 计算机
tags:
- Dojo
- Javascript
- 编程
---
网上关于Dojo实现拖放操作的资料都讲得很浅，所以我在研究这个东西的时候在一些细节上费了不少功夫，例如创建自定义样式的拖拽物件、将光标置于拖拽开始时的位置等等。这些细节一般在实现个性化的拖放操作时都要涉及，但几乎找不到任何直接的资料，所以本文斗胆宣称是当前Dojo拖放的最佳实践。

创建自定义样式的拖拽物件
------------------------

Dojo缺省的拖拽物件样式很丑，通过覆盖官方文档里列出的几个CSS的class可以有限地调整部分样式，如果需要更多个性化，就需要使用自定义的物件模板。

通过重载Source对象的creator方法可以实现这一点。这个方法会在创建拖拽物件的时候被调用，如果hint参数的值为“avatar”就表示将被创建的是被拖拽物件，此时可以使用预先定义好的模板avatarTmpl创建物件的node。

{% codeblock lang:javascript %}
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
{% endcodeblock %}

置光标位置于拖拽起始处
----------------------

在拖拽开始后，Dojo默认将光标置于被拖拽物件的左上角，而一般把光标置于拖拽开始时相对于物件的位置处显得比较自然。

实现方式是先记录拖拽开始时光标的位置，然后设置dojo.dnd.Manager的两个位移属性。

{% codeblock lang:javascript %}
on(this.domNode, 'mousedown', lang.hitch(this, this._setDndOffset))

_setDndOffset: function(evt){
    // summary: 鼠标按下时将光标相对于组件的位移设置为dojo.dnd.Manager的位移
    //          即使光标在拖动开始后位于拖动开始时的位置
    var cPos = Functions.getCursorPosition(evt);
    var nPos = domGeometry.position(this.domNode);
    Manager.manager().OFFSET_X = nPos.x - cPos.x;
    Manager.manager().OFFSET_Y = nPos.y - cPos.y;
},
{% endcodeblock %}

使鼠标事件穿透被拖拽物件
------------------------

将光标置于物件开始被拖拽时的位置后，物件本身会挡住mouseover事件，导致Target不能获知物件被拖拽到自己上方，以致能拖不能放。

解决方法是通过CSS使鼠标事件穿透被拖拽物件。

{% codeblock lang:javascript %}
.dojoDndAvatar {
    pointer-events: none; /*Chrome, FF下使鼠标事件穿透*/
    background:transparent; /*IE下使鼠标事件穿透*/
}
{% endcodeblock %}
