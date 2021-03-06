# PHP单元测试的技巧


以下是我平时写单元测试时总结的一些最佳实践，有些是和具体的框架强相关的。

<!--more-->

## 利用Model Factory生成测试数据

Laravel/Lumen的Model Factory是个很好的工具。

事先定义好主要对象的Model Factory，可以在写单元测试的代码时方便地生成临时数据：

```php
// 在数据库中创建一条发票的数据，并返回其Model的实例
$invoice = factory(\App\Models\Invoice::class)->create();
```

此外，借助[Faker](https://github.com/fzaninotto/Faker)，可以为Model Factory定义仿真数据：

```php
$factory->define(\App\Models\Invoice::class, function () {
    $faker = Faker\Factory::create('zh_CN');
    $enFaker = Faker\Factory::create('en_US');

    return [
        'user_id'                      => 1,
        'amount'                       => 100,
        'title'                        => $faker->company,
        'taxpayer_id'                  => $faker->randomNumber(),
        'bank'                         => $faker->bank,
        'account'                      => $enFaker->bankAccountNumber,
        'company_registration_address' => $faker->address,
        'company_registration_phone'   => $faker->phoneNumber,
        'consignee_name'               => $faker->name,
        'consignee_province'           => $faker->state,
        'consignee_city'               => $faker->city,
        'consignee_district'           => $faker->citySuffix,
        'consignee_address'            => $faker->address,
        'consignee_phone'              => $faker->phoneNumber,
        'zip_code'                     => $faker->postcode,
        'remark'                       => $faker->text,
        'status'                       => 1,
    ];
});
```

## 只伪造被测单元所需的数据

单元测试只关注被测单元本身，并不关注整体功能，所以无须保证测试数据的完整性。换言之，如果被测单元只用到了订单主表数据，就没必要创建一个包含子单、SPU/SKU、支付记录等在内的完整订单。

## 利用dataProvider简化测试代码

当多个测试用例逻辑相似时，可以把它们抽象成一个测试方法，并用dataProvider提供每个用例的数据和断言。从而使测试代码变得更为简洁、易懂和便于维护。

## 单独测试被测单元的校验逻辑

通常我们会用标准的IO模型实现一个方法，同时对包含校验不合法在内的异常情况抛出相应异常。

PHPUnit提供了expectException()系列方法用于断言异常的抛出。但由于代码抛出了异常、程序被中断，故无法在一次测试中对多个异常做断言。

因此，可以把对一个方法校验逻辑的测试单独抽象出一个测试方法，并用dataProvider提供测试数据和断言。

## 利用工具查看代码覆盖率

PHPUnit提供参数`--coverage-html`生成覆盖率分析结果的HTML页面，但在IDE或编辑器里相对实时地查看结果会更方便。

主流IDE或编辑器一般都集成了这个功能或提供了相关插件，如[Coverage Gutters](https://marketplace.visualstudio.com/items?itemName=ryanluker.vscode-coverage-gutters)。

