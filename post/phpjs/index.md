# 强大的工具函数库：php.js

<p>PHP最大的特点在于它拥有一个丰富、灵活、强大的函数库，因此得以机动灵活成为软件开发语言中的轻骑兵。</p>

<p><a href="http://phpjs.org/">php.js</a>是一个用Javascript实现的函数库，它试图用Javascript最大程度地重写PHP的函数库。相对于JQuery、ExtJS这些Javascript库，php.js不并致力于为AJAX、DOM和界面开发提供一揽子解决方案，它只是将PHP函数的强大和简便带到前端开发中来，由于JQuery这样的库并不旨在提供完全的Javascript开发标准和手段，实际上，它弥补了这些高端的工具库与低端的Javascript开发之间的一个空白。</p>

<p>对于熟悉PHP的人尤其是PHP程序员来说，使用php.js是几乎不需要切换思维方式的。</p>

<p>这是使用php实现的日期格式校验函数：</p>

<p>
```php
function IsValidDate($strDate, $strFormat='Y-m-d') {
    $strDate = trim($strDate);
    $unixTime = strtotime($strDate);
    $strNewDate = date($strFormat, $unixTime);
    return $strDate == $strNewDate;
}
```
</p>

<p>这是使用php.js实现的日期格式校验函数：</p>

<p>
```javascript
function IsValidDate(strDate, strFormat) {
    strDate = trim(strDate);
    var unixTime = strtotime(strDate);
    var strNewDate = date(strFormat, unixTime);
    return strDate == strNewDate;
}
```
</p>

