# PHP大量常量应集中使用APC定义

<p>用xdebug跟踪程序性能时，发现一个定义了约七百个常量的文件，include_once()时消耗约六十毫秒的时间。事实上define()的效率比较低，如果需要定义大量常量，使用APC扩展提供的apc_define_constants()效果会好得多。</p>

<p>下面是对这个文件改造前后效率跟踪结果的对比：</p>

<table>
    <tr>
        <th></th>
        <th>define()方案（ms）</th>
        <th>apc_define_constants()方案（ms）</th>
    </tr>
    <tr>
        <td>启动Web服务和PHP，第一次运行</td>
        <td>58</td>
        <td>4</td>
    </tr>
    <tr>
        <td>不重启Web服务和PHP，第二次运行</td>
        <td>65</td>
        <td>1</td>
    </tr>
    <tr>
        <td>不重启Web服务和PHP，第三次运行</td>
        <td>66</td>
        <td>1</td>
    </tr>
</table>

<p>由此可见，apc_define_constants()不但在初次调用时效能就超过define()，而且由于APC自身的缓存功能，在后续调用时效率会有进一步的提高，而define()基本没有变化。</p>

<p>以下是apc_define_constants()的代码示例：</p>

```php
<?php
if(!apc_load_constants('my_constants')) {
    $constants = array(
        'ONE'   => 1,
        'TWO'   => 2,
        'THREE' => 3,
    );
    apc_define_constants('my_constants', $constants);
}
?>
```

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

