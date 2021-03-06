# 从iG:Syntax Hiliter转换到WP-Syntax

<p>终于决定放弃使用了<a href="http://0x3f.org/?p=342">三年多</a>的iG:Syntax Hiliter，将代码语法高亮插件换成<a href="http://wordpress.org/extend/plugins/wp-syntax/">WP-Syntax</a>。</p>

<p>原因是Vim的Markdown语法高亮<a href="http://www.vim.org/scripts/script.php?script_id=2882">插件</a>有问题，会把iG的标签<code>[LANG]</code>当作Markdown的超链接处理，结果导致插入代码后，后面的内容被错误地著色，很难看。而Syntax使用<code>&lt;pre lang="LANG"&gt;</code>格式的标签，不会有这个问题。</p>

<p>其实很早就想过要换了，只是受累于使用iG进行高亮的文章太多，替换起来不方便。加上后来<a href="http://0x3f.org/?p=501">解决</a>了由于iG停止更新导致的不支持更多的语法的问题，所以就一致拖到现在。今天晚上花了一个小时就完成了替换，比想象中的要容易地多。</p>

<p>根据我使用过的语法的情况，使用以下语句在数据库中替换iG的标签到Syntax的标签：</p>

```sql
update wp_posts set post_content=replace(post_content,'[bash]','&lt;pre lang="bash" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/bash]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[python]','&lt;pre lang="python" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/python]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[vim]','&lt;pre lang="vim" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/vim]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[java]','&lt;pre lang="java" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/java]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[lua]','&lt;pre lang="lua" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/lua]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[sql]','&lt;pre lang="sql" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/sql]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[plsql]','&lt;pre lang="plsql" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/plsql]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[php]','&lt;pre lang="php" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/php]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[cpp]','&lt;pre lang="cpp" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/cpp]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[inno]','&lt;pre lang="inno" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/inno]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[xml]','&lt;pre lang="xml" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/xml]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[html]','&lt;pre lang="html4strict" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/html]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[ini]','&lt;pre lang="ini" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/ini]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[pascal]','&lt;pre lang="pascal" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/pascal]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[javascript]','&lt;pre lang="javascript" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/javascript]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[reg]','&lt;pre lang="reg" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/reg]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'[css]','&lt;pre lang="css" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'[/css]','&lt;/pre&gt;');
update wp_posts set post_content=replace(post_content,'&lt;coolcode LANG="php"&gt;','&lt;pre lang="php" line="1"&gt;');
update wp_posts set post_content=replace(post_content,'&lt;/coolcode&gt;','&lt;/pre&gt;');
```

<p>即便转换到Syntax，也不能避免Vim中Markdown语法高亮的问题，只要文字中出现成对的方括号，仍然会出问题，正确的做法是对HTML标签中的内容不进行Markdown语法的著色。</p>

<p>在转换和写这篇文章的过程中，发现WP-Syntax还存在一些问题，例如启用行号后部分表格不能充满页面宽度的问题，还有上面这些SQL语句，必须将其中的尖括号替换成HTML实体才能正常显示。看来虽然历经两三年的发展，WP-Syntax还是不很成熟。</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

