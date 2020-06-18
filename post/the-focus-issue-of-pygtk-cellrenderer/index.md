# 关于PyGTK.CellRenderer的回调方法中聚焦异常的问题

<p>遇到这样一个问题：</p>

<p>在pygtk.TreeView中，需要在一个Cell的值被修改后做一个校验，如果不合法，则重新聚焦该Cell并选中其中的内容。</p>

<p>示意代码如下：</p>

```python
# pygtk.CellRenderer的edited事件的回调方法
def onCellEdited(self, cell, path, newText, userData):
    store,colNum = userData
    # 使用事件驱动的设计理念，创建一个自定义的事件
    evt = ServerTypeChangedEvent(self, cell, usrData)

    # 事件入队列
    EventQueue.fireEvent(evt)
    # 处理事件队列，如果截获异常，提示并重新聚焦Cell
    try:
        EventQueue.processEvents()
    except Exception,e:
        self.alert(e.message)
        col = self.treeView.get_column(colNum-1)
        # 聚焦Cell，并选中Cell中的内容
        self.treeView.set_cursor_on_cell(path, col, cell, True)
```

<p>关键的代码在最后一行，通过调用TreeView的set_cursor_on_cell方法聚焦Cell并选中其内容。但就是这一步出了问题，报如下警告：</p>

<blockquote>
  <p>GtkWarning: _gtk_tree_view_column_start_editing: assertion `tree_column->editable_widget == NULL' failed</p>
</blockquote>

<p>此后整个TreeView的行为表现不正常，表现为可直接修改其它Cell的内容，且原Cell一直处于聚焦状态。</p>

<p>此问题的原因是：edited事件在Cell的输入控件被销毁前就发出了，这时在事件的回调方法中重新聚焦该Cell就导致了这个问题（<em>详见<a href="http://www.gtkforums.com/viewtopic.php?t=4619">这里</a></em>）。</p>

<p>解决方法是借助glib.idle_add函数，在PyGTK空闲的时候再调用set_cursor_on_cell方法，由于此时原控件已被销毁，所以不会有问题。</p>

<p>修改后的代码如下：</p>

```python
def onCellEdited(self, cell, path, newText, userData):
    store,colNum = userData
    evt = ServerTypeChangedEvent(self, cell, usrData)

    EventQueue.fireEvent(evt)
    try:
        EventQueue.processEvents()
    except Exception,e:
        self.alert(e.message)
        col = self.treeView.get_column(colNum-1)
        #self.treeView.set_cursor_on_cell(path, col, cell, True)
        import glib
        glib.idle_add(self.treeView.set_cursor_on_cell, path, col, cell, True)
```

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

