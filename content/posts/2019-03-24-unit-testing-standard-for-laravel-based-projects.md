---
title: 基于Laravel的项目的单元测试规范
categories:
- 计算机
tags:
  - 编程
  - 单元测试
  - php
  - Laravel/Lumen
date: 2019-03-24 22:42:08
---

单元测试是个好东西，解决了我很多问题，不论开发效率还是代码质量，都给我助益良多。最近想在团队内部推广，就拟了个规范草稿：

<!-- more -->

# 什么是单元？

单元是逻辑的最小单位，是函数或方法。

单元测试意味着只测试单元本身，单元内部调用的其它接口、函数和方法等均称为依赖关系。依赖关系自有它们对应的单元测试负责，不在本单元的测试范围内。

# 怎么测试单元？（Stub & Mock）

依赖关系是脆弱的，它会导致单元测试的编写和运行效率低下，甚至易于失败。以下是项目中的一个测试用例，由于依赖了用户服务，且该服务在我家无法访问，导致测试失败：

```
  donie@Donies > ~/Projects/app/service-biz >> master > phpunitat57                                  -- INSERT --
  PHPUnit 5.7.26 by Sebastian Bergmann and contributors.
  
  ......E.                                                            8 / 8 (100%)
  
  Time: 5.59 seconds, Memory: 22.00MB
  
  There was 1 error:
  
  1) Tests\\Quotation\\QuotationTest::testUpdateQuotationProject
  GuzzleHttp\\Exception\\ConnectException: cURL error 28: Connection timed out after 2003 milliseconds (see <http://curl.haxx.se/libcurl/c/libcurl-errors.html>)
  
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/Handler/CurlFactory.php:186
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/Handler/CurlFactory.php:150
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/Handler/CurlFactory.php:103
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/Handler/CurlHandler.php:43
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/Handler/Proxy.php:28
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/Handler/Proxy.php:51
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/PrepareBodyMiddleware.php:72
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/Middleware.php:30
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/RedirectMiddleware.php:68
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/Middleware.php:59
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/HandlerStack.php:67
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/Client.php:275
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/Client.php:123
  /Users/donie/Projects/app/service-biz/vendor/guzzlehttp/guzzle/src/Client.php:129
  /Users/donie/Projects/app/service-biz/app/Services/UserService.php:61
  /Users/donie/Projects/app/service-biz/app/Services/UserService.php:96
  /Users/donie/Projects/app/service-biz/app/Services/QuotationService.php:121
  /Users/donie/Projects/app/service-biz/vendor/illuminate/support/Facades/Facade.php:221
  /Users/donie/Projects/app/service-biz/tests/Quotation/QuotationTest.php:154
  
  ERRORS!
  Tests: 8, Assertions: 8, Errors: 1.
```

要实现真正的单元测试，不可避免地要对单元内部的依赖关系进行伪造，即Mock。

# 规范

## 目录结构

```
tests/
    ↳ Api/
    ↳ Services/
    ↳ Repositories/
    ↳ Helpers/
    ↳ TestCase.php
    ↳ TransactionalTestCase.php
```

## 继承关系

```
App\Tests\TestCase
    ↳Tests\TestCase
        ↳Tests\Api\TagTest
        ...
    ↳Tests\TransactionalTestCase
        ↳Tests\Services\TagTest
        ...
```

## 接口测试

- 所有接口都必须有测试用例，代码覆盖率100%
- 位于`tests/Api`下，命名空间是`Tests/Api`
- 和被测试的Controller对应
- 只测试路由和Action本单元的代码，不测试具体业务逻辑
- 具体业务逻辑封装在Service层，由该层的单元测试负责
- 测试代码中通过Facade实现对Service层的Mock

## 单元测试

### Service 

- 所有Service层的方法都必须有测试用例，代码覆盖率不低于90%
- 位于`tests/Services`目录下，命名空间是`Tests/Services`
- 和被测试的Service对应
- 通过Facade调用Service层并实现对被测试单元依赖关系的Mock

### Repository 

- 复杂的或有必要的方法要有测试用例，其余可以通过Service层的单元测试覆盖到，代码覆盖率不低于90%
- 位于`tests/Repositories`目录下，命名空间是`Tests/Repositories`
- 和被测试的Repository对应

### Helper Functions 

- 复杂的或有必要的函数要有测试用例，其余可以通过其它层的单元测试覆盖到，代码覆盖率100%
- 位于`tests/Helpers`目录下，命名空间是`Tests/Helpers`
- 每个测试用例和被测试的helper函数对应

## 辅助方法

基类中封装如下辅助方法：

```php
/**
 * Mock一个对象并返回伪造的实例
 *
 * 可通过回调匿名函数定制Mock的实例的行为特征。
 * 当$inject参数为false时，Mock的实例不被注入容器，缺省为注入。
 *
 * @param mixed $class 类名字符串或类本身
 * @param callable $handler 回调匿名函数，接收Mock的实例作为参数，用于定制实例自身行为特征
 * @param bool $inject 是否注入容器，缺省为true
 *
 * @return \\Mockery\\MockInterface
 */
protected function mock($class, callable $handler = null, bool $inject = true): MockInterface
{
    $mockedObj = \Mockery::mock($class);

    if (is_callable($handler)) {
        call_user_func($handler, $mockedObj);
    }

    if ($inject) {
        $this->app->instance($class, $mockedObj);
    }

    return $mockedObj;
}
  
/**
 * Mock一个单例模式的类
 *
 * 可通过回调匿名函数定制Mock的实例的行为特征。
 *
 * @param mixed    $class   类名字符串或类本身
 * @param callable $handler 回调匿名函数，接收Mock的实例作为参数，用于定制实例自身行为特征
 */
protected function mockSingleton($class, callable $handler = null): void
{
    $mockedObj = \Mockery::mock($class);

    if (is_callable($handler)) {
        call_user_func($handler, $mockedObj);
    }

    $ref = new \ReflectionProperty($class, 'instance');
    $ref->setAccessible(true);
    $ref->setValue(null, $mockedObj);
}

/**
  * 触发对象的private或protected方法
  *
  * @param  object $object 对象实例
  * @param  string $methodName 方法名
 * @param  array $parameters 参数数组
  *
  * @return mixed 被触发方法的返回值
  */
protected function invokeMethod(&$object, $methodName, array $parameters = [])
{
    $reflection = new \ReflectionClass(get_class($object));
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(true);
    return $method->invokeArgs($object, $parameters);
}
```
