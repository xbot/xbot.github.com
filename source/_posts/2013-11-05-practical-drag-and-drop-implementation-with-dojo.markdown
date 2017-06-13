---
layout: post
title: "用Dojo實現拖放操作的最佳實踐"
date: 2013-11-05 16:58
comments: true
categories: 計算機
tags:
- Dojo
- Javascript
- 編程
---
網上關於Dojo實現拖放操作的資料都講得很淺，所以我在研究這個東西的時候在一些細節上費了不少功夫，例如創建自定義樣式的拖拽物件、將光標置於拖拽開始時的位置等等。這些細節一般在實現個性化的拖放操作時都要涉及，但幾乎找不到任何直接的資料，所以本文斗膽宣稱是當前Dojo拖放的最佳實踐。

創建自定義樣式的拖拽物件
------------------------

Dojo缺省的拖拽物件樣式很醜，通過覆蓋官方文檔裡列出的幾個CSS的class可以有限地調整部分樣式，如果需要更多個性化，就需要使用自定義的物件模板。

通過重載Source對象的creator方法可以實現這一點。這個方法會在創建拖拽物件的時候被調用，如果hint參數的值為“avatar”就表示將被創建的是被拖拽物件，此時可以使用預先定義好的模板avatarTmpl創建物件的node。

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

置光標位置於拖拽起始處
----------------------

在拖拽開始後，Dojo默認將光標置於被拖拽物件的左上角，而一般把光標置於拖拽開始時相對於物件的位置處顯得比較自然。

實現方式是先記錄拖拽開始時光標的位置，然後設置dojo.dnd.Manager的兩個位移屬性。

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

使鼠標事件穿透被拖拽物件
------------------------

將光標置於物件開始被拖拽時的位置後，物件本身會擋住mouseover事件，導致Target不能獲知物件被拖拽到自己上方，以致能拖不能放。

解決方法是通過CSS使鼠標事件穿透被拖拽物件。

{% codeblock lang:javascript %}
.dojoDndAvatar {
    pointer-events: none; /*Chrome, FF下使鼠标事件穿透*/
    background:transparent; /*IE下使鼠标事件穿透*/
}
{% endcodeblock %}
