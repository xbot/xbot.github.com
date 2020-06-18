# 用JavaScript实现分页打印

最近客户提的一个需求，要实现分页打印功能。公司产品对打印功能实现得不够好，排版全由程序生成，耦合度高，且不支持分页，需要用笨拙的方法变通。

于是我对如何更好地实现分页打印产生了兴趣，Google了一下，整理了一个Demo：

```html
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
        <style media=print>    
        .Noprint{display:none;}    
        .PageNext{page-break-after: always;}    
        </style>
    </head>
    <body>
        <OBJECT id=WebBrowser classid=CLSID:8856F961-340A-11D0-A96B-00C04FD705A2 height=0 width=0 VIEWASTEXT>
        </OBJECT>
        <div class="Noprint">
            <input   onclick=document.all.WebBrowser.ExecWB(6,1)   type=button   value=打印   name=Button>   
            <input   onclick=document.all.WebBrowser.ExecWB(7,1)   type=button   value=打印预览 name=Button>   
            <input   onclick=document.all.WebBrowser.ExecWB(8,1)   type=button   value=页面设置   name=Button4>   
        </div>
        <div class="PageNext">
            <center><h1>雅游</h1></center>
            <p>旧院人称曲中，前门对武定桥，后门在钞库街。妓家鳞次，比屋而居。屋宇精洁，花木萧疏，迥非尘境。</p>
            <p>到门则铜环半启，珠箔低垂;升阶则猧儿吠客，鹦哥唤茶；登堂则假母肃迎，分宾抗礼；</p>
            <p>进轩则丫鬟毕妆，捧艳而出；坐久则水陆备至，丝肉竞陈；定情则目眺心挑，绸缪宛转。</p>
            <p>纨绔少年，绣肠才子，无不魂迷色阵，气尽雌风矣。</p>
            <p>妓家，仆婢称之曰娘，外人呼之曰小娘，假母称之曰娘儿。</p>
            <p>有客称客曰姐夫，客称假母曰外婆。</p>
        </div>
        <div class="PageNext">
            <center><h1>木兰花令·拟古决绝词</h1></center>
            <center><h3>清·纳兰容若</h3></center>
            <p>人生若只如初见，何事秋风悲画扇。等闲变却故人心，却道故人心易变!</p>
            <p>骊山语罢清宵半，泪雨零铃终不怨。何如薄幸锦衣郎，比翼连枝当日愿!</p>
        </div>
    </body>
</html>
```

这个方法的好处是支持以低耦合的方式分页打印，同时支持只打印页面上指定的部分内容；缺点是由于使用了ActiveX，故只支持IE浏览器。

