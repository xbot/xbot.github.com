---
layout: post
title: "PHP框架實戰（∝）：烈焰之終章"
date: 2014-01-02 15:15
comments: true
categories: 計算機
tags:
- PHP
- Flamework
- 框架
- 編程
---

寫“烈焰”（Flame）用了一周的業餘時間，主要是對平時一些想法的總結和驗證。實現了比較完整的控制器層和視圖層，對模型層的ActiveRecord實現思路做了一下梳理。

當然，一個可實用的框架需要包含的東西遠不止這些。比如框架中用到代碼動態調用的地方，一定要做好語言安全子集的過濾，否則就是很大的安全漏洞。再比如需要支持依賴反轉的緩存機制，實現對多種緩存方式的平滑支持。此外，像URI路由、可擴展、多模板方案支持也都是現代框架的標配。這些留待以後有時間再討論。然而在這次練習的過程中，我突然想到一個問題——PHP是不是適合實現一個完備的框架。

曾見過一句話，說PHP本身就是一個框架，後來明白，這才是微言大義。PHP有很多高級選項、高級函數和擴展，用得好事半功倍，用不好就是魔鬼。

PHP本身有很多問題，協議不統一、函數命名混亂、命名空間語法怪異而且雞肋等等都是老生常談。在運行模式上，無論是Apache+PHP模塊，還是NGINX+FastCGI，都只能實現在縱向層面上對一次請求的處理，由於缺乏在內存中持續運行程序的機制，凡是對程序全局共享並持續佔有的東西都不能實現，比如數據庫連接池等，以至於很多初始化的工作對於每次請求都要重新執行一次，這意味著面向對象越徹底、封裝越多，系統資源的重複消耗越厲害，所以PHP的程序在性能和內存佔用上與Java相比有一定缺陷。因此PHP更適合短平快的應用場景，不適合實現複雜的業務邏輯。

基於這個觀點，我認同混合編程。沒有哪種語言是完美的，用對的工具做對的事是最理想的。用PHP實現一個完備的框架也許不是個明智的選擇，從短平快的角度出發，它更適合用來實現微框架。

現在微框架是個比較熱門的話題，我最早接觸的是Python的Bottle和Flask，短小精悍，非常容易上手。微框架主要實現控制器層和視圖層，一般不包括模型層。為了以最快的速度將請求路由到處理邏輯，一般以最直接的方式建立URI模板和回調物件之間的映射，控制器層可以以極簡的方式實現，例如只做一個像本文後面例子中那樣簡單的約定。微框架應該盡可能少地包含配置，大部分時候並不需要像Java的S.S.H那樣濫用配置，[CoC原則](http://en.wikipedia.org/wiki/Convention_over_configuration)就持這樣的觀點，約定可以解決的問題就不要用配置去做。

下面只使用兩個函數和五條約定實現一個微框架：

{% codeblock lang:php %}
<?php
/**
 * 路由定義與應用
 * @param string $route 用作定義路由規則時，此參數為模板字符串，
 *     使用冒號加參數名作為參數佔位符，例如：
 *         on('/post/edit/:id', function($id){});
 *     用做應用路由規則時，此參數為URI，例如：
 *         on($_SERVER['REQUEST_URI']);
 * @param callable $callback 路由規則的回調邏輯，如果路由規則中
 *     含有參數佔位符，回調中需存在同名的參數；當函數作為應用路
 *     由規則使用時，此參數不指定
 * @return void
 * @since 1.0
 */
function on($route, $callback) 
{
    static $routes = array();
    $regex = '#'.preg_replace('#:[^\/]+#', '.*', $route).'#';
    $routes[$route] = array($regex, $callback);
    if (is_null($callback)) {
        foreach ($routes as $r=>$cfg){
            if (preg_match($cfg[0], $route)) {
                $params = array();
                $idx = strpos($r, ':');
                if (is_int($idx)) {
                    $keys = explode('/', substr($r, $idx));
                    $keys = array_map(function($v){ return trim($v, ':'); }, $keys);
                    $values = explode('/', substr($route, $idx));
                    $params = array_combine($keys, $values);
                }
                call_user_func_array($callback, $params);
                break;
            }
        }
        echo '404';
    } 
}

/**
 * 視圖渲染函數
 * @param string $view 視圖名稱
 * @param array $params 關聯數組，包含需要填到視圖模板中的參數鍵值對
 * @return void
 * @since 1.0
 */
function render($view, $params=array()) 
{
    extract($data, EXTR_PREFIX_SAME, 'tpl_');
    $viewFile = dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'view'
        .DIRECTORY_SEPARATOR.$view.'.php';
    if (is_readable($viewFile)) {
        require($viewFile);
    } else {
        throw new Exception("View template $view does not exist or cannot be readable.");
    }
}
?>
{% endcodeblock %}

on()身兼兩用，一是定義路由規則和對應的響應邏輯，一是對指定URI應用路由規則。render()的作用是渲染視圖模板。用法如下：

{% codeblock lang:php %}
<?php
include 'micro.php';

on('/post/save', function(){
    echo "Post saved.\n";
});

on('/mail/send/:address/:title', function($address, $title){
    echo "write letter to $address with title $title\n";
});

on($_SERVER['REQUEST_URI']);
?>
{% endcodeblock %}

約定如下：

  1. 每個Controller作為一個文件放在項目根目錄下的controller目錄中，名稱即文件名，後綴是“.php”；Action對應於Controller中的各個函數（注意過濾語言安全子集）；
  - 使用php.ini的配置項“auto_prepend_file”和“auto_append_file”實現過濾器；
  - 使用“set_error_handler()”和“set_exception_handler()”自動處理異常和錯誤；
  - 使用PDO實現數據庫抽象層；
  - 視圖模板用PHP腳本實現，模板文件放在當前目錄下的view目錄裡，模板文件名即模板名，後綴為“.php”；

當然這離實際可用還差得遠，這裡只是說明一下微框架的基本理念：第一，打狗不需要金箍棒；第二，大部分項目都是在打狗。結合混合編程，這一點會更明顯。
