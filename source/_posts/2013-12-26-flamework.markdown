---
layout: post
title: "PHP框架實戰：Flamework"
date: 2013-12-26 20:24
comments: true
categories: 計算機
tags:
- PHP
- Flamework
- 框架
- 編程
---

從今天開始，逐步實現一個PHP的MVC框架，以踐行平時對這方面的一些想法。

項目信息
--------

 - 名稱：Flamework (_Flame Framework_)
 - 源碼：https://github.com/xbot/flamework

框架特性
--------

**激進**

用PHP高版本引入的新特性，不考慮向前兼容問題，沒有歷史包袱。

**命名空間**

PHP 5.3引入的命名空間可以有效避免類命名衝突，這樣可以使類名看起來更自然，不用再在類名前面加難看的前綴了。

**類的自動加載**

手動include會增加維護的難度，因為經常會出現一個類被從源碼中移除而它的include行還在的問題，這會拖慢程序執行速度、增加內存佔用。

實現類的自動加載可以在類被引用時自動include相應的源碼。

**異常的自動處理**

在設計程序時，一般應該把用戶級的錯誤返回給頁面顯示，或者對一些HTTP錯誤顯示個性化的頁面（_例如人民群眾喜聞樂見的404頁面_），所以在業務邏輯、數據操作這些層一般應該逐級向上拋異常，然後在Controller裡捕獲並加工成頁面可識別的格式（_例如JSON_）。這樣做的缺點是Controller裡每個Action都包含重複的try...catch塊。

異常的自動處理允許開發者指定自定義的異常處理邏輯，將異常處理與普通邏輯解耦，這樣每個Action只需實現它所關注的功能即可。

**過濾器**

過濾器允許面向切面編程，是將橫向邏輯與縱向邏輯解耦的重要工具。Flamework要實現針對Controller和Action兩個級別的過濾器鏈，過濾器可在該級別邏輯前後執行，並能停止該級別邏輯及後續過濾器的執行。

**懶加載**

對盡可能多的資源實現懶加載，例如數據庫連接、類、組件等，目的是提高效率、節約資源。

**參數綁定**

自動將請求中的參數與Action方法的參數綁定，從而避免在Action裡出現通過$\_POST、$\_GET這些數組取參數的髒代碼，也可以自動實現參數的校驗和錯誤處理。

**ActiveRecord**

ORM是對關係模型和對象模型的阻抗不匹配問題的解決方案，ActiveRecord是目前最流行的一種ORM的實現方式。通過AR，可以以更對象化的方式操作關係數據庫的數據。

**依賴注入**

依賴注入是個很好的解耦方法，也可以很優雅地實現懶加載。

目錄
----

  1. [零：代碼規範](/post/flamework-code-spec)
  - [一：框架入口與類的自動加載](/post/flamework-entry)
  - [二：錯誤和異常的自動處理](/post/flamework-error-auto-handling)
  - [三：實現Controller和Filter](/post/flamework-controller-and-filter)
  - [四：視圖的模板與渲染](/post/flamework-view-rendering)
  - [五：ORM與ActiveRecord](/post/flamework-active-record)
  - [六：依賴注入](/post/flamework-dependency-injection)
  - [∝：烈焰之終章](/post/flamework-summary)
