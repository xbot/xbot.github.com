---
layout: post
title: "PHP框架實戰（一）：框架入口與類的自動加載"
date: 2013-12-28 01:56
comments: true
categories: 計算機
tags:
- PHP
- Flamework
- 框架
- 編程
---

目標
----

實現框架入口和類的自動加載。

獲取代碼
--------

{% codeblock lang:bash %}
git checkout v0.1
{% endcodeblock %}

設計與實現
----------

通過兩個類實現本次目標。

靜態類Flame作為整個框架的入口，實現一系列框架級公用靜態方法（_例如創建應用實例和自動加載類_）。

WebApplication是應用的抽象層，實現應用的入口和其它應用級（**即運行時**）公用方法（_例如讀取配置文件_）。

**程序入口**

{% codeblock lang:php %}
<?php
// ...
Flame::createApplication($config)->run();
?>
{% endcodeblock %}

此處傳入的$config是應用配置文件的路徑，該配置文件內容格式如下：

{% codeblock lang:php %}
<?php
return array(
    'opt1' => 'val1',
    // ...
);
?>
{% endcodeblock %}

在文件中直接return一個關聯數組的好處是，加載該文件時，include()函數的返回值就是該數組，代碼更簡潔，效率比解析其它格式配置文件高。

**類的自動加載**

{% codeblock lang:php %}
<?php
// ...
class Flame {

    // ...

    public static function autoload($className)
    {
        if (!isset(self::$_namespaces[__NAMESPACE__]))
            self::$_namespaces[__NAMESPACE__] = dirname(__FILE__);
        foreach (self::$_namespaces as $ns=>$path){
            if (strpos($className, $ns) === 0) {
                $classFile = $path.str_replace('\\', DIRECTORY_SEPARATOR, substr($className, strlen($ns))).'.php';
                include($classFile);
                return class_exists($className);
            }
        }
        return false;
    }

    // ...

}

spl_autoload_register(__NAMESPACE__.'\\Flame::autoload');
?>
{% endcodeblock %}

通過函數spl_autoload_register()註冊Flame::autoload()方法。當用到一個類時，PHP會先檢查該類是否已加載，如果沒有，就把包括命名空間在內的完整的類名傳遞給autoload()，最終完成類的加載。

> 當不填參數調用spl_autoload_register()時，PHP使用默認的spl_autoload()函數加載類文件，如果命名空間與目錄結構完全對應，也可以實現自動加載，而且效率比指定的自定義加載方法更高。但是spl_autoload()有個歷久彌新的Bug，它總是試圖加載文件名是小寫字母的文件，例如，假如類名是MyClass，spl_autoload()就去找文件名為myclass.php的文件，然而一般情況是文件名與類名相同，即MyClass.php，這在大小寫敏感的系統中就找不到了。這個函數的開發者得有多恨Linux啊。

驗證Demo
--------

整個Demo程序的index.php只需包含以下代碼即可：

{% codeblock lang:php %}
<?php
require_once '../flamework/Flame.php';
use org\x3f\flamework\Flame as Flame;

$_config = 'protected/config.php';

Flame::createApplication($_config)->run();
?>
{% endcodeblock %}

總結
----

這樣一個最簡單的框架就實現了。

通過這兩個類，將框架級靜態邏輯與運行時的動態邏輯分開。對應用高度的抽象和OOP封裝使框架和應用的代碼更易讀。單一的應用入口也方便實現一些橫向邏輯（_例如過濾器_）。

此外，利用include()會將它所加載的腳本中return的值作為自身返回值的特性實現配置文件的讀取，簡化了代碼，提高了效率。類的自動加載使開發者不必再關心類文件的引入和移除，避免因多餘的include或require而導致效率下降和資源浪費。
