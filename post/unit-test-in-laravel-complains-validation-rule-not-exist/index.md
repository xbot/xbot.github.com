# Lumen单元测试提示自定义校验规则不存在的问题


执行单元测试时遇到报错：

```
BadMethodCallException: Method [validateIdList] does not exist.
```

<!--more-->

`id_list`是一个自定义的校验规则，通过`Illuminate\Support\Facades\Validator::extend()`在`AppServiceProvider::boot()`中注册。由于存在一个和该规则配套的、通过`Illuminate\Support\Facades\Request::macro()`注册的扩展方法`getIDs()`，故通过`Illuminate\Support\Facades\Request::hasMacro()`判断当该方法不存在时才注册，并把这个校验规则的注册逻辑也放到了这个判断里。

同一接口，在Postman中测试并无异常。通过日志看到，`AppServiceProvider::boot()`被执行了两次，这也很好理解，执行单元测试本身时会初始化一次，当单元测试调用被测试接口时，是第二次调用。也就是在这次调用中，抛出了这个异常。

那么，为什么`getIDs()`方法没有问题，而`id_list`校验规则却不存在呢？

通过源码可以看到，`macro()`方法来自于`Illuminate\Support\Traits\Macroable`，该Trait内部使用静态变量`$macros`存储被注册的逻辑。而注册校验规则的`extend()`方法来自于`Illuminate\Validation\Factory`这个类。此类在`Illuminate\Validation\ValidationServiceProvider`中被注册为单例。

因此，当第二次被执行时，静态存储的`getIDs()`方法仍然存在，而`id_list`由于程序实例被重置，已经不存在了。

综上，解决的办法是把注册自定义校验规则的逻辑拿到`hasMacro()`外面。

