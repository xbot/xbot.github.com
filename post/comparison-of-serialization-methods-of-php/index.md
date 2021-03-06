# PHP序列化方案效率比较

<p>同时对以下几种PHP的序列化方案进行了测试：</p>

<ul>
<li>serialize() and unserialize()</li>
<li>json_encode() and json_decode()</li>
<li>msgpack_serialize() and msgpack_unserialize()</li>
<li>igbinary_serialize() and igbinary_unserialize()</li>
</ul>

<p>测试环境：</p>

<ul>
<li>OS:        Archlinux 32-bit</li>
<li>CPU:       CORE 2 DUO T9400, 2.53GHz</li>
<li>Mem:       DDR3, 3G</li>
<li>Server:    nginx v1.0.0</li>
<li>PHP:       php v5.3.6 + php-fpm</li>
<li>Profiler:  xhprof v0.9.2</li>
</ul>

<p>测试代码块：</p>

```php
<?php
class Test{
    function __construct(){
        $this->value = str_repeat('a', 1000);
    }
}

function produce($num)
{
    $arr = array();
    for ($i = 0; $i < $num; $i++) {
        $arr[] = new Test();
    }

    $data = serialize($arr);

    $arr = unserialize($data);

    $data = json_encode($arr);

    $arr = json_decode($data);

    $data = msgpack_serialize($arr);

    $arr = msgpack_unserialize($data);

    $data = igbinary_serialize($arr);

    $arr = igbinary_unserialize($data);
}

produce(10000);
?>
```

<p>测试结果：</p>

<p><a href="https://picasaweb.google.com/lh/photo/TAyUpJxyW3RVj18DU4B9gg?feat=embedwebsite"><img src="https://lh6.googleusercontent.com/-2iBCMB4kyOg/Taq-aeruqEI/AAAAAAAABp4/wyqG-Qt5s0c/s400/php_serialization.png" height="124" width="400" /></a></p>

<ul>
<li>序列化性能：serialize() <strong><em>24.611ms</em></strong> &gt; msgpack_serialize() <strong><em>32.687ms</em></strong> &gt; igbinary_serialize() <strong><em>36.012ms</em></strong> &gt; json_encode() <strong><em>132.142ms</em></strong></li>
<li>反序列化性能：igbinary_unserialize() <strong><em>12.141ms</em></strong> &gt; msgpack_unserialize() <strong><em>17.185ms</em></strong> &gt; unserialize() <strong><em>28.723ms</em></strong> &gt; json_decode() <strong><em>183.141ms</em></strong></li>
<li>综合性能：igbinary <strong><em>48.153ms</em></strong> &gt; msgpack <strong><em>49.872ms</em></strong> &gt; serialize <strong><em>53.334ms</em></strong> &gt; json <strong><em>315.283ms</em></strong></li>
</ul>

<p>测试结果并没有像传说中的那样，官方的序列化和反序列化函数性能与msgpack和igbinary两个第三方的扩展相差不大，而JSON性能非常差。</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

