---
layout: post
title: "Dojo DnD: Cannot read property 'type' of undefined"
date: 2013-07-11 22:44
comments: true
categories: 計算機
tags:
- Dojo
- Javascript
- 編程
---
場景如下：

假設有兩個Widget：ItemListWidget和ItemWidget，後者要被創建多份並追加到前者內部，同時ItemListWidget要作為dojo/dnd/Source，每個ItemWidget作為一個單元可被拖拽到另外一個dojo/dnd/Target容器中。

問題是，當Source被創建後，再添加到ItemListWidget的ItemWidget實例在被拖拽時會報如下錯誤：

> Uncaught TypeError: Cannot read property 'type' of undefined

在Chrome開發工具中點開這個錯誤，顯示以下內容：

{% img http://pic.yupoo.com/leninlee/D0dD4euT/cULuA.png %}

從方法的註釋或API中可以看到，checkAcceptance()是用來判斷當前拖拽對象是否在這個target接受的範圍之內，接受規則用Source和Target的構造參數中的“accept”定義。在這個方法裡通過Source.getItem()方法拿到的對象是null，上述錯誤就是從這兒報出來的。

接合API和Reference Guide發現，每個Source中的可拖拽項目在Source中都要有一個對應的對象，這個對象至少包括兩個屬性：“data”和“type”。在[Reference Guide](http://dojotoolkit.org/reference-guide/1.9/dojo/dnd.html)中，對這兩個屬性有詳細說明，簡言之，data是向Target傳遞的數據，type是被Target用來判斷拖拽個體是否屬於接受範圍的依據。

當Source實例被創建時，已經存在於ItemListWidget中的ItemWidget實例會被自動創建對應的上述對象，但之後加入的不會。解決的辦法是調用Source.setItem()方法為每一個新加入的ItemWidget關聯相應的對象，或在ItemWidget中添加一個構造參數，用於指定Source，並在postCreate()方法中為當前ItemWidget實例關聯相應的對象:

{% codeblock lang:javascript %}
define([
    "dojo/_base/declare",
    "dijit/_WidgetBase",
], function(declare, _WidgetBase){
    return declare("ItemWidget", [_WidgetBase], {

        // 本Widget實例所屬的Source實例
        dndSrc: null,
        
        postCreate: function() {
            this.inherited(arguments);
            // 不需要傳遞數據時，可忽略data參數
            if (this.dndSrc !== null)
                this.dndSrc.setItem(this.id, {type:["text"]});
        }
    });
});
{% endcodeblock %}
