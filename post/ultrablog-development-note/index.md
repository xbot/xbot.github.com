# UltraBlog.vim开发手记

<p>对于<a href="http://blog.sina.com.cn/s/blog_694377f90100rmko.html">博客已死</a>的说法，我一点都不感到奇怪。早在几年前博客还比较火的时候我就表达过对博客泡沫的<a href="http://0x3f.org/?p=209">看法</a>。</p>

<p>经历了短暂的<a href="http://0x3f.org/?p=354">tumblog</a>的风头，微博成为现在最火的媒介。说博客已死，无非就是说微博将成为个人信息传播的主流。好吧，现在我知道那时候我所说的不懂什么是博客的人都去干什么了。</p>

<p>但是博客还是会继续发展，微博的兴起只会减少滥竽充数。因为微博突出的是时效性，最缺乏的是系统性和可重复更新的能力。微博的短处，正是博客的长处。这让我对博客有了一个新的、迟到了很久的认识──一个好的博客，它的文章不应该是写掉就忘的，而应该是不断地更新著的。</p>

<p><a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>就是出于这个需要而写的。</p>

<p>我对博客客户端的使用大概可以追溯到四五年前，那时候博客正火，在比较了几个<a href="http://en.wikipedia.org/wiki/Blog_service_provider">BSP</a>后选择了<a href="http://www.blogcn.com/">中国博客网</a>，就是因为他们提供桌面客户端。后来独博之后也试用过几个通用的客户端，但都没坚持多久，最根本的原因是它们几乎无一例外地都是<a href="http://zh.wikipedia.org/wiki/%E6%89%80%E8%A6%8B%E5%8D%B3%E6%89%80%E5%BE%97">WYSIWYG</a>。而WYSIWYG的编辑器生成的HTML源码几乎都使用inline的样式，这就导致文章的排版布局很难控制，我对WYSIWYG其实是很抵触的，至今都不怎么用Office这类东西。</p>

<p>后来我意识到，我所需要的是样式与内容分离，于是准备自己写一个<a href="http://0x3f.org/?p=331">客户端</a>，后来由于工作的原因不了了之。与此同时，我自定义了一套文章格式并用Java写了一个转换文章到HTML的<a href="http://0x3f.org/?p=342">工具</a>，直到后来发现<a href="http://0x3f.org/?p=383">Markdown</a>，我才知道我其实是需要一个轻量标记语言。</p>

<p>使用Markdown之后，写博客开始变得轻松愉快。我一度用过一段时间<a href="http://www.scintilla.org/SciTE.html">SciTE</a>，还用<a href="http://zh.wikipedia.org/wiki/Lua">Lua</a>写了个<a href="http://0x3f.org/?p=499">辅助脚本</a>。但是我需要一个完整的客户端来管理文章，而这是一个编辑器所不能胜任的，所以就著手实现一个支持Markdown的<a href="http://0x3f.org/?p=812">客户端</a>。这一次走得比较远，<a href="http://0x3f.org/?p=812">ForeverFantasy</a>最终达到了可用的程度，我一度用它写过一段时间的博客。但是作为一个Vim重症患者，wxPython的编辑器部件是远远达不到我需要的水平的。虽然后来还<a href="http://0x3f.org/?p=1409">实现</a>了调用外部程序处理文章内容（<em>当然也可以调用Vim</em>），但总归觉得不舒服，至今已经一年没更新了。</p>

<p>直到前段时间试用了一下<a href="http://0x3f.org/?p=1861">VimRepress</a>，在修改这个插件的时候突然受到启发，我这几年的需求原来是可以这么简单地解决的。</p>

<p>其实在几年前<a href="http://www.vim.org/scripts/script.php?script_id=1953">vimpress</a>刚刚发布的时候我就了解过这个东西，包括此后接连发布的几个Vim写博客的插件，它们和我原来写的SciTE的那个辅助脚本没有本质的区别，都是博客编辑器。与完整的客户端相比，编辑器缺少对本地文章源码的管理，而且它们不记录文章源码和发布的文章的关联关系，简单地说是无状态的。这不符合博客文章持续更新的观点。</p>

<p>UltraBlog.vim使用<a href="http://www.sqlalchemy.org/">SQLAlchemy</a>做数据库抽象层，将所有文章的源码及其状态保存在本地的一个SQLite数据库中，并且以此为基础，通过<a href="http://en.wikipedia.org/wiki/XML-RPC">XMLRPC</a>接口实现对博客文章、页面的<a href="http://en.wikipedia.org/wiki/Create,_read,_update_and_delete">CRUD</a>操作。同时利用Vim分模式的特点实现了可分页的文章列表和文章编辑视图。</p>

<p>UltraBlog.vim目前支持Markdown和HTML两种格式的文章，它最大的特点是在Markdown格式的源码和发布到博客中的文章之间建立联系，你可以随时修改本地数据库中的Markdown源码并更新博客中对应的文章。</p>

<p>新的功能还会不断地加入。</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

