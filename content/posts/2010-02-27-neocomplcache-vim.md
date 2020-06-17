---
layout: post
title: Vim的终极自动补全插件：NeoComplCache
slug: neocomplcache vim
date: 2010-02-27 00:00:00
categories:
- 计算机
tags:
- Vim
- 配置
status: publish
published: true
comments: true
---

![](http://lh3.ggpht.com/_ceUJ_lBTHzc/SaV494hGreI/AAAAAAAAAzY/xExf7CzGyv0/s800/the-mug-of-vi.jpg)

<p>关于自动补全，最初用的是<a href="http://www.vim.org/scripts/script.php?script_id=1643">SuperTab</a>，那个时候Vim的自动补全插件寥寥无几，也就SuperTab比较有名。不过实际使用过程中这个插件给我的体验不是很好，原因是补全的准确度不高。</p>

<p>后来出了一个新插件，<a href="http://www.vim.org/scripts/script.php?script_id=1879">AutoComplPop</a>，功能和使用都很简单。但是很快我就又用回SuperTab，原因是AutoComplPop在输入的同时实时地查询匹配的关键词，导致输入极不流畅，效率很低。</p>

<p>一直就这么凑合着用着SuperTab，随着这个插件版本的更新，旧功能不断地完善，新功能也接二连三地引入，SuperTab在匹配关键词的准确度上有了一些改善，但是新的问题又出现了。SuperTab后来加入一个新功能，对于程序源文件，可以在其引入的文件以及API文件中匹配关键词。比如假设我当前正在编辑a.php，在a.php中有<strong>include 'b.php';</strong>这样的语句，当我输入<strong>array</strong>并按下Tab键时，SuperTab不但会在当前文件中查询所有匹配项，还会到b.php中查询，如果配置过vim、指定一个包含了php的API的文件，则SuperTab还会自动从这个文件中查询匹配项。按理说这个功能的理念很好，但问题就在于SuperTab做的是实时查询，如果源文件中包含的文件较多，各个文件又较大，问题就显而易见了。我不得不在写程序时小心地使用Tab键，否则有时就会出现按一下Tab键然后等着Vim在那狂搜的情况。</p>

<p>前两天发现了<a href="http://www.vim.org/scripts/script.php?script_id=2620">NeoComplCache</a>，光看名字就让我有点儿兴奋，一般使用缓存的速度都很快。这个插件会在Vim打开文件的时候对上下文作一个索引，并把索引结果保存到缓存中。同时，文件更改的内容会在保存的时候被索引。此外，NeoComplCache支持多种关键词索引模式，例如它会判断当前路径下的文件或目录的名字是否匹配补全条件，也可以从缓存的程序语言API中匹配补全条件。到此为止，它就解决了SuperTab和AutoComplPop共同的效率问题，并具备它们各自的长处。看了一遍文档，发现这个插件的功能比较细致，大概有以下一些特点：</p>

<p>1、使用缓存，自动补全时效率高；
2、生成的关键词列表准确；
3、支持下划线分割的关键词，如apple_boy_cat，就可以只输入a_b_c，然后补全；
4、支持驼峰格式匹配关键词，如AppleBoyCat，就可以只输入ABC，然后补全；
5、既可以像AutoComplPop那样在Vim中输入的同时自动弹出补全列表，又可以自定义快捷键手动触发；
6、支持从文件名和目录名中匹配补全条件；
7、对于程序源文件，支持从语言API中匹配补全条件；</p>

<p>NeoComplCache的缺点是文档不全，虽然从只言片语中发现它还支持Snippet，但从文档中没有找到足够的有用信息。加之一直用<a href="http://www.vim.org/scripts/script.php?script_id=2540">SnipMate</a>感觉不错，所以目前还是用它来实现snippet功能。</p>

<p>这就有个搭配问题：虽然NeoComplCache不存在补全时的效率问题，但我仍然打算只在需要补全时才用快捷键触发此功能，最主要的原因是我既希望用Tab键触发SnipMate的代码块补全功能，又希望修SuperTab那样用Tab选择补全列表中的选项。也就是要达到只用Tab键就可以完成打开自动补全列表、补全列表选项选择和SnipMate代码块替换的效果。但是，如果将Tab映射到触发自动补全，则补全列表选择和SnipMate均无法使用Tab，反之亦然。</p>

<p>所以我想如果能让NeoComplCache、SuperTab、SnipMate和谐共存，那问题就解决了，几经摸索，终于找到了办法：</p>

<p>1、设置NeoComplCache不自动弹出补全列表，即在vimrc中加入：</p>

<blockquote>
  <p>let g:NeoComplCache_DisableAutoComplete = 1</p>
</blockquote>

<p>2、由于NeoComplCache在手工模式下使用快捷键组合<code>&lt;C-X&gt;&lt;C-U&gt;</code>打开补全列表，故设置SuperTab的默认补全操作为<code>&lt;C-X&gt;&lt;C-U&gt;</code>，即在vimrc中加入：</p>

<blockquote>
  <p>let g:SuperTabDefaultCompletionType = '<code>&lt;C-X&gt;&lt;C-U&gt;</code>'</p>
</blockquote>

<p>这样，NeoComplCache只负责补全关键词缓存的生成，SuperTab控制Tab键的行为并在需要触发补全操作时打开补全列表、进而在列表中的选项间移动焦点，而当光标前的关键词是snippet时，SnipMate会被优先调用并完成代码块的替换。</p>

<p>就在写这篇文章的时候，我突然觉得NeoComplCache自动弹出补全列表+SnipMate的方式也挺好，只是这样就不能用Tab键选择列表中的选项了。</p>

### 相关阅读：

* [用neocomplete换掉了YouCompleteMe](/post/replace-youcompleteme-with-neocomplete/)
* [How to Make YouCompleteMe Compatible with UltiSnips](/post/make-youcompleteme-ultisnips-compatible/)
