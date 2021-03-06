# ack: grep的同类替代品

<p><a href="http://betterthangrep.com">ack</a>是一个perl脚本，是grep的一个可选替换。</p>

<p>有以下优势：</p>

<ul>
<li>为程序员设计，使用方便，输入较少
<ul>
<li>默认递归搜索</li>
<li>缺省提供多种文件类型供选，文件类型对应的后缀名可修改</li>
</ul></li>
<li>使用perl的正则表达式，而grep只支持其一个子集</li>
<li>搜索结果高亮输出</li>
</ul>

<p>很多人以讹传讹，使人容易对ack的搜索速度产生误解。ack的官方宣传中说比grep快，其前提是ack可方便地针对部分后缀的文件进行搜索，而grep默认对所有文件搜索。ack的官方文档中明确指出，在对大量文件进行搜索时，grep的速度还是最快的。</p>

<h2>~/.ackrc</h2>

<p>使用此配置文件存储ack的常用选项。</p>

<blockquote>
  <p>-i
  --type-add
  php=.lib</p>
</blockquote>

<p>如上所示，该文件中每一行应该是ack命令行参数中用空格分隔的一个部分。</p>

<h2>例子</h2>

<p>如果搜索的关键词是正则表达式，在Linux下必须将其用双引号包围起来。</p>

<p>如：</p>

```bash
ack "gbldb\s*="
```

<h2>Windows下的安装</h2>

<ul>
<li>安装<a href="http://strawberryperl.com">strawberryperl</a></li>
<li>保存<a href="http://betterthangrep.com/ack-standalone">ack.pl</a>到<em>C:\bin</em></li>
```dos
@echo off
perl.exe c:/bin/ack.pl %*
```
<li>将<em>C:\bin</em>加入环境变量<strong>%PATH%</strong></li>
</ul>

<h2>资源</h2>

<ul>
<li><a href="http://www.vim.org/scripts/script.php?script_id=2572">ack.vim</a>: Vim使用ack的辅助扩展</li>
</ul>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

