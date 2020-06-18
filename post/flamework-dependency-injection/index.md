# PHP框架实战（六）：依赖注入


简述
----

依赖注入是个很好的解耦方法，也可以优雅的实现懒加载。

以数据库连接为例，当一个组件用到数据库时，最简单粗暴的办法是在使用前创建连接：

```php
<?php
class Component {
    public function doSth() {
        // ...

        $db = new DatabaseConnection($schema, $host, $port, $login, $password);

        // ...
    }
}
?>
```

这样做的缺点是代码一旦执行，Component行为将不可更改，有时我们需要在程序执行的过程中决定其行为。很直接地，可以想到给Component添加一个Setter方法：

```php
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
```

这样就把Component和数据库连接解耦了。这时又有一个问题，怎样管理数据库连接？最简单粗暴的办法是在每次使用前创建，如果在程序中多处需要改变数据库连接，这就把代码写死了。

一种解决问题的方法是使用一个全局变量保存连接，对于简单场景，这没问题，但是这种做法会污染全局命名空间，尤其是在依赖较多的情况下，这种做法就不可取了。另一种方法就是用一个注册表持有所有依赖，这就是依赖注入要做的事。

我曾经处理过一个问题，生产环境中即使在无请求的情况下也会在短时间内生成大量会话文件，直接原因是使用了keepalived检查系统可用性。由于程序在一开始就打开了会话，并且keepalived不能保持会话，导致每次访问都会生成一个新的会话文件。这就是没有使用懒加载导致的错误。

懒加载可以让每个组件只有在需要的情况下才被初始化，一方面简化了代码、提高了可读性，另一方面也能提高程序效率、降低资源消耗。如果组件很多，而每次请求实际用到的很少，初始化所有组件产生的资源消耗将会很可观。

实现
----

用单例模式实现，以键值对的形式注册依赖。同时支持以变量和callable的形式注入，前者用以注册简单类型或已实例化的依赖，后者可以用匿名函数的方式更灵活地管理依赖。同时，注入依赖时可以指定该依赖是否为单例模式，如果是，callable类型的依赖将会在第一次被调用后保持下来。此外，使用__call()魔术方法实现直接以getter方法的方式获取依赖。

```php
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
```

使用
----

```php
<?php
$di = DI::getInstance();

// 简单类型
$di->set('foo', 'bar');

// 每次调用生成不同的依赖
$di->set('newPassword', function(){
    $pwd = ”;
    $pwdLen = 10;
    for ($i = 0; $i < $pwdLen; $i++) {
        $pwd .= chr(mt_rand(33, 126));
    }
    return $pwd;
});

// 注入对象实例
$di->set('simpleSession', new SimpleSession());

// 依赖在第一次被获取时动态创建，然后保持为单例
$di->set('complicatedSession', function(){
    $session = new ComplicatedSession();
    $session->setFirstVisitTime(time());
    return $session;
}, true);

// 获取依赖
echo $di->get('foo');

// 以getter的形式获取依赖
echo $di->getFoo();
?>
```

总结
----

之前的版本中，org\x3f\flamework\base\WebApplication中保存了数据库连接和日志级别，现在就可以把它们从这个类中解耦了。使用依赖注入，可以使框架核心保持尽可能的精简，最大程度地保证任何一个组件都是可拆卸和更换的，也是防止过度设计的一个很好的机制。

