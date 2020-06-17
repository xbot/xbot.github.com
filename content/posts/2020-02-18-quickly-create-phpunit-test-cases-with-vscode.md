---
title: 在VS Code中快速创建PHPUnit测试类的方法
date: 2020-02-18 22:49:45
categories:
- 计算机
tags:
- 最佳实践
- 编程
- 单元测试
- php
- phpunit
- vscode
---

通过单元测试解决问题或者调试代码，可以避开错综复杂的依赖关系、直击问题的核心，从而极大地节约时间、提高效率。

但是，当创建测试类成为一个经常发生的操作时，每次都手动创建类、引入基类、添加测试方法就显得很烦琐。我需要这样一个解决方案，让我可以立即开始着手写测试代码。

<!-- more -->

# 方案一：文件模板

通过VS Code的插件[Template](https://marketplace.visualstudio.com/items?itemName=yongwoo.template)，可以实现把事先准备好的一个测试类文件模板复制到工程目录下。

安装完后，第一次执行`Template: Create New`命令会在当前工程根目录下创建文件“template.config.js”和目录“.templates”。

在“.templates”中创建测试类模板文件，例如：

```php
<?php

namespace Tests;

use Tests\TransactionalTestCase;

class AnyTest extends TransactionalTestCase
{
    public function testAnything()
    {
    }
}
```

下次再执行Template的命令或者侧边栏文件管理器中的右键菜单项时，就可以选择该模板了。

Template有个很大的短板，目前的版本还不支持全局模板，每个工程下都维护一套模板还是很烦琐的。

# 方案二：代码片断

代码片断（以下统称Snippet）可能是VS Code做得最烂的一个功能，我曾无数次泪流满面地回想起有Vim和UltiSnips相伴的美好时光。

解决方案是这样的：

先通过插件[File Utils](https://marketplace.visualstudio.com/items?itemName=sleistner.vscode-fileutils)的`File: New File Relative to Project Root`命令在工程目录下创建一个空白文件（如AnyTest.php）。然后再通过Snippet快速插入测试类的脚手架代码。

这里有一个问题，VS Code的PHP Snippet只有在`<?php`标签之内才能生效，所以如果创建一个针对PHP语言的Snippet，在上面创建的这个空白文件里是无法触发的。

VS Code的Snippet总体上分为工程和通用两类，所谓工程类（Project Snippet Scope），就是只对当前工程生效，存储在工程根目录下的“.vscode”目录中。通用类（Language Snippet Scope）是我们平时最常用的，对所有工程都会生效。它又分为语言和全局两种。语言类（Language Snippet File）是针对具体的语言定义的Snippet集合，文件名为语言名称，后缀是“.json”，如“php.json”。全局类（Global Snippet File）通常是不受限于语言的，当然也可以指定单个Snippet对哪些语言启用，文件名随意，后缀是“.code-snippets”。

因此，测试类脚手架代码的Snippet不能定义在“php.json”中，而应放在“global.code-snippets”里：

```JSON
"PHPUnit：新测试类": {
    "prefix": "newcase",
    "body": [
        "<?php",
        "",
        "namespace Tests${1:\\\\${2:SubNameSpace}};",
        "",
        "use Tests\\\\${3|TransactionalTestCase,TestCase|};",
        "",
        "class ${TM_FILENAME_BASE} extends $3",
        "{",
        "    $0",
        "}"
    ],
    "description": "PHPUnit：新测试类",
    "scope": "html,php",
},
```

这里通过选项“scope”设定只对“html”和“php”两种语言生效，注意如果只设定“php”也是只能在`<?php`标签内才能生效的。