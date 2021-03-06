# 面向单元测试编程


同一逻辑可以有多种实现方式，选择对单元测试友好的方式可以提高写测试代码的效率。

<!--more-->

## 返回被创建的对象

通常创建数据的接口只需要返回错误信息，成功时返回空即可，不需要返回被创建的数据。故创建数据的方法可以定义成无返回值。并且，如果我们并不关注数据是不是实际上被插入到数据库中，可以通过mock那部分的逻辑实现，事实上，这其实是更符合单元测试的理念的。

但如果希望测试这一点，就需要到数据库中查询数据是否存在。有时候，如果被创建数据的特征不明显或不唯一，按这种方式实现就很困难。此时，如果被测单元返回被创建的对象实例，就可以省掉这一步，从而简化测试代码、提高执行效率。

## 唾弃乱注入的恶习

遵循MVCSR的分层规范：

1. Controller只实现校验参数、获取参数、调用Service、返回值，不封装具体业务逻辑。
2. Service只注入本对象的Repository，对其它对象的逻辑的调用均通过该对象的Facade实现。
3. Repository只注入本对象的Model，只封装本对象的逻辑。
4. Controller和Service中对任何其它Service的调用均通过Facade实现。

否则，写测试代码时对被测单元的依赖关系的处理会很麻烦。

## 使用Facade

Laravel/Lumen的Facade集成了Mockery，可以极大简化mocking。

对使用依赖注入的方式调用的逻辑做mocking时代码是这样的：

```php
$fakeOrderService = \Mockery::mock(OrderService::class);
$fakeOrderService->shouldReceive('update')->once()->andReturn(true);
$this->app->instance(OrderService::class, $fakeOrderService);
```

而当使用Facade时，只需一行代码：

```php
OrderFacade::shouldReceive('udpate')->once()->andReturn(true);
```

## 使用app(Model::class)而不是new Model()

在Laravel/Lumen中，当代码中需要获取一个对象Model的实例时，通过容器实现会比直接new对单元测试更友好。因为借助容器，可以方便地mock这个Model。

