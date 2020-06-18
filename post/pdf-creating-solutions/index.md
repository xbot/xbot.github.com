# 生成PDF的方案调研

## 结论
倾向于通过wkhtmltopdf+消息队列实现。

工作流程：
 1.  用户保存表单
 2.  推送生成pdf的任务到队列
 3.  同时页面显示「正在生成PDF文档，请稍候」并轮询后端接口
 4.  PDF生成后，页面显示下载按钮

## 后端

### dompdf
纯PHP实现。工作原理是把HTML转换成PDF。

优点：
 1.  不需要PHP调用命令行，安全性高。

缺点：
 1.  HTML/CSS支持不完全，实测多个网页转换后排版错乱。
 2.  中文网页转换后，中文都是问号（网上说有解决办法[1](http://www.cnblogs.com/xxoome/p/6083542.html)、[2](http://blog.51cto.com/lampzxr/1916038)，未测试）。
 3.  CPU占用比较高（网上的说法，没有实测具体有多高）。

参考：

*  [laravel-dompdf主页](https///github.com/barryvdh/laravel-dompdf)

### mpdf

纯PHP实现。工作原理是把HTML转换成PDF。

优点：
 1.  不需要PHP调用命令行，安全性高。

缺点（未实测）：
 1.  HTML/CSS支持不完整。
 2.  生成PDF耗时长。

参考：

*  [laravel-pdf主页](https///github.com/niklasravnsborg/laravel-pdf)
*  [mpdf文档](https///mpdf.github.io)
*  [mpdf主页](https///github.com/mpdf/mpdf)
*  [mpdf与fpdf的使用比较](http://www.cnblogs.com/attitudeY/p/7297948.html)

### PDFtk Server

PDFtk Server是个命令行程序。工作原理是利用FDF表单替换PDF模板中的占位符。实现方案有：纯PHP实现FDF + PDFtk、FPDI + PDFtk。

优点：
 1.  直接替换PDF模板，实现成本低，最大程度保证生成的PDF的效果。

缺点：
 1.  需要允许PHP调用命令行，有安全隐患。
 2.  只能实现简单的字符串替换（例如公司名称），不能替换有格式文本。

参考：

*  [FPDI](https///www.setasign.com/products/fpdi/manual/#p-58)
*  [PHP的FDF手册](http://php.net/manual/zh/intro.fdf.php)
*  [PDFtk的使用方法](https///stackoverflow.com/questions/1389964/merge-fdf-data-into-a-pdf-file-using-php)
*  [纯PHP实现FDF + PDFtk](https///www.sitepoint.com/filling-pdf-forms-pdftk-php/)

### wkhtmltopdf

wkhtmltopdf是个命令行程序。工作原理是转换HTML到PDF。实现方案是laravel-snappy。

优点：
 1.  由于内嵌webkit核心，HTML/CSS的支持没有问题。

缺点：
 1.  需要允许PHP调用命令行，有安全隐患。
 2.  wkhtmltopdf体积40M，并发较多时影响服务器性能和稳定性（需要考虑用队列辅助实现，异步执行，需要需求变更交互方式）。

参考：

*  [laravel-snappy主页](https///github.com/barryvdh/laravel-snappy)

### CutyCapt

命令行程序。工作原理和wkhtmtopdf相同。

优缺点同wkhtmltopdf，但是最近的更新在13年。

参考：

*  [CutyCapt主页](http://cutycapt.sourceforge.net)

### Prince

命令行程序。工作原理是转换HTML到PDF。

优点：
 1.  HTML/CSS的支持很好。
 2.  文档很全

缺点：
 1.  收费，而且很贵。（免费版会在输出的文档右上角打一个Logo）
 2.  需要允许PHP执行命令行，有安全隐患。
 3.  需要考虑用队列辅助实现，异步执行，需要需求变更交互方式。

参考：

*  [Prince官网](https///www.princexml.com)

## 前端

### jsPDF
工作原理有两种：编程方式动态生成和转换HTML到PDF。有三个插件支持转换HTML到PDF：fromHTML、addHTML和html2pdf。

fromHTML最老，优点是直接转换HTML到PDF，缺点是对复杂的HTML/CSS支持得不好。实测结果，UTF-8编码的中文网页，转换到PDF都是乱码。

addHTML较新，但目前处于deprecated状态，利用html2canvas/rasterizeHTML创建一个canvas，然后把HTML转换成图片、再转换成PDF。实测结果，对中文网页和CSS支持得都不错，但是对分页支持得不好，指定分页选项后图片被拉伸并强行分割，很难看。

html2pdf是正在开发的功能，还没完成。

参考：

*  https://stackoverflow.com/questions/44778260/what-are-the-differences-between-jspdfs-methods-addhtml-and-fromhtml

### pdfkit

只能通过编程（//调用接口指定内容、分页等相关属性//）生成PDF，不支持从HTML转换到PDF。

### pdfmake

据说扩展自pdfkit。只能通过编程（//调用接口指定内容、分页等相关属性//）生成PDF，不支持从HTML转换到PDF。而且需要引用字体文件转换成的js文件，不适合中文内容。

## 变通方案

### 利用浏览器打印PDF
Chrome、Firefox、Safari的最新版都支持打印网页到PDF，Edge未测试。

优点：
 1.  实现成本低。

缺点：
 1.  用户体验差。

### 导出word文档

优点：
 1.  实现成本低。（未证实）

缺点：
 1.  用户可更改文档。

## 参考

*  https://blog.stahlmandesign.com/export-html-to-pdf-how-hard-can-it-be/
*  [PHP输出PDF打印HTML5+CSS3打印格式控制(Prince和wkhtmltopdf)](https///my.oschina.net/janpoem/blog/705912)


