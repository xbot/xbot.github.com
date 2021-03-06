# UltraBlog.vim v3.4.0: 正则表达式、批量替换和调试模式

<p>这次的更新主要引入了支持正则表达式的全文检索、批量替换和调试模式。</p>
<h2>正则表达式</h2>
<p>我一直觉得原来的全文检索有一个遗憾，虽然可以通过多个关键词实现较为精确的查询，但还是不如正则表达式灵活和精确。</p>
<p>这是个蓄谋已久的需求，但直到真正做起来，才发现很多有意思的东西。虽然SQLite3提供支持正则表达式查询的<strong>“REGEX”</strong>关键词，但并没有实际实现这个功能，而是需要写程序实现并在数据库接口中注册这个函数：</p>
```python
import sqlite3

conn = sqlite3.connect('/tmp/your-database-file.db')

# 使用正则表达式匹配给定内容的函数，返回布尔类型
def regexp_search(expr, item):
    """Check if the item has a sub-string which matches the expr"""
    reg = re.compile(expr)
    return reg.search(item) is not None

# 在数据库中注册这个函数
conn.create_function('REGEXP', 2, regexp_search)

cur = conn.execute('select id,title from post where content REGEXP ?', '\babc\b')
row = cur.fetchone()
print row

conn.close()
```

<p>在SQLAlchemy中具体的实现方式是：</p>
```python
# 注册函数
dbe = sqlalchemy.create_engine("sqlite:///tmp/your-database-file.db")
conn = dbe.connect()
conn.connection.create_function('REGEXP', 2, regexp_search)

# 在SQL Expression Language中创建查询条件
tbl = Post.__table__
cond_1 = tbl.c.title.op('regexp')(r'\babc\b')
cond_2 = tbl.c.content.op('regexp')(r'\babc\b')
```

<p>和普通的全文检索一样，正则表达式的全文检索也支持使用多个表达式作为查询条件，多个条件之间是与的关系。现在可以这样查询所有包含“UltraBlog.vim”但不把推广代码算在内的文章了：</p>
```vim
:UBRegexSearch [^\[]UltraBlog\.vim[^\]]
```

<h2>批量替换</h2>
<p>在我换过新域名后，我就觉得这个功能很有必要了：</p>
```vim
:UBReplace https://sinolog.it https://0x3f.org
```

<p>包含第一个参数内容并被替换的文章数目会显示在Vim的命令输出缓冲区中。</p>
<p>有了前面实现全文检索支持正则表达式的尝试，再实现支持正则表达式的批量替换就容易多了：</p>
```python
# 转换字符串成raw格式的函数
def raw(text):
    """Returns a raw string representation of text"""
    escape_dict={'\a':r'\a', '\b':r'\b', '\c':r'\c', '\f':r'\f', '\n':r'\n',
               '\r':r'\r', '\t':r'\t', '\v':r'\v', '\'':r'\'', '\"':r'\"',
               '\0':r'\0', '\1':r'\1', '\2':r'\2', '\3':r'\3', '\4':r'\4',
               '\5':r'\5', '\6':r'\6', '\7':r'\7', '\8':r'\8', '\9':r'\9'}
    return "".join([escape_dict.get(char,char) for char in text])

# 使用正则表达式替换字符串的函数
def regex_replace(string, expr, repl):
    """Do substitutions on the string for repls matching the expr"""
    r = re.compile(raw(expr))
    return r.sub(repl, string)

# 在数据库中注册这个函数
conn.connection.create_function('regex_replace', 3, regex_replace)

# 在SQL语句中使用这个函数
sql_replace = "update post set title=regex_replace(title,:needle,:replacement),content=regex_replace(content,:needle,:replacement)"
conn.execute(sql_replace, {'needle':r'\babc\b', 'replacement':'xyz'})
```

<p>最终，这个命令是这样的：</p>
```vim
:UBRegexReplace \babc\b xyz
```

<p>实现批量替换容易，但要解决由此引出的一个问题就费周折了，就是批量替换过文章内容后怎样和博客同步的问题，现在我还没有好的想法，留作以后实现。</p>
<h2>调试模式</h2>
<p>开启调试模式可以将所有被执行的SQL语句显示在Vim的命令输出缓冲区中，在有异常抛出时，也可以显示堆栈信息。由于开启调试模式既不需要修改代码，也不需要重启Vim，这可以极大地方便开发时对UltraBlog.vim的调试，也能使用户提交问题时能反馈更多更详细的信息。</p>
<p>以下三个命令用于控制调试模式的开启状态：</p>
<ul>
<li><code>:UBEnableDebug</code></li>
<li><code>:UBDisableDebug</code></li>
<li><code>:UBToggleDebug</code></li>
</ul>
<h2>其它内容</h2>
<p>本次更新的内容如下：</p>
<ul>
<li>Feature: Add the command <strong>:UBRegexSearch</strong>, doing full-text searches with regular expressions !</li>
<li>Feature: Add the command <strong>:UBReplace</strong>, doing full-text substitutions.</li>
<li>Feature: Add the command <strong>:UBRegexReplace</strong>, doing full-text substitutions with regular expressions.</li>
<li>Feature: Add commands <strong>:UBEnableDebug</strong>, <strong>:UBDisableDebug</strong>, <strong>:UBToggleDebug</strong> and an option <strong>ub_debug</strong>. In debug mode, SQL statements and stack traces of exceptions will be displayed.</li>
<li>Change:  Undo keywords highlighting after executing <strong>:UBList</strong>.</li>
<li>Bugfix:  Exceptions raised when opening the current item under cursor in item lists if the option <strong>ub_hotkey_save_current_item</strong> has not been set. Now this options comes with a default value.</li>
</ul>
<p>关于UB的详细信息在<a href="http://0x3f.org/?p=1894">这里</a>。</p>
<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

