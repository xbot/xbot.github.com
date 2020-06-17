---
layout: post
title: "PHP框架实战（三）：实现Controller和Filter"
slug: flamework controller and filter
date: 2013-12-29 20:40:00
comments: true
categories:
- 计算机
tags:
- PHP
- Flamework
- 框架
- 编程
---

目标
----

实现Controller和Filter，程序可以从HTTP请求中解析Controller和Action，并在这两个切面级别实现Filter链。此外，在Controller中，可以使用Action的参数直接访问HTTP请求中的同名参数。

获取代码
--------

项目目录结构做了调整，framework目录存放Flamework框架源码，demo目录存放示例项目。

```bash
git checkout v0.3
```

设计与实现
----------

**Controller的实现**

要求请求URL的格式如下：

>http://www.mydomain.com/index.php?r=post/save

**r**表示Route，斜杠前面的**post**表示Controller的名称，后面的**save**表示Action的名称。对HTTP请求的各种处理逻辑封装在新对象HttpRequest中：

```php
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
```

考虑到HttpRequest可能在多个地方被调用，所以用单例模式实现。

WebApplication中添加如下内容：

```php
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
```

程序应指定一个缺省的Controller，覆盖$defaultController属性即可，默认为“default”。Controller的类名应在名称后面加“Controller”字样的后缀。由于需要包含命名空间的完整类名来动态实例化Controller，故Controller的源码中都应在最后返回其命名空间（_return \_\_NAMESPACE\_\_;_）。

增加Controller类，作为所有Controller的父类：

```php
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
```

Controller::process()是入口方法，它会通过反射机制实现HTTP参数与Action参数的绑定，并指定Action。

**Filter与Filter链的实现**

Filter中实现before()和after()方法，Filter链通过对Filter按顺序递归调用，实现所有Filter::before()方法在切面之前顺序执行，并且所有Filter::after()方法在切面之后逆序执行。

```php
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
```

```php
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
```

对FilterChain和Filter的使用方法在前面的WebApplication::run()和Controller::process()中均有包含。Controller级的Filter在配置文件中设置，内容如下：

```php
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
```

Action级的Filter在Controller里覆盖$filters属性：

```php
<?php
    // ...

    protected $filters = array(
        'org\\x3f\\flamedemo\\filter\\ActionFilterC' => '/^(index|noindex)$/',
    );

    // ...
?>
```

Action级别的Filter通过$filters数组中的正则表达式选择适用的Action。

Demo验证
--------

Demo中实现了两个Controller级别的Filter（_GlobalFilterA和GlobalFilterB_），一个Action级别的Filter（_ActionFilterC_），访问demo项目，页面打印如下结果：

>org\x3f\flamedemo\filter\GlobalFilterA::before
org\x3f\flamedemo\filter\GlobalFilterB::before
org\x3f\flamedemo\filter\ActionFilterC::before
org\x3f\flamedemo\controller\Defaultcontroller::index
org\x3f\flamedemo\filter\ActionFilterC::after
org\x3f\flamedemo\filter\GlobalFilterB::after
org\x3f\flamedemo\filter\GlobalFilterA::after

总结
----

WebApplication作为程序的统一入口，通过对HTTP请求的解析动态创建Controller，并借此实现了Controller级别的Filter链。Controller通过反射机制实现了HTTP参数与Action参数的绑定，以及Action级别的Filter链。而通过对Filter的递归执行，Filter链实现了面向切面编程。
