# PHPUnit代码覆盖率的统计方法


关于PHPUnit代码覆盖率的很多概念，官方文档中讲的十分清楚，本文仅就部分语焉不详的细节做些补充。

虽然phpunit.xml不是必需，但每次都输入完整的命令很不方便，所以一般都要生成这个配置文件。用`phpunit --generate-configuration`生成的phpunit.xml默认包含`forceCoversAnnotation="true"`，它表示必须在测试方法头部的注释里显式使用`@covers`标签声明统计范围，例如：

```php
/**
 * @covers Ox3f\LaravelUtils\Log\Log::<public>
 * @covers Ox3f\LaravelUtils\Log\Log::parseCallStack
 * @covers Ox3f\LaravelUtils\Log\Log::__construct
 * @covers Ox3f\LaravelUtils\Log\Log::__callStatic
 */
public function testAll()
{
    // ...
}
```

否则在执行统计代码覆盖率的命令时，会报risk：

> There was 1 risky test:
>
> 1) Ox3f\LaravelUtils\Log\LogTest::testAll
> This test does not have a @covers annotation but is expected to have one

如果希望单元测试覆盖全部代码、且不用一一显式声明，可以将此选项的值改成
`false`。

之后即可使用明令`phpunit —coverage-html ./report`统计代码覆盖率，统计结果保存在report目录中。

## 参考

* [官方文档：第11章-代码覆盖率分析](https://phpunit.de/manual/current/zh_cn/code-coverage-analysis.html)


