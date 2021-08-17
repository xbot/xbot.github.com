# PHPUnit 9.x 性能分析的方法


利用 PHPUnit 9.x 的 extension 特性，可以记录每个测试用例的耗时并存储到 CSV 文件中。

Extension 代码：

```php
<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Runner\AfterTestHook;

class LongRunningTestsLogger implements AfterTestHook
{
    public function __construct(private string $log_file) {
    }

    public function executeAfterTest(string $test, float $time): void
    {
        $fp = fopen($this->log_file . '.csv', 'a');
        fputcsv($fp, [$test, $time]);
        fclose($fp);
    }
}
```

phpunit.xml 的配置：

```xml
    <extensions>
        <extension class="Tests\LongRunningTestsLogger">
            <arguments>
                <string>profiling</string>
            </arguments>
        </extension>
    </extensions>
```


