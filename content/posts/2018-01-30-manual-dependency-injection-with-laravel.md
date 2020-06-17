---
title: 解决Laravel的Facade在被Mock时不自动注入依赖的一种方法
slug: manual dependency injection with laravel
date: 2018-01-30 15:41:16
categories:
- 计算机
tags:
- 编程
- 单元测试
- php
- Laravel/Lumen
---
由于集成了Mockery，Laravel的Facade对单元测试非常友好，只需要一行代码就能实现mocking。

例如，测试目标方法中调用了订单工具类的一个方法，在使用依赖注入的方式时，需要用三行代码实现对这个方法的mocking：

```php
$fakeOrderTool = m::mock(\App\Tool\Order::class);
$fakeOrderTool->shouldReceive('getById')->once()->with($id)->andReturn($fakeOrder);
$this->app->instance(\App\Tool\Order::class, $fakeOrderTool);
```

而用Facade是这样的：

```php
OrderFacade::shouldReceive('getById')->once()->with($id)->andReturn($fakeOrder);
```

当测试目标方法也属于订单工具类时，需要部分mock这个类，所以还要加上一行：

```php
OrderFacade::makePartial();
```

但如果订单工具类的构造方法里有需要注入的依赖关系，这里并不会被执行。所以我在工具类基类里封装了一个方法，用反射机制实现手工注入依赖关系：

```php
    /**
     * 注入依赖，用于单元测试
     *
     * @return  void
     * @author  donieli
     * @since   5
     * @version 5
     */
    public function injectDependencies()
    {
        $reflector = new \ReflectionClass(get_class($this));
        $params    = $reflector->getConstructor()->getParameters();
        $args      = [];
        foreach ($params as $param) {
            $paramClass = $param->getClass();
            if (empty($paramClass)) {
                throw new \Exception('Dependency named '.$param->getName().' cannot be injected.');
            }
            $instance  = app()->make($paramClass->getName());
            $args[] = $instance;
        }
        call_user_func_array([$this, '__construct'], $args);
    }
```

然后就可以这样处理：

```php
OrderFacade::shouldReceive('getById')->with($id)->once()->andReturn($fakeOrder);
OrderFacade::makePartial();
OrderFacade::injectDependencies();
```


