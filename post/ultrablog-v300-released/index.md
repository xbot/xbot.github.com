# UltraBlog.vim v3.0释出：全文检索与事件驱动

<p>用了一周的业余时间，昨天我释出了<a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>的3.0版。新版本主要加入了全文检索的功能，并引入事件驱动的模式。</p>

<p>全文检索是我蓄谋已久的功能，一个不能搜索的博客客户端的管理功能是大打折扣的。得益于SQLite数据库和SQLAlchemy框架，全文检索的实现是很简单的，新增加的<code>:UBFind</code>命令将在所有文章和页面的标题与内容中查询，并将搜索结果显示在一个可分页的列表中。这个命令支持任意多个关键词，各关键词之间遵循与的关系。此外，检索结果中，所有的关键词将被自动标记为高亮。</p>

<p>事件驱动模式的引入是我悍然将版本号跳跃到3.x的主要原因。</p>

<p>在前几个版本中，我实现了对多窗口的支持，它使得用户可以在新窗口中打开列表中的文章。但UltraBlog.vim最初开发的时候并没有考虑到多窗口的问题，所有命令都只针对当前窗口进行操作。这就有了缓冲区内容同步的问题，假如在一个新的缓冲区中打开了和另一个缓冲区相同的文章，则一个缓冲区内容的改变不会同步到另外一个中，这多少是有些隐患的。</p>

<p>最直接的做法是在所有可能改变缓冲区内容的功能中加入对其它缓冲区的处理，但这样做有不少问题：</p>

<ul>
<li>代码耦合度太高，违反K.I.S.S原则，不利于今后的开发和维护</li>
<li>函数体过长，我讨厌难看的代码</li>
<li>代码冗余，重复劳动</li>
</ul>

<p>事件驱动模式可以很好的解决以上问题，一个函数只干一件事，做完后一个事件抛出去，至于连带著要做什么，谁监听这个事件谁去处理，代码的耦合度很低，复用度很高，易于维护和阅读。</p>

<p>UltraBlog.vim引入事件驱动模式处理缓冲区同步的问题。不同的操作抛出不同的事件，所有的事件继承自父类<strong>UBEvent</strong>：</p>

```python
class UBEvent:
    def __init__(self, srcObj):
        self.srcObj = srcObj

class UBDebugEvent(UBEvent): pass
class UBTmplDelEvent(UBEvent): pass
class UBTmplSaveEvent(UBEvent): pass
class UBLocalPostDelEvent(UBEvent): pass
class UBRemotePostDelEvent(UBEvent): pass
class UBPostSendEvent(UBEvent): pass
class UBPostSaveEvent(UBEvent): pass
```

<p>一个可能改变缓冲区内容的操作执行完后，创建一个特定的事件并将其加入到事件队列中。事件队列类中存放两个列表，一是事件队列，二是事件监听器列表；提供三个方法，分别用来注册事件监听器、对事件执行入队列操作和处理队列中所有事件：</p>

```python
class UBEventQueue:
    queue = []
    listeners = []

    @classmethod
    def fireEvent(cls, evt):
        cls.queue.append(evt)

    @classmethod
    def processEvents(cls):
        for evt in cls.queue:
            for listener in cls.listeners:
                if listener.isTarget(evt):
                    cls.queue.remove(evt)
                    listener.processEvent(evt)

    @classmethod
    def registerListener(cls, lsnr):
        cls.listeners.append(lsnr)
```

<p>事件队列对事件的处理是通过事件监听器进行的，每个事件监听器要实现两个功能：识别监听对象和处理监听对象。所有具体事件的监听类都是<strong>UBListener</strong>的子类：</p>

```python
class UBListener():
    ''' Parent class of all listeners
    '''
    eventType = None

    @classmethod
    def isTarget(cls, evt):
        return isinstance(evt, cls.eventType)

    @staticmethod
    def processEvent(evt): pass

class UBPostSaveListener(UBListener):
    ''' Listener for saving posts/pages
    1. Refresh the current view if it is an edit/list view of this post
    2. Mark all edit/list views of posts/pages outdated
    '''
    eventType = UBPostSaveEvent

    @staticmethod
    def processEvent(evt):
        for nr in ub_get_buffers(['post_edit','page_edit']):
            if evt.srcObj==ub_get_meta('id', nr):
                if nr==ub_get_bufnr('%'):
                    ub_refresh_current_view()
                else:
                    ub_set_view_outdated(nr)

        for nr in ub_get_buffers(['post_list','page_list','search_result_list']):
            if nr == ub_get_bufnr('%'):
                ub_refresh_current_view()
            else:
                ub_set_view_outdated(nr)
```

<p>事件监听器遍历所有满足处理条件的缓冲区，对当前缓冲区，立即刷新，其余的标记为已过期。同时利用Vim自身的事件驱动特性，在进入已过期的缓冲区时，再更新之，也就是传说中的懒加载模式：</p>

```vim
au BufEnter * py __ub_on_buffer_enter()
```

```python
@__ub_exception_handler
def __ub_on_buffer_enter():
    ''' Triggered by BufEnter event, check if the buffer is outdated
    '''
    if ub_is_view_outdated('%'):
        ub_refresh_current_view()
        ub_set_view_outdated('%', False)
```

<p>此外，随著代码量的增加，原来把全部代码都放在一个文件中的做法不再适用，尤其是以<a href="http://en.wikipedia.org/wiki/Here_document">Here Document</a>形式存放在vim脚本文件中的Python代码不能被ctags识别，所以按类别分别存放到<strong>$VIMRUNTIME/plugin/ultrablog/</strong>下的几个Python源文件中。</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

