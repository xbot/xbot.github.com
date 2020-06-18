# 怎样测试PHP的Private或Protected方法


利用闭包绑定：

```php
$ctrlr = new UserController;

$tester = function () use ($uid) {
    $this->getUser($uid);
};
$runner = $tester->bindTo($ctrlr, $ctrlr);
$runner();
```

