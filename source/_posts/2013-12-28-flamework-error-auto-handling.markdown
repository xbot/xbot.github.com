---
layout: post
title: "PHP框架實戰（二）：錯誤和異常的自動處理"
date: 2013-12-28 13:40
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

實現錯誤和異常的自動捕獲和處理。

獲取代碼
--------

{% codeblock lang:bash %}
git checkout v0.2
{% endcodeblock %}

設計與實現
----------

使用set_error_handler()和set_exception_handler()兩個函數註冊錯誤和異常的處理方法，並在兩個處理方法中先調用用戶自定義的錯誤和異常處理邏輯，如果自定義邏輯不存在或者返回false，繼續調用框架缺省的處理邏輯，輸出錯誤信息到頁面。

{% codeblock lang:php %}
<?php
class WebApplication {
    // ...

    /**
     * @var boolean Whether to enable error auto-handling, default to true 
     * @since 1.0
     */
    public $enableErrorHandling = true;
    /**
     * @var boolean Whether to enable exception auto-handling, default to true 
     * @since 1.0
     */
    public $enableExceptionHandling = true;
    /**
     * @var callable Error handler 
     * @since 1.0
     */
    public $errorHandler;
    /**
     * @var callable Exception handler 
     * @since 1.0
     */
    public $exceptionHandler;
    /**
     * @var boolean Whether to enable debug mode, default to false 
     * @since 1.0
     */
    public $debug = false;

    /**
     * @param string $config
     */
    public function __construct($config)
    {
        // ...

        $this->initErrorHandlers();
    }

    /**
     * Initialize auto-handling for errors and exceptions
     * @return void
     * @since 1.0
     */
    public function initErrorHandlers()
    {
        if ($this->enableErrorHandling == true)
            set_error_handler(array($this, 'handleError'), error_reporting());
        if ($this->enableExceptionHandling == true)
            set_exception_handler(array($this, 'handleException'));
    }
    
    /**
     * Handle errors
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @return void
     * @since 1.0
     */
    public function handleError($code, $message, $file, $line)
    {
        // prevent recursive errors
        restore_error_handler();
        restore_exception_handler();

        $msg = "Error $code: $message ($file:$line)";
        Flame::error($msg);

        // let errorHandler() return true to prevent displayError()
        if (is_callable($this->errorHandler) && call_user_func($this->errorHandler, $code, $message, $file, $line) !== true)
            $this->displayError($code, $message, $file, $line);

        exit(1);
    }
    
    /**
     * Handle exceptions
     * @param Exception $exception
     * @return void
     * @since 1.0
     */
    public function handleException($exception)
    {
        // prevent recursive errors
        restore_error_handler();
        restore_exception_handler();

        $msg = get_class($exception).': '.$exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine()."\n".$exception->getTraceAsString();
        Flame::error($msg);

        // let exceptionHandler() return true to prevent displayException()
        if (is_callable($this->exceptionHandler) && call_user_func($this->exceptionHandler, $exception) !== true)
            $this->displayException($exception);

        exit(1);
    }
    
    /**
     * Display error information
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @return void
     * @since 1.0
     */
    public function displayError($code, $message, $file, $line)
    {
        if ($this->debug == true) {
            echo "<h1>Error $code</h1>";
            echo "<p>$message in ($file:$line)</p>";
            echo '<pre>';
            debug_print_backtrace();
            echo '</pre>';
        } else {
            echo "<h1>Error $code</h1>";
            echo "<p>$message</p>";
        }
    }
    
    /**
     * Display exception information
     * @param Exception $exception
     * @return void
     * @since 1.0
     */
    public function displayException($exception)
    {
        if ($this->debug == true) {
            echo '<h1>'.get_class($exception).'</h1>';
            echo '<p>'.$exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine().')</p>';
            echo '<pre>'.$exception->getTraceAsString().'</pre>';
        } else {
            echo '<h1>'.get_class($exception).'</h1>';
            echo '<p>'.$exception->getMessage().'</p>';
        }
    }
    
    // ...
}
?>
{% endcodeblock %}

“handleError()”和“handleException()”中先調用了“restore_error_handler()”和“restore_exception_handler()”，用於防止遞歸處理。

開發者可以在配置數組中指定自定義的錯誤和異常處理邏輯：

{% codeblock lang:php %}
<?php
return array(
    // ...
    'exceptionHandler' => function($exception) {
        var_dump($exception);
        return true;
    },
    'errorHandler' => array('MyClass', 'handleError'),
);
?>
{% endcodeblock %}

“errorHandler”和“exceptionHandler”的值必須是一個callable類型，在這個callable結束時，如果不希望後續邏輯（_例如框架自己的錯誤、異常處理邏輯_）繼續處理，就返回true，此時程序將會終止執行並退出。

Demo驗證
--------

在WebApplication::run()中拋出一個異常或使用trigger_error()觸發一個錯誤，可以看到均被攔截和處理。

總結
----

引入錯誤和異常的自動處理可以極大地簡化代碼。我們經常需要在AJAX請求出錯時返回一個JSON字符串，並由客戶端決定如何處理，這時就可以使用自定義的處理邏輯處理錯誤和異常，而Controller裡只實現正常的邏輯即可，無需再有大量難看的try...catch塊。
