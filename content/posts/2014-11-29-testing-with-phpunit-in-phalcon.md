---
layout: post
title: "Phalcon项目中PHPUnit的初始化"
date: 2014-11-29 09:58:00
comments: true
categories:
- 计算机
tags:
- php
- phalcon
- phpunit
- 编程
- 单元测试
---

参考[官方文档](http://docs.phalconphp.com/zh/latest/reference/unit-testing.html)，稍作修改。

在项目下创建目录unittests，进入目录执行：

```bash
composer require phpunit/phpunit
```

创建tests目录并在其中创建文件Bootstrap.php：

```php
<?php
use Phalcon\DI,
    Phalcon\DI\FactoryDefault;

ini_set('display_errors',1);
error_reporting(E_ALL);

define('ROOT_PATH', __DIR__);
define('PROJECT_DIR', '/home/taoqi/workspace');

set_include_path(
    ROOT_PATH . PATH_SEPARATOR . get_include_path()
);

// required for phalcon/incubator
include __DIR__ . "/../vendor/autoload.php";

// 加载项目文件
$config = require_once PROJECT_DIR.'/web/config/config.php';
require_once PROJECT_DIR.'/web/config/loader.php';
$loader->registerDirs(array(
    ROOT_PATH
), true);

// $di = new FactoryDefault();
DI::reset();

// add any needed services to the DI here
require_once PROJECT_DIR.'/web/config/services.php';

DI::setDefault($di);
```

安装phalcon的phpunit辅助库：

```bash
composer require phalcon/incubator
```

创建phpunit.xml：

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="./Bootstrap.php"
        backupGlobals="false"
        backupStaticAttributes="false"
        verbose="true"
        colors="false"
        convertErrorsToExceptions="true"
        convertNoticesToExceptions="true"
        convertWarningsToExceptions="true"
        processIsolation="false"
        stopOnFailure="false"
        syntaxCheck="true">
    <testsuite name="Phalcon - Testsuite">
        <directory>./</directory>
    </testsuite>
</phpunit>
```

创建单元测试基类UnitTestCase.php：

```php
<?php
use Phalcon\DI,
    \Phalcon\Test\UnitTestCase as PhalconTestCase;

abstract class UnitTestCase extends PhalconTestCase {

    /**
     * @var \Voice\Cache
     */
    protected $_cache;

    /**
     * @var \Phalcon\Config
     */
    protected $_config;

    /**
     * @var bool
     */
    private $_loaded = false;

    public function setUp(Phalcon\DiInterface $di = NULL, Phalcon\Config $config = NULL) {

        // Load any additional services that might be required during testing
        $di = DI::getDefault();

        // get any DI components here. If you have a config, be sure to pass it to the parent
        parent::setUp($di);

        $this->_loaded = true;

    }

    /**
     * Check if the test case is setup properly
     * @throws \PHPUnit_Framework_IncompleteTestError;
     */
    public function __destruct() {
        if(!$this->_loaded) {
            throw new \PHPUnit_Framework_IncompleteTestError('Please run parent::setUp().');

        }

    }

}
```

创建单元测试类testsTestUnitTest.php：

```php
<?php

namespace Test;

/**
 * Class UnitTest
 */
class UnitTests extends \UnitTestCase {

    public function testTestCase() {
        $post = \Post::find(33);
        $this->assertObjectHasAttribute('title', $post, 'where is title ?');
    }

}
```

在tests目录下建立phpunit的软连接并执行测试：

```bash
ln -sf ../vendor/bin/phpunit run
./run
```

**另**：发现个诡异的问题，如果Model中不覆盖getSource()方法，单元测试中会自动找用下划线分隔的表名，即假如Model名为FooBar，会去找foo_bar的表名，但正常执行程序时找的是foobar。在官方论坛问的[问题](http://forum.phalconphp.com/discussion/4078/whats-the-principle-when-phalcon-gets-the-table-name-of-a-model)还木有解决。phalcon坑挺多的。
