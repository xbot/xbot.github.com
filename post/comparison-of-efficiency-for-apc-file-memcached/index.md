# APC、文件和Memcached效率对比

<p>对一个保存了100个对象实例的数组进行300次读写，该数组序列化后大小为232KB。</p>

<h2>测试环境</h2>

<table>
    <tr>
        <th>网络服务器</th>
        <th>PHP版本</th>
        <th>操作系统</th>
        <th>硬件配置</th>
    </tr>
    <tr>
        <td>NGINX v1.0.4</td>
        <td>PHP-FPM v5.3.6<br>APC启用</td>
        <td>Archlinux 32bit</td>
        <td>Intel(R) Core(TM)2 Duo CPU T9400 @ 2.53GHz<br>Mem: 3G DDR3<br>Disk: 5400r/s</td>
    </tr>
</table>

<h2>测试脚本</h2>

<p>测试数据：</p>

```php
<?php
/*
 * @file data.php
 */

class XTest
{
    var $x0;
    var $x1;
    var $x2;
    var $x3;
    var $x4;
    var $x5;
    var $x6;
    var $x7;
    var $x8;
    var $x9;
    var $x10;
    var $x11;
    var $x12;
    var $x13;
    var $x14;
    var $x15;
    var $x16;
    var $x17;
    var $x18;
    var $x19;
    var $x20;
    var $x21;
    var $x22;
    var $x23;
    var $x24;
    var $x25;
    var $x26;
    var $x27;
    var $x28;
    var $x29;
    var $x30;
    var $x31;
    var $x32;
    var $x33;
    var $x34;
    var $x35;
    var $x36;
    var $x37;
    var $x38;
    var $x39;
    var $x40;
    var $x41;
    var $x42;
    var $x43;
    var $x44;
    var $x45;
    var $x46;
    var $x47;
    var $x48;
    var $x49;
    var $x50;
    var $x51;
    var $x52;
    var $x53;
    var $x54;
    var $x55;
    var $x56;
    var $x57;
    var $x58;
    var $x59;
    var $x60;
    var $x61;
    var $x62;
    var $x63;
    var $x64;
    var $x65;
    var $x66;
    var $x67;
    var $x68;
    var $x69;
    var $x70;
    var $x71;
    var $x72;
    var $x73;
    var $x74;
    var $x75;
    var $x76;
    var $x77;
    var $x78;
    var $x79;
    var $x80;
    var $x81;
    var $x82;
    var $x83;
    var $x84;
    var $x85;
    var $x86;
    var $x87;
    var $x88;
    var $x89;
    var $x90;
    var $x91;
    var $x92;
    var $x93;
    var $x94;
    var $x95;
    var $x96;
    var $x97;
    var $x98;
    var $x99;

    function __construct()
    {
        $this->x0 = 1234567890;
        $this->x1 = 1234567890;
        $this->x2 = 1234567890;
        $this->x3 = 1234567890;
        $this->x4 = 1234567890;
        $this->x5 = 1234567890;
        $this->x6 = 1234567890;
        $this->x7 = 1234567890;
        $this->x8 = 1234567890;
        $this->x9 = 1234567890;
        $this->x10 = 1234567890;
        $this->x11 = 1234567890;
        $this->x12 = 1234567890;
        $this->x13 = 1234567890;
        $this->x14 = 1234567890;
        $this->x15 = 1234567890;
        $this->x16 = 1234567890;
        $this->x17 = 1234567890;
        $this->x18 = 1234567890;
        $this->x19 = 1234567890;
        $this->x20 = 1234567890;
        $this->x21 = 1234567890;
        $this->x22 = 1234567890;
        $this->x23 = 1234567890;
        $this->x24 = 1234567890;
        $this->x25 = 1234567890;
        $this->x26 = 1234567890;
        $this->x27 = 1234567890;
        $this->x28 = 1234567890;
        $this->x29 = 1234567890;
        $this->x30 = 1234567890;
        $this->x31 = 1234567890;
        $this->x32 = 1234567890;
        $this->x33 = 1234567890;
        $this->x34 = 1234567890;
        $this->x35 = 1234567890;
        $this->x36 = 1234567890;
        $this->x37 = 1234567890;
        $this->x38 = 1234567890;
        $this->x39 = 1234567890;
        $this->x40 = 1234567890;
        $this->x41 = 1234567890;
        $this->x42 = 1234567890;
        $this->x43 = 1234567890;
        $this->x44 = 1234567890;
        $this->x45 = 1234567890;
        $this->x46 = 1234567890;
        $this->x47 = 1234567890;
        $this->x48 = 1234567890;
        $this->x49 = 1234567890;
        $this->x50 = 1234567890;
        $this->x51 = 1234567890;
        $this->x52 = 1234567890;
        $this->x53 = 1234567890;
        $this->x54 = 1234567890;
        $this->x55 = 1234567890;
        $this->x56 = 1234567890;
        $this->x57 = 1234567890;
        $this->x58 = 1234567890;
        $this->x59 = 1234567890;
        $this->x60 = 1234567890;
        $this->x61 = 1234567890;
        $this->x62 = 1234567890;
        $this->x63 = 1234567890;
        $this->x64 = 1234567890;
        $this->x65 = 1234567890;
        $this->x66 = 1234567890;
        $this->x67 = 1234567890;
        $this->x68 = 1234567890;
        $this->x69 = 1234567890;
        $this->x70 = 1234567890;
        $this->x71 = 1234567890;
        $this->x72 = 1234567890;
        $this->x73 = 1234567890;
        $this->x74 = 1234567890;
        $this->x75 = 1234567890;
        $this->x76 = 1234567890;
        $this->x77 = 1234567890;
        $this->x78 = 1234567890;
        $this->x79 = 1234567890;
        $this->x80 = 1234567890;
        $this->x81 = 1234567890;
        $this->x82 = 1234567890;
        $this->x83 = 1234567890;
        $this->x84 = 1234567890;
        $this->x85 = 1234567890;
        $this->x86 = 1234567890;
        $this->x87 = 1234567890;
        $this->x88 = 1234567890;
        $this->x89 = 1234567890;
        $this->x90 = 1234567890;
        $this->x91 = 1234567890;
        $this->x92 = 1234567890;
        $this->x93 = 1234567890;
        $this->x94 = 1234567890;
        $this->x95 = 1234567890;
        $this->x96 = 1234567890;
        $this->x97 = 1234567890;
        $this->x98 = 1234567890;
        $this->x99 = 1234567890;
    }
}

$repeat = 300;

$arr = array();
for ($i = 0; $i < 100; $i++) {
    $arr[] = new XTest;
}
?>
```

<p>APC:</p>

```php
<?php
/*
 * @file t1.php
 */
include_once 'data.php';

$stime = microtime(true);

for ($i = 0; $i < $repeat; $i++) {
    apc_store('key'.$i, $arr);
    apc_fetch('key'.$i);
}

$etime = microtime(true);

echo $etime-$stime;
?>
```

<p>文件：</p>

```php
<?php
/*
 * @file t2.php
 */
include_once 'data.php';

$stime = microtime(true);

for ($i = 0; $i < $repeat; $i++) {
    file_put_contents('/tmp/xtest_key'.$i.'.srl', serialize($arr));
    unserialize(file_get_contents('/tmp/xtest_key'.$i.'.srl'));
}

$etime = microtime(true);

echo $etime-$stime;
?>
```

<p>Memcached:</p>

```php
<?php
/*
 * @file t3.php
 */
include_once 'data.php';

$mem = new Memcached;
$mem->addServer("127.0.0.1", 11211);

$stime = microtime(true);

for ($i = 0; $i < $repeat; $i++) {
    $mem->set('key'.$i, $arr);
    $mem->get('key'.$i);
}

$etime = microtime(true);

echo $etime-$stime;
?>
```

<h2>测试结果</h2>

<table>
    <tr>
        <th>APC</th>
        <th>文件</th>
        <th>Memcached</th>
    </tr>
    <tr>
        <td>3.4926421642303</td>
        <td>3.6572530269623</td>
        <td>4.6224999427795</td>
    </tr>
</table>

<h2>总结</h2>

<ul>
<li>APC效率最高，Memcached效率最低</li>
<li>APC和Memcached的测试结果很稳定，文件方式的耗时从开始时的4秒逐步降低并稳定在3.6秒</li>
<li>猜想：
<ul>
<li>在IO量较小的情况下，文件读写可能确实比通过TCP操作Memcached效率高，但是在IO量较大的情况下，文件方式会出现瓶颈，Memcached的优势会得到体现</li>
<li>对于集群应用，通过NFS共享文件缓存的效率会低于Memcached</li>
</ul></li>
</ul>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

