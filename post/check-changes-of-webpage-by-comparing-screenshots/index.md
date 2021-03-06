# 通过比较截图检查网页变化


有时候我们希望检查代码或数据的更新对网页产生的影响，如果能把这个过程集成到git等版本控制系统中，在提交成果的同时自动批量执行，将会极大地提高生产力。

首先网页的截图可以用PhantomJS、Headless Chrome实现：

```javascript
var page = require('webpage').create();
page.open('http://github.com/', function() {
  page.render('github.png');
  phantom.exit();
});
```

图片的差异比较用GraphicsMagick实现：

```bash
gm compare old.png new.png -file diff.png -highlight-style assign
```

为方便查看，还可以把截图做成gif动图：

```bash
gm convert -delay 20 old.png diff.png new.png diff.gif
```


