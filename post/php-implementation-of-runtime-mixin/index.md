# 运行时可装卸的Mixin的PHP实现


PHP的Trait可以实现加载时（load time）的混入（mixin）。作为元编程的一部分，运行时（run time）的混入拥有更大的灵活性。下面利用PHP的魔术方法实现运行时的混入。

```php
<?php
/**
 * 支持混入的类
 */
class Component
{
    // ...
    
    // 所有混入的实例
    private $_behaviors = [];
    
    /**
     * 魔术方法
     * @param string $name 方法名
     * @param array $arguments 参数数组
     * @return mixed
     * @throws MethodNotFoundException
     */
    public function __call($name, $arguments) {
        foreach ($this->_behaviors as $behavior) {
            if (method_exists($behavior, $name)) {
                return call_user_func_array([$behavior, $name], $arguments);
            }
        }
        throw new MethodNotFoundException(get_class($this), $name);
    }
    
    /**
     * 魔术方法，从混入对象实例中取属性值
     * @param string $attrName 属性名
     * @return mixed
     * @throws AttrNotFoundException
     */
    public function __get($attrName)
    {
        foreach ($this->_behaviors as $behavior) {
            if (property_exists($behavior, $attrName)) {
                return $behavior->$attrName;
            }
        }
        throw new AttributeNotFoundException(get_class($this), $attrName);
    }
    
    /**
     * 附加混入对象实例
     * @param object $behavior 混入对象实例
     * @param string $name 混入对象实例名称
     * @return void
     */
    public function attachBehavior($behavior, $name='') {
        if (empty($name))
            $this->_behaviors[] = $behavior;
        else
            $this->_behaviors[$name] = $behavior;
    }
    
    /**
     * 卸载混入对象实例
     * @param string $name 混入对象实例名称
     * @return void
     */
    public function detachBehavior($name) {
        unset($this->_behaviors[$name]);
    }
    
    // ...
}
```


```php
<?php
/**
 * 混入类
 */
class Behavior
{
    // ...
    
    /**
     * 将本实例混入指定对象
     * @param object $object 支持混入的实例
     * @param string $name 目标对象存储本混入对象实例的键值
     * @return void
     * @throws BehaviorNotAttachableException
     */
    public function mixin($object, $name='') {
        if (method_exists($object, 'attachBehavior')) {
            return call_user_func_array([$object, 'attachBehavior'], [$this, $name]);
        }
        throw new BehaviorNotAttachableException(get_class($object));
    }
    
    // ...
}
```

使用示例：

```php
<?php
include_once 'component.php';
include_once 'behavior.php';

class TestBehavior extends Behavior
{
    public function test($what)
    {
        echo "say $what";
    }
}

$c = new Component();
$b = new TestBehavior();

$c->attachBehavior($b, 'test');
echo '<pre>'; var_dump($c); echo '</pre>';

$c->detachBehavior('test');
echo '<pre>'; var_dump($c); echo '</pre>';

$b->mixin($c, 'test');
echo '<pre>'; var_dump($c); echo '</pre>';

$c->detachBehavior('test');
$b->mixin($c);
echo '<pre>'; var_dump($c); echo '</pre>';

$c->test('hello world');
```

