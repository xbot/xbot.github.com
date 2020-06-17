---
layout: post
title: "PHP框架实战（一）：框架入口与类的自动加载"
date: 2013-12-28 01:56:00
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

实现框架入口和类的自动加载。

获取代码
--------

```bash
git checkout v0.1
```

设计与实现
----------

通过两个类实现本次目标。

静态类Flame作为整个框架的入口，实现一系列框架级公用静态方法（_例如创建应用实例和自动加载类_）。

WebApplication是应用的抽象层，实现应用的入口和其它应用级（**即运行时**）公用方法（_例如读取配置文件_）。

**程序入口**

```php
<?php
// ...
Flame::createApplication($config)->run();
?>
```

此处传入的$config是应用配置文件的路径，该配置文件内容格式如下：

```php
<?php
return array(
    'opt1' => 'val1',
    // ...
);
?>
```

在文件中直接return一个关联数组的好处是，加载该文件时，include()函数的返回值就是该数组，代码更简洁，效率比解析其它格式配置文件高。

**类的自动加载**

```php
<?php
// ...
class Flame {

    // ...

    public static function autoload($className)
    {
        if (!isset(self::$_namespaces[__NAMESPACE__]))
            self::$_namespaces[__NAMESPACE__] = dirname(__FILE__);
        foreach (self::$_namespaces as $ns=>$path){
            if (strpos($className, $ns) === 0) {
                $classFile = $path.str_replace('\\', DIRECTORY_SEPARATOR, substr($className, strlen($ns))).'.php';
                include($classFile);
                return class_exists($className);
            }
        }
        return false;
    }

    // ...

}

spl_autoload_register(__NAMESPACE__.'\\Flame::autoload');
?>
```

通过函数spl_autoload_register()注册Flame::autoload()方法。当用到一个类时，PHP会先检查该类是否已加载，如果没有，就把包括命名空间在内的完整的类名传递给autoload()，最终完成类的加载。

> 当不填参数调用spl_autoload_register()时，PHP使用默认的spl_autoload()函数加载类文件，如果命名空间与目录结构完全对应，也可以实现自动加载，而且效率比指定的自定义加载方法更高。但是spl_autoload()有个历久弥新的Bug，它总是试图加载文件名是小写字母的文件，例如，假如类名是MyClass，spl_autoload()就去找文件名为myclass.php的文件，然而一般情况是文件名与类名相同，即MyClass.php，这在大小写敏感的系统中就找不到了。这个函数的开发者得有多恨Linux啊。

验证Demo
--------

整个Demo程序的index.php只需包含以下代码即可：

```php
<?php
require_once '../flamework/Flame.php';
use org\x3f\flamework\Flame as Flame;

$_config = 'protected/config.php';

Flame::createApplication($_config)->run();
?>
```

总结
----

这样一个最简单的框架就实现了。

通过这两个类，将框架级静态逻辑与运行时的动态逻辑分开。对应用高度的抽象和OOP封装使框架和应用的代码更易读。单一的应用入口也方便实现一些横向逻辑（_例如过滤器_）。

此外，利用include()会将它所加载的脚本中return的值作为自身返回值的特性实现配置文件的读取，简化了代码，提高了效率。类的自动加载使开发者不必再关心类文件的引入和移除，避免因多余的include或require而导致效率下降和资源浪费。
