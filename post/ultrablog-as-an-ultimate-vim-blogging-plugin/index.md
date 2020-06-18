# UltraBlog.vim: Ultimate Vim Blogging Plugin


<h2>Introduction</h2>

UltraBlog.vim is yet another Vim blogging script for Wordpress.

The biggest difference between UB and other similar scripts is that UB is an ultimate client, which stores posts locally in an SQLite database, while others just operate remotely. In this way, many things can be done easily by the advantage of local storage and many other utilities, for example, you can search for posts stored in the database with a few keywords by typing a simple command, also you can preview drafts with any of your favorite templates as soon as you want to.

For those who just needs a lightweight blog editor similar as other Vim blogging scripts, UB also comes with an Editor Mode, which doesn't create a database and store data in it.

In addition, UB is tending to make life easier for writing posts with many kinds of lightweight markup languages, currently the following kinds are supported: Markdown, reStructuredText, LaTeX, Textile and of cause HTML.

Enjoy Vim blogging !

[Here](/post/ultrablog-development-note/) is a post written in Chinese describing the motion for which I wrote UltraBlog.vim.

<h2>Features</h2>

* Multiple syntax support: Markdown, HTML, reStructuredText, LaTeX, Textile.
* Editor mode and client mode.
* Data is stored in a local SQLite database in client mode.
* Full-text search with keywords highlighted.
* Full-text search by using regular expressions.
* Full-text substitutions.
* Full-text substitutions using regular expressions.
* Templates for previewing posts.
* Built-in web browser, much faster to preview items.
* Event-driven system.
* I18N.

<h2>Tutorial</h2>

<h3>Requirement</h3>

UltraBlog.vim takes advantages from the following techs:

* Vim with python support
* [SQLAlchemy](http://www.sqlalchemy.org) v0.7 or newer
* [python-markdown](http://www.freewisdom.org/projects/python-markdown/) or [python-markdown2](https://github.com/trentm/python-markdown2)
* [pandoc](http://johnmacfarlane.net/pandoc/)

You must have these prerequisites met before using UltraBlog.vim. For more information, refer to **UltraBlog_Prerequisites** in the help file.

<h3>Installation</h3>

Download UltraBlog.vim from [Vim.org](http://www.vim.org/scripts/script.php?script_id=3532) or retrieve the latest source from Github:

https://github.com/xbot/UltraBlog.vim

Install UltraBlog.vim to your plugin folder and put the following lines in your vimrc file:

```vim
let ub_blog = {'login_name':'admin',
            \'password':'pass2011',
            \'url':'http://www.sample.com',
            \'xmlrpc_uri':'xmlrpc.php',
            \'db':'~/.vim/UltraBlog.db',
            \'categories':'News|Computer|Image'
            \}

" Set this option to 1 if you want to enable debug mode, see :help ub_debug for more information
let ub_debug = 0

" Set this option to 0 if you prefer using firefox or chromium to preview items.
let ub_use_ubviewer = 1

" Default timeout period of xmlrpc operations, see :help ub_socket_timeout for more information
let ub_socket_timeout = 10

" Default page size of local post list, see :help ub_local_pagesize for more information
let ub_local_pagesize = 30

" Default page size of remote post list, see :help ub_remote_pagesize for more information
let ub_remote_pagesize = 15

" Default page size of search result list, see :help ub_search_pagesize for more information
let ub_search_pagesize = 30

" Proudly show your visitors that you are blogging with the world's most powerful editor
let ub_append_promotion_link = 1

" Set width of the local id column in post or page lists
let ub_list_col1_width = 7

" Set width of the remote id column in post or page lists
let ub_list_col2_width = 8

" Set width of the status column in post or page lists
let ub_list_col3_width = 11

" Set this value to 1 if you want to use editor mode.
let ub_editor_mode = 0

" Set this value to 1 if you want to save posts/pages immediately after they are fetched from the blog.
let ub_save_after_opened = 0

" Set this value to 0 if you do not want to save posts/pages immediately after they are sent to the blog.
let ub_save_after_sent = 1

" Set the following options to use a custom extenal command as the converter.
let ub_converter_command = 'pandoc'
let ub_converter_options = ['--reference-links']
let ub_converter_option_from = '--from=%s'
let ub_converter_option_to = '--to=%s'

" Customize hotkeys
let ub_hotkey_open_item_in_current_view='<enter>'
let ub_hotkey_open_item_in_splitted_view='<s-enter>'
let ub_hotkey_open_item_in_tabbed_view='<c-enter>'
let ub_hotkey_delete_item='<del>'
let ub_hotkey_save_current_item='<C-S>'

" Set the link template string for images uploaded by :UBUpload
let ub_tmpl_img_url="markdown###![$(file)s][]\n[$(file)s]:%(url)s"

" Set the default template to use when previewing posts/pages locally
let ub_default_template="default"
```

Change values of the upper options to yours. Restart Vim and a database file will be created in the path you specified above.

<h3>Concepts</h3>

It is important to understand some basic concepts of UltraBlog.vim.

<h4>Modes</h4>

UltraBlog.vim makes life easier while writing or updating blogs. It stores posts/pages in a local SQLite database. You can also set it to editor mode, in which UltraBlog.vim does not store data locally, just like other Vim blogging scripts.

By default, UltraBlog.vim is in client mode. You can set it to use editor mode by adding the fallowing line to the vimrc file:

```vim
let ub_editor_mode = 1
```

<h4>Items</h4>

Currently, UltraBlog.vim manages three items: post, page and tmpl. "tmpl" is the shorthand of "template".

Templates are used to preview the current post/page in the browser locally. This feature is a reparation for the remote previewing, due to the limit of the API, users cannot send a post to Wordpress as draft and preview it without affecting the post status if the post has been published.

With templates, users can preview posts/pages directly in the browser in a pre-defined style. They can create as many templates as they like and customize the look with CSS and HTML, or even Javascript.

Templates should be formatted as a valid python template string, that is, use the following avaliable placeholders and escape any literal '%' with another '%':

<blockquote>
<ol>
<li>%(title)s<br />
   The title of the current post/page.</li>
<li>%(content)s<br />
   The content of the current post/page.</li>
<li>%%<br />
   A literal '%'.</li>
</ol>
</blockquote>

There is a default template in the database, whose name is 'default', which can be used as an example of template.

Users can specify the default template to use with the option **ub_default_template**.

<h4>Syntaxes</h4>

The syntaxes supported by UltraBlog.vim currently are:

> markdown, html, rst, textile, latex.

<h4>Statuses</h4>

The available statuses are:

> publish, private, draft, pending.

<h4>Scopes</h4>

Scopes tells UltraBlog.vim to operate on items in which place, "local" stands for items stored in the database, and "remote" stands for the blog.

<h2>Usage</h2>

<h4>Create a new item</h4>

<code>:UBNew [item [syntax/template_name]]</code>

Create a new item.

For the first parameter, refer to **UltraBlog_Items**. The default value is "post".

If "item" is either "post" or "page", the second parameter must be a syntax name, refer to **UltraBlog_Syntaxes**. The default value is "markdown".

If "item" is "tmpl", the second parameter should be the name of the new template.

When the cursor is focused in the metadata line of categories, press **<C-X><C-U>** to do auto-completion. This will use the data set by option ub_blog['categories'], if it is not set, categories will be fetched from your blog.

<h4>Save a modified post</h4>

<code>:UBSave</code>

After executing this command, the current buffer is saved into database.

<h4>Send a post to blog</h4>

<code>:UBSend [status]</code>

Post an item.

If no parameter is given, UltraBlog.vim will send the item to blog and set it to be the value stored in the meta information area.

Refer to **UltraBlog_Statuses**.

<h4>List posts</h4>

<code>:UBList [item [scope [page_size [page_no]]]]</code>

List items.

Refer to **UltraBlog_Items** for the first parameter. The default value of this parameter is "post".

The second parameter "scope" is only available when "item" is either "post" or "page". Refer to **UltraBlog_Scopes**.

"page_size" and "page_no" are both for the situation when "item" is "post" and "scope" is "local". The former stands for how many item will be listed a page. The latter stands for the page number.

For example:

<code>:UBList</code>

This command lists the first page of local posts, by default, posts which have not been posted to blog are listed before the posted ones, and there are **ub_local_pagesize** posts a page.

<code>:UBList post local 20 3</code>

This command lists the third page of local posts, 20 posts a page. As you see, you can use this command to scroll forward or back between pages. As a matter of fact, there are two key mappings within local post list:

* CTRL+PageDown
* CTRL+PageUp

<code>:UBList post remote 50</code>

This command lists the latest 50 posts in the blog.

Pressing the ENTER key in a remote post list will open the post under cursor and save it to the local database if it is not in it, otherwise, the local copy will be opened instead of the remote one. This enables users to modify markdown source and update the remote post.

The remote post list doesn't support paging.

<h4>Open a single post</h4>

<code>:UBOpen {item} {post_id/template_name} [scope]</code>

Open an item.

For the first parameter, refer to **UltraBlog_Items**.

If "item" is either "post" or "page", the second parameter should be value of its id. If it's "tmpl", the name.

For "scope", refer to **UltraBlog_Scopes**. The default value is "local".

<h4>Upload a media</h4>

<code>:UBUpload {file_path}</code>

This command can only be executed in a post edit view, and the URL of the uploaed file will be appended in that buffer.

<h4>Preview the changes</h4>

<code>:UBPreview [status/template name]</code>

Preview the content of the current buffer.

If any of the **UltraBlog_Statuses** is given, the current buffer will be sent to the blog and then opened in the browser with a parameter "preview" appended to the URL.

If the given parameter is not a post status, a pre-defined template whose name is the same with the parameter will be used to preview the buffer locally.

If none is given, the default template is used.

You do not have to care for which syntax you use, markdown source will be translated into html automatically before a browser window is opened to display it.

The matter that whether to use the built-in web browser or the system default ones is controlled by the option **ub_use_ubviewer**.

<h4>Delete a post/page</h4>

<code>:UBDel {item} {post_id/template_name} [scope]</code>

Delete an item.

Refer to **UBOpen** for the usage of these options.

You can also delete items in the list by pressing the DELETE button on the target. In a local post list, if the post to be deleted has been posted to the blog, a confirmation will be prompted for you to decide whether to delete the remote copy cascadly.

<h4>Create a post/page using content of the current buffer</h4>

<code>:UBThis [item [to_syntax [from_syntax]]]</code>

Create a new post or page which is filled with content in the current buffer.

If no parameter is specified, the first parameter will be default to "post"; for the second and the third one, the syntax of the current buffer is used.

Content of the current buffer will be automatically converted from the **from_syntax** to the **to_syntax**.

<h4>Convert a post/page buffer between the available syntaxes</h4>

<code>:UBConv {to_syntax} [from_syntax]</code>

Convert the current buffer from 'from_syntax' to 'to_syntax'.

Refer to **UltraBlog_Syntaxes**.

If you only need to convert from Markdown to HTML, only python-markdown or python-markdown2 module is required. For other conversion scenarios, you must install pacdoc or use the options: **ub_converter_command**, **ub_converter_options**, **ub_converter_option_from**, **ub_converter_option_to** to specify a valid external command.

<h4>Refresh the current buffer</h4>

<code>:UBRefresh</code>

<h4>Full-text search</h4>

<code>:UBFind keyword1 [keyword2 ...]</code>

Doing full-text searches for both posts and pages by keywords, all keywords will be highlighted. Page size of the search result list is controlled by option **ub_search_pagesize**.

<code>:UBRegexSearch regexp1 [regexp2 ...]</code>

Doing full-text searches for both posts and pages by regular expressions, all strings that match the regular expressions will be highlighted. Page size of the search result list is controlled by **ub_search_pagesize**.

<h4>Full-text substitutions</h4>

**Attention:** Full-text substitutions will change contents of all matched posts/pages immediately when executed. You may need to backup your database file first.

<code>:UBReplace needle replacement</code>

Do full-text substitutions.

<code>:UBRegexReplace regexp replacement</code>

Do full-text substitutions using regular expressions.

<h4>Debug</h4>

In debug mode, SQL statements being executed will be displayed and so are all stack traces of exceptions raised.

<code>:UBEnableDebug</code>

Enable debugging.

<code>:UBDisableDebug</code>

Disable debugging.

<code>:UBToggleDebug</code>

Toggle debugging status.

<h3>Options</h3>

For more detail information on tweaking UltraBlog.vim, refer to the documentation.

<h2>Tips</h2>

* Install syntax files for any light weight markup language you use to highlight the posts/pages.
* [Enable Code Snippet Highlighting For UltraBlog.vim Templates](/post/enable-code-snippet-highlighting-for-ultrablog-vim-templates/)

<h2>News</h2>

* 2014-04-20 v3.6.1 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2012-04-30 v3.5.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2012-04-29 v3.4.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2012-04-15 v3.3.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2012-01-05 v3.2.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-11-01 v3.1.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-07-24 v3.0.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-06-15 v2.3.1 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-06-10 v2.3.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-05-30 v2.2.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-05-28 v2.1.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-05-12 v2.0.1 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-04-14 v2.0.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-04-12 v1.4.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-04-09 v1.3.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-04-07 v1.2.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-04-05 v1.1.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-04-04 v1.0.5 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-04-02 v1.0.4 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-04-02 v1.0.3 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-04-02 v1.0.2 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-04-01 v1.0.1 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)
* 2011-04-01 v1.0.0 released. [Changes](https://github.com/xbot/UltraBlog.vim/wiki/Changelog)

Posted via [UltraBlog.vim](/post/ultrablog-as-an-ultimate-vim-blogging-plugin/).

