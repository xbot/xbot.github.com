---
layout: post
title: "PHP框架實戰（三）：實現Controller和Filter"
date: 2013-12-29 20:40
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

實現Controller和Filter，程序可以從HTTP請求中解析Controller和Action，並在這兩個切面級別實現Filter鏈。此外，在Controller中，可以使用Action的參數直接訪問HTTP請求中的同名參數。

獲取代碼
--------

項目目錄結構做了調整，framework目錄存放Flamework框架源碼，demo目錄存放示例項目。

{% codeblock lang:bash %}
git checkout v0.3
{% endcodeblock %}

設計與實現
----------

**Controller的實現**

要求請求URL的格式如下：

>http://www.mydomain.com/index.php?r=post/save

**r**表示Route，斜杠前面的**post**表示Controller的名稱，後面的**save**表示Action的名稱。對HTTP請求的各種處理邏輯封裝在新對象HttpRequest中：

{% codeblock lang:php %}
<?php
namespace org\x3f\flamework\base;
use org\x3f\flamework\Flame as Flame;

/**
 * HTTP request wrapper
 *
 * @author Donie Leigh <donie.leigh@gmail.com>
 * @link http://0x3f.org
 * @copyright Copyright &copy; 2013-2014 Donie Leigh
 * @license BSD (3-terms)
 * @since 1.0
 */
class HttpRequest
{
    /**
     * @var HttpRequest Singleton instance 
     * @since 1.0
     */
    private static $_instance;
    /**
     * @var string Controller name, null if no one is given 
     * @since 1.0
     */
    private $_controller;
    /**
     * @var string Action name, null if no one is given 
     * @since 1.0
     */
    private $_action;

    /**
     * Singleton constructor
     * @return void
     * @since 1.0
     */
    private function __construct()
    {
        $this->parseRoute();
    }

    /**
     * Disable the cloning
     * @return void
     * @since 1.0
     */
    public function __clone()
    {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }

    /**
     * Get the singleton instance
     * @return HttpRequest
     * @since 1.0
     */
    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self;
        return self::$_instance;
    }
    
    /**
     * Parse request route, set controller and action names
     *
     * @return void
     * @since 1.0
     */
    public function parseRoute()
    {
        if (isset($_GET['r'])) {
            $arr = explode('/', $_GET['r']);
            $this->_controller = $arr[0];
            if (count($arr)>1) $this->_action = $arr[1];
        } else {
            $this->_controller = Flame::app()->getDefaultController();
        }
    }
    
    /**
     * Get controller name
     *
     * @return string null if no controller is present
     * @since 1.0
     */
    public function getController()
    {
        return $this->_controller;
    }
    
    /**
     * Get action name
     *
     * @return string null if no action is found
     * @since 1.0
     */
    public function getAction()
    {
        return $this->_action;
    }
    
    /**
     * Get parameter value
     *
     * @param string $param Parameter name
     * @return mixed Parameter value
     * @since 1.0
     */
    public function getParam($param)
    {
        if (isset($_REQUEST[$param]))
            return $_REQUEST[$param];
        return null;
    }

}

?>
{% endcodeblock %}

考慮到HttpRequest可能在多個地方被調用，所以用單例模式實現。

WebApplication中添加如下內容：

{% codeblock lang:php %}
<?php
class WebApplication {

    // ...

    /**
     * @var string The default controller name
     * @since 1.0
     */
    public $defaultController = 'default';

    // ...

    /**
     * Get the default controller name
     * @return string Controller name
     * @since 1.0
     */
    public function getDefaultController()
    {
        return $this->defaultController;
    }
    
    /**
     * Get controller path
     * @return string The controller path
     * @since 1.0
     */
    public function getControllerPath()
    {
        return $this->getProtectedPath().DIRECTORY_SEPARATOR.'controller';
    }
    
    /**
     * Create an instance of the controller
     * @param string $controllerName
     * @return Controller Controller instance
     * @since 1.0
     */
    public function createController($controllerName)
    {
        $className = ucfirst($controllerName).'Controller';
        $classFile = $this->getControllerPath().DIRECTORY_SEPARATOR."$className.php";
        if (file_exists($classFile)) {
            $ns = include_once($classFile);
            $fullClassName = "$ns\\$className";
            if (class_exists($fullClassName)) {
                return new $fullClassName();
            } else {
                throw new HttpException(404, "Request to $controllerName is unresolvable.");
            }
        } else {
            throw new HttpException(404, "Request to $controllerName is unresolvable.");
        }
    }

}
?>
{% endcodeblock %}

程序應指定一個缺省的Controller，覆蓋$defaultController屬性即可，默認為“default”。Controller的類名應在名稱後面加“Controller”字樣的後綴。由於需要包含命名空間的完整類名來動態實例化Controller，故Controller的源碼中都應在最後返回其命名空間（_return \_\_NAMESPACE\_\_;_）。

增加Controller類，作為所有Controller的父類：

{% codeblock lang:php %}
<?php
namespace org\x3f\flamework\base;
use org\x3f\flamework\exceptions\HttpException;

/**
 * Ancestor class for all controllers
 *
 * @author Donie Leigh <donie.leigh@gmail.com>
 * @link http://0x3f.org
 * @copyright Copyright &copy; 2013-2014 Donie Leigh
 * @license BSD (3-terms)
 * @since 1.0
 */
class Controller 
{
    /**
     * @var string The default action
     * @since 1.0
     */
    protected $defaultAction = 'index';
    /**
     * @var array Filter classnames and rules.
     *            This is an associative array, in which keys are classnames of filters,
     *            and values are regular expressions.
     *            For example:
     *                array(
     *                    'org\\x3f\\flamedemo\filter\\PerformanceFilter' => '/^(save|update|delete)$/',
     *                );
     * @since 1.0
     */
    protected $filters = array();

    /**
     * Process http request
     * @param HttpRequest $request
     * @return void
     * @since 1.0
     */
    public function process(HttpRequest $request)
    {
        $action = $request->getAction() === null ? $this->defaultAction : $request->getAction();
        if (method_exists($this, $action)) {
            // do parameter bindings
            $method = new \ReflectionMethod(get_class($this), $action);
            $params = array();
            foreach ($method->getParameters() as $param){
                if (isset($_REQUEST[$param->getName()])) {
                    $params[] = $_REQUEST[$param->getName()];
                } else if ($param->isDefaultValueAvailable()) {
                    $params[] = $param->getDefaultValue();
                } else {
                    throw new HttpException(400, "Parameter ".$param->getName()." is missing.");
                }
            }
            // create filter chain and run it
            $filters = $this->getActionFilters($action);
            $chain = new FilterChain($this, $action, $params, $filters);
            $chain->run();
        } else {
            $msg = 'Request to '.$request->getController().'/'.$action.' cannot be resolved, action does not exist.';
            throw new HttpException(404, $msg);
        }
    }
    
    /**
     * Get filters for the given action
     * @param string $action
     * @return array Filter names
     * @since 1.0
     */
    public function getActionFilters($action)
    {
        $filters = array();
        foreach ($this->filters as $filterClass=>$regex){
            if (preg_match($regex, $action))
                $filters[] = $filterClass;
        }
        return $filters;
    }
    
}
?>
{% endcodeblock %}

Controller::process()是入口方法，它會通過反射機制實現HTTP參數與Action參數的綁定，並指定Action。

**Filter與Filter鏈的實現**

Filter中實現before()和after()方法，Filter鏈通過對Filter按順序遞歸調用，實現所有Filter::before()方法在切面之前順序執行，並且所有Filter::after()方法在切面之後逆序執行。

{% codeblock lang:php %}
<?php
namespace org\x3f\flamework\base;

/**
 * Ancestor class for all filters
 *
 * @author Donie Leigh <donie.leigh@gmail.com>
 * @link http://0x3f.org
 * @copyright Copyright &copy; 2013-2014 Donie Leigh
 * @license BSD (3-terms)
 * @since 1.0
 */
class Filter
{
    /**
     * Run this filter and the filter chain
     * @param FilterChain $chain
     * @return void
     * @since 1.0
     */
    public function filter(FilterChain $chain)
    {
        if ($this->before($chain)) {
            $chain->run();
            $this->after($chain);
        }
    }
    
    /**
     * The logic to be executed before the aspect point
     * @param FilterChain $chain
     * @return boolean Return true to continue the filter chain, return false to break the chain
     * @since 1.0
     */
    protected function before(FilterChain $chain) {
        return true;
    }

    /**
     * The logic to be executed after the aspect point
     * @param FilterChain $chain
     * @return void
     * @since 1.0
     */
    protected function after(FilterChain $chain) {
    }
}
?>
{% endcodeblock %}

{% codeblock lang:php %}
<?php
namespace org\x3f\flamework\base;

/**
 * Filter chain
 *
 * @author Donie Leigh <donie.leigh@gmail.com>
 * @link http://0x3f.org
 * @copyright Copyright &copy; 2013-2014 Donie Leigh
 * @license BSD (3-terms)
 * @since 1.0
 */
class FilterChain
{
    /**
     * @var object Object to be filtered 
     * @since 1.0
     */
    public $obj;
    /**
     * @var string Method of the object to be filtered 
     * @since 1.0
     */
    public $method;
    /**
     * @var array Parameters to be passed to the method 
     * @since 1.0
     */
    public $params;
    /**
     * @var array Filters 
     * @since 1.0
     */
    public $filters;
    /**
     * @var int The offset of filters array
     * @since 1.0
     */
    private $_offset = 0;

    public function __construct($obj, $method, $params, $filters)
    {
        $this->obj = $obj;
        $this->method = $method;
        $this->params = $params;
        $this->filters = $filters;
    }
    
    /**
     * Run this filter and the filter chain
     * @return void
     * @since 1.0
     */
    public function run()
    {
        $filter = $this->nextFilter();
        if ($filter instanceof Filter) {
            $filter->filter($this);
        } else {
            call_user_func_array(array($this->obj, $this->method), $this->params);
        }
    }
    
    /**
     * Get next filter
     * @return Filter Filter instance
     * @since 1.0
     */
    private function nextFilter()
    {
        if ($this->_offset < count($this->filters)) {
            $filter = $this->filters[$this->_offset];
            $this->_offset++;
            return new $filter;
        }
    }
    
}
?>
{% endcodeblock %}

對FilterChain和Filter的使用方法在前面的WebApplication::run()和Controller::process()中均有包含。Controller級的Filter在配置文件中設置，內容如下：

{% codeblock lang:php %}
<?php
return array(
    
    // ...
    
    // app namespace and its path
    'namespaces' => array('org\\x3f\\flamedemo' => '/srv/http/flamework/demo/protected'),
    // filter classes
    'filters' => array(
        'org\\x3f\\flamedemo\\filter\\GlobalFilterA',
        'org\\x3f\\flamedemo\\filter\\GlobalFilterB',
    ),
);
?>
{% endcodeblock %}

Action級的Filter在Controller裡覆蓋$filters屬性：

{% codeblock lang:php %}
<?php
    // ...

    protected $filters = array(
        'org\\x3f\\flamedemo\\filter\\ActionFilterC' => '/^(index|noindex)$/',
    );

    // ...
?>
{% endcodeblock %}

Action級別的Filter通過$filters數組中的正則表達式選擇適用的Action。

Demo驗證
--------

Demo中實現了兩個Controller級別的Filter（_GlobalFilterA和GlobalFilterB_），一個Action級別的Filter（_ActionFilterC_），訪問demo項目，頁面打印如下結果：

>org\x3f\flamedemo\filter\GlobalFilterA::before
org\x3f\flamedemo\filter\GlobalFilterB::before
org\x3f\flamedemo\filter\ActionFilterC::before
org\x3f\flamedemo\controller\Defaultcontroller::index
org\x3f\flamedemo\filter\ActionFilterC::after
org\x3f\flamedemo\filter\GlobalFilterB::after
org\x3f\flamedemo\filter\GlobalFilterA::after

總結
----

WebApplication作為程序的統一入口，通過對HTTP請求的解析動態創建Controller，並借此實現了Controller級別的Filter鏈。Controller通過反射機制實現了HTTP參數與Action參數的綁定，以及Action級別的Filter鏈。而通過對Filter的遞歸執行，Filter鏈實現了面向切面編程。
