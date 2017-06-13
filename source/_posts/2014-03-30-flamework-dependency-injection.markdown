---
layout: post
title: "PHP框架實戰（六）：依賴注入"
date: 2014-03-30 10:00
comments: true
categories: 計算機
tags:
- PHP
- Flamework
- 框架
- 編程
---

簡述
----

依賴注入是個很好的解耦方法，也可以優雅的實現懶加載。

以數據庫連接為例，當一個組件用到數據庫時，最簡單粗暴的辦法是在使用前創建連接：

{% codeblock lang:php %}
<?php
class Component {
    public function doSth() {
        // ...

        $db = new DatabaseConnection($schema, $host, $port, $login, $password);

        // ...
    }
}
?>
{% endcodeblock %}

這樣做的缺點是代碼一旦執行，Component行為將不可更改，有時我們需要在程序執行的過程中決定其行為。很直接地，可以想到給Component添加一個Setter方法：

{% codeblock lang:php %}
<?php
class Component {
    private $__db;

    public function doSth() {
        // ...

        if ($this->__db !== null) {
            // do something ...
        }

        // ...
    }

    public function setConnection($db) {
        $this->__db = $db;
    }
}
?>
{% endcodeblock %}

這樣就把Component和數據庫連接解耦了。這時又有一個問題，怎樣管理數據庫連接？最簡單粗暴的辦法是在每次使用前創建，如果在程序中多處需要改變數據庫連接，這就把代碼寫死了。

一種解決問題的方法是使用一個全局變量保存連接，對於簡單場景，這沒問題，但是這種做法會污染全局命名空間，尤其是在依賴較多的情況下，這種做法就不可取了。另一種方法就是用一個註冊表持有所有依賴，這就是依賴注入要做的事。

我曾經處理過一個問題，生產環境中即使在無請求的情況下也會在短時間內生成大量會話文件，直接原因是使用了keepalived檢查系統可用性。由於程序在一開始就打開了會話，並且keepalived不能保持會話，導致每次訪問都會生成一個新的會話文件。這就是沒有使用懶加載導致的錯誤。

懶加載可以讓每個組件只有在需要的情況下才被初始化，一方面簡化了代碼、提高了可讀性，另一方面也能提高程序效率、降低資源消耗。如果組件很多，而每次請求實際用到的很少，初始化所有組件產生的資源消耗將會很可觀。

實現
----

用單例模式實現，以鍵值對的形式註冊依賴。同時支持以變量和callable的形式注入，前者用以註冊簡單類型或已實例化的依賴，後者可以用匿名函數的方式更靈活地管理依賴。同時，注入依賴時可以指定該依賴是否為單例模式，如果是，callable類型的依賴將會在第一次被調用後保持下來。此外，使用__call()魔術方法實現直接以getter方法的方式獲取依賴。

{% codeblock lang:php %}
<?php
namespace org\x3f\flamework\base;
use org\x3f\flamework\exceptions\FlameException;

/**
 * Dependency Injection Class
 *
 * @author Donie Leigh <donie.leigh@gmail.com>
 * @link http://0x3f.org
 * @copyright Copyright &copy; 2013-2014 Donie Leigh
 * @license BSD (3-terms)
 * @since 1.0
 */
class DI
{
    /**
     * @var object Singleton instance
     * @since 1.0
     */
    public static $_instance;
    /**
     * @var array Services
     * @since 1.0
     */
    private $_services = array();

    /**
     * Singleton constructor
     * @return void
     * @author Donie Leigh <donie.leigh@gmail.com>
     * @since 1.0
     */
    private function __construct()
    {
    }

    /**
     * Return the singleton instance
     * @return object Singleton instance
     * @since 1.0
     **/
    public static function getInstance()
    {
        if (! self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Add a service to the register
     * @param string $key Service name
     * @param mixed $service Callable to create a service instance or exactly an instance
     * @param bool $isSingleton Set true to treat this service as singleton
     * @return void
     * @since 1.0
     **/
    public function set($key, $service, $isSingleton=false)
    {
        $this->_services[strtolower($key)] = array(
            'service' => $service,
            'isSingleton' => $isSingleton,
            'instance' => null
        );
    }
    
    /**
     * Get a service instance
     * @return mixed Service instance
     * @since 1.0
     */
    public function get($key)
    {
        $key = strtolower($key);
        if (isset($this->_services[$key])) {
            $info = &$this->_services[$key];
            if ($info['instance'] !== null)
                return $info['instance'];
            if (is_callable($info['service'])) {
                $instance = call_user_func($info['service']);
                if ($info['isSingleton'] === true)
                    $info['instance'] = $instance;
                return $instance;
            } else {
                return $info['service'];
            }
        }
        return null;
    }
    
    /**
     * Get service with magic method
     * @param string $method get{ServiceName}
     * @param array $parameters Parameters, currently useless
     * @return mixed Service instance
     * @since 1.0
     */
    public function __call($method, $parameters)
    {
        if (strpos(strtolower($method), 'get') === 0) {
            $serviceName = substr($method, 3);
            return $this->get($serviceName);
        }
        throw new FlameException('Call to undefined method: '.$method);
    }
    
} // END class DI
?>
{% endcodeblock %}

使用
----

{% codeblock lang:php %}
<?php
$di = DI::getInstance();

// 簡單類型
$di->set('foo', 'bar');

// 每次調用生成不同的依賴
$di->set('newPassword', function(){
    $pwd = ”;
    $pwdLen = 10;
    for ($i = 0; $i < $pwdLen; $i++) {
        $pwd .= chr(mt_rand(33, 126));
    }
    return $pwd;
});

// 注入對象實例
$di->set('simpleSession', new SimpleSession());

// 依賴在第一次被獲取時動態創建，然後保持為單例
$di->set('complicatedSession', function(){
    $session = new ComplicatedSession();
    $session->setFirstVisitTime(time());
    return $session;
}, true);

// 獲取依賴
echo $di->get('foo');

// 以getter的形式獲取依賴
echo $di->getFoo();
?>
{% endcodeblock %}

總結
----

之前的版本中，org\x3f\flamework\base\WebApplication中保存了數據庫連接和日誌級別，現在就可以把它們從這個類中解耦了。使用依賴注入，可以使框架核心保持盡可能的精簡，最大程度地保證任何一個組件都是可拆卸和更換的，也是防止過度設計的一個很好的機制。
