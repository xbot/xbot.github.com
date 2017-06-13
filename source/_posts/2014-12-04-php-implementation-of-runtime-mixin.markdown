---
layout: post
title: "運行時可裝卸的Mixin的PHP實現"
date: 2014-12-04 11:52
comments: true
categories: 計算機
tags:
- 編程
- php
---

PHP的Trait可以實現加載時（load time）的混入（mixin）。作爲元編程的一部分，運行時（run time）的混入擁有更大的靈活性。下面利用PHP的魔術方法實現運行時的混入。

```php
<?php
/**
 * 支持混入的類
 */
class Component
{
    // ...
    
    // 所有混入的實例
    private $_behaviors = [];
    
    /**
     * 魔術方法
     * @param string $name 方法名
     * @param array $arguments 參數數組
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
     * 魔術方法，從混入對象實例中取屬性值
     * @param string $attrName 屬性名
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
     * 附加混入對象實例
     * @param object $behavior 混入對象實例
     * @param string $name 混入對象實例名稱
     * @return void
     */
    public function attachBehavior($behavior, $name='') {
        if (empty($name))
            $this->_behaviors[] = $behavior;
        else
            $this->_behaviors[$name] = $behavior;
    }
    
    /**
     * 卸載混入對象實例
     * @param string $name 混入對象實例名稱
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
 * 混入類
 */
class Behavior
{
    // ...
    
    /**
     * 将本實例混入指定對象
     * @param object $object 支持混入的實例
     * @param string $name 目标對象存儲本混入對象實例的鍵值
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
