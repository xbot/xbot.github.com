# 使用Vim写博客

<p><a href="http://www.vim.org/scripts/script.php?script_id=3510">VimRepress</a>是Vim写博客的插件中较新的一个，是久未更新的<a href="http://www.vim.org/scripts/script.php?script_id=1953">Vimpress</a>的一个衍生版。</p>

<p>虽然不是所有此类插件中功能最多的一个，但VimRepress非常实用，除包含最常用的几个功能外，还支持<a href="http://daringfireball.net/projects/markdown/">Markdown</a>。但是当前版本的VimRepress在转换Markdown格式的字符串到HTML时，是通过直接调用外部命令<strong>markdown</strong>来实现的，这显然只是针对Linux（及其它类UNIX）系统设计的。</p>

<p>为了使VimRepress支持在Windows下使用Markdown写文章，可以对它做一些改进。</p>

<p>修改VimRepress的源文件<strong>blog.vim</strong>，在<code>if __name__ == "__main__":</code>这一行的上方加入如下两个函数：</p>

<p>
```python
def markdown_preview2():
    import sys
    reload(sys)
    sys.setdefaultencoding('utf-8')
    import markdown2 as mkd

    global vimpress_temp_dir
    if vimpress_temp_dir == '':
        vimpress_temp_dir = tempfile.mkdtemp(suffix="vimpress")
    temp_htm = os.path.join(vimpress_temp_dir, "vimpress_temp.htm")
    html_heads = \
"""<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
"""
    tmp_file = open(temp_htm, 'w')
    tmp_file.write(html_heads)
    txt = "\n".join(vim.current.buffer[:])
    #txt = unicode(txt,'cp936')
    tmp_file.write(mkd.markdown(txt))
    #tmp_file.write(mkd.markdown("\n".join(vim.current.buffer[:])))
    tmp_file.close()

    webbrowser.open("file://%s" % temp_htm)

def markdown_newpost2():
    import sys
    reload(sys)
    sys.setdefaultencoding('utf-8')
    import markdown2 as mkd

    global vimpress_temp_dir
    if vimpress_temp_dir == '':
        vimpress_temp_dir = tempfile.mkdtemp(suffix="vimpress")
    temp_htm = os.path.join(vimpress_temp_dir, "vimpress_post.htm")

    title = ""
    title_s = 0
    try:
        while title_s < 10:
            if vim.current.buffer[title_s].startswith("#"):
                title = vim.current.buffer[title_s].strip('#')
                break
            title_s += 1
    except IndexError:
        pass

    cur_file = vim.eval('expand("%:p")')
    if cur_file is None: 
        cur_file = os.path.join(vimpress_temp_dir, "tmp_vimpress.mkd")
        sys.stdout.write("\n\nCurrent buffer saved to %s\n\n" % cur_file)
    vim.command(":w! %s" % cur_file)
    tmp_file = open(temp_htm, 'w')
    tmp_file.write(mkd.markdown("\n".join(vim.current.buffer[:])))
    tmp_file.close()
    sys.stdout.write("Press ENTER to continue.")
    vim.command(":bdelete")
    vim.command(":r %s" % temp_htm)
    blog_new_post(title = title)
```
</p>

<p>这两个函数修改自原有的<strong>markdown<em>preview()</strong>和<strong>markdown</em>newpost()</strong>，由调用外部的<strong>markdown</strong>命令改为调用Python的<a href="http://code.google.com/p/python-markdown2/">markdown2</a>模块来实现格式转换。</p>

<p>然后，修改<strong>blog.vim</strong>中的命令映射，使VimRepress的<code>:MarkDownPreview</code>和<code>:MarkDownNewPost</code>命令在Windows下自动调用上述两个函数：</p>

<p>
```vim
if has("win32")
    command! -nargs=0 MarkDownPreview exec('py markdown_preview2()')
    command! -nargs=0 MarkDownNewPost exec('py markdown_newpost2()')
else
    command! -nargs=0 MarkDownPreview exec('py markdown_preview()')
    command! -nargs=0 MarkDownNewPost exec('py markdown_newpost()')
endif
```
</p>

<p>最后，只须安装Python的markdown2模块就可以用了。</p>

<p><strong>2011-03-23更新：</strong></p>

<p><a href="http://apt-blog.net/manage-multiple-wordpresses-with-new-vimpress">pentie</a>已合并这些功能到<a href="http://ptcoding.googlecode.com/svn/trunk/vimpress/">新版本</a>的VimRepress。</p>

<p><strong>2011-04-04更新：</strong></p>

<p>作为Markdown写博的重度患者，我觉得Vim的其它相关插件都缺乏对Markdown源码的有效管理，所以我干脆写了个新的插件<a href="http://0x3f.org/?p=1894">UltraBlog</a>。</p>

