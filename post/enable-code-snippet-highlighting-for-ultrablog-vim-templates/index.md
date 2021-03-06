# Enable Code Snippet Highlighting For UltraBlog.vim Templates

<p>Templates make it possible for users previewing posts in custom formats in <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>. However, there is more you can do with our highly customizable templates. This article discusses how to highlight code snippets in posts by the use of some third-party open-source tools.</p>

<p>Before setting out to work, let me introduce the tools we are about to use:</p>

<ul>
<li><a href="http://softwaremaniacs.org/soft/highlight/en/">highlight.js</a>: A javascript library which highlights code blocks in web pages automatically.</li>
<li><a href="http://jquery.com/">jquery</a>: The famous RIA development javascript library.</li>
</ul>

<p>Download the upper two libraries and put them under any folder accessible to your webbrowser, in my case, it is <strong>/home/lenin/.vim/bundle/ultrablog/vendor/</strong>.</p>

<p>Then open UltraBlog.vim and edit the template you are using, put the following lines in the <code>&lt;head&gt;...&lt;/head&gt;</code> area:</p>

```xml
<link rel="stylesheet" href="/home/lenin/.vim/bundle/ultrablog/vendor/highlight/styles/default.css">
<script src="/home/lenin/.vim/bundle/ultrablog/vendor/highlight/highlight.pack.js"></script>
<script src="/home/lenin/.vim/bundle/ultrablog/vendor/jquery.js"></script>
<script>
$(document).ready(function(){
    $('pre').each(function(i, e){
        if(typeof e.lang != 'undefined')
            e.className = e.lang;
        hljs.highlightBlock(e, '    ');
    });
});
</script>
```

<p>If you are using the default template UltraBlog.vim supplies, change <strong>.title</strong> to <strong>.postTitle</strong> in the pre-defined <code>&lt;style&gt;...&lt;/style&gt;</code> area, and change the class name of the div element in the <code>&lt;body&gt;...&lt;/body&gt;</code> area from <strong>title</strong> to <strong>postTitle</strong>, that is because this class name is also used by highlight.js.</p>

<p>An important point, the jquery selector I used in the code snippet above selects every <code>&lt;pre&gt;...&lt;/pre&gt;</code> element and copies the value of its attribute <strong>lang</strong> to <strong>class</strong>. This is specified for the wordpress plugin <a href="http://wordpress.org/extend/plugins/wp-syntax/">wp-syntax</a> I use. If you use another syntax highlighting tool which uses different markup for code blocks, you should modify the code above to make highlight.js find code blocks correctly.</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

