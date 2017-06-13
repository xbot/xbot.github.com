---
layout: post
title: "Phalcon項目中PHPUnit的初始化"
date: 2014-11-29 09:58
comments: true
categories: 計算機
tags:
- php
- phalcon
- phpunit
- 編程
---

參考[官方文檔](http://docs.phalconphp.com/zh/latest/reference/unit-testing.html)，稍作修改。

在項目下創建目錄unittests，進入目錄執行：

```bash
composer require phpunit/phpunit
```

創建tests目錄并在其中創建文件Bootstrap.php：

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

// 加載項目文件
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

安裝phalcon的phpunit輔助庫：

```bash
composer require phalcon/incubator
```

創建phpunit.xml：

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

創建單元測試基類UnitTestCase.php：

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

創建單元測試類testsTestUnitTest.php：

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

在tests目錄下建立phpunit的軟連接并執行測試：

```bash
ln -sf ../vendor/bin/phpunit run
./run
```

**另**：發現個詭異的問題，如果Model中不覆蓋getSource()方法，單元測試中會自動找用下劃線分隔的表名，即假如Model名爲FooBar，會去找foo_bar的表名，但正常執行程序時找的是foobar。在官方論壇問的[問題](http://forum.phalconphp.com/discussion/4078/whats-the-principle-when-phalcon-gets-the-table-name-of-a-model)還木有解決。phalcon坑挺多的。
