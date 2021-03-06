# 解决Laravel中makeWith()无法取到被mock的实例的问题


被测单元有一行实例化一个类的代码，而且该类的构造方法需要参数。基于面向单元测试编程的原则，通过容器的makeWith()方法实现：

```php
$api = app()->makeWith(Api::class, ['config' => $config]);
```

但是在执行单元测试时发现，虽然测试代码中已经mock了这个类且注入到容器，但在被测单元中取到的还是原类的实例。

<!--more-->

实际上，测试代码中在将mock的实例注入容器时使用的是instance()方法：

```php
$this->app->instance($class, $mockedObj);
```

而容器在取带构造参数的类的实例时，并不取通过instance()方法注册进来的实例：

```php
protected function resolve($abstract, $parameters = [])
{
    $abstract = $this->getAlias($abstract);

    $needsContextualBuild = ! empty($parameters) || ! is_null(
        $this->getContextualConcrete($abstract)
    );

    // If an instance of the type is currently being managed as a singleton we'll
    // just return an existing instance instead of instantiating new instances
    // so the developer can keep using the same objects instance every time.
    if (isset($this->instances[$abstract]) && ! $needsContextualBuild) {
        return $this->instances[$abstract];
    }

    $this->with[] = $parameters;

    $concrete = $this->getConcrete($abstract);

    // We're ready to instantiate an instance of the concrete type registered for
    // the binding. This will instantiate the types, as well as resolve any of
    // its "nested" dependencies recursively until all have gotten resolved.
    if ($this->isBuildable($concrete, $abstract)) {
        $object = $this->build($concrete);
    } else {
        $object = $this->make($concrete);
    }
    
    // ...
    
}
```

当存在构造参数时，容器认为是“上下文相关的构造”（needsContextualBuild），所以尝试通过具体的（concrete）逻辑实时构造。

进一步地，getConcrete()方法的实现如下：

```php
protected function getConcrete($abstract)
{
    if (! is_null($concrete = $this->getContextualConcrete($abstract))) {
        return $concrete;
    }

    // If we don't have a registered resolver or concrete for the type, we'll just
    // assume each type is a concrete name and will attempt to resolve it as is
    // since the container should be able to resolve concretes automatically.
    if (isset($this->bindings[$abstract])) {
        return $this->bindings[$abstract]['concrete'];
    }

    return $abstract;
}
```

它从bindings数组中获取构造逻辑。因此，可以将测试代码中注册被mock的实例的方法改成如下所示：

```php
$this->app->offsetSet($class, $mockedObj);
```

因为offsetSet()方法就是通过bind()方法把被mock的实例注册到容器的：

```php
public function offsetSet($key, $value)
{
    $this->bind($key, $value instanceof Closure ? $value : function () use ($value) {
        return $value;
    });
}
```


