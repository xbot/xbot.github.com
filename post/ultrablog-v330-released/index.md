# UltraBlog.vim v3.3.0：I18N、超时时间和其它

<p>UB一岁了，这算是个周年纪念版吧。</p>

<p>在这一年里，因为各种原因，博客写得没有以前多了，但每篇都是用这个插件写的，我觉得很好用。断断续续地做了一些修改，因为一切都是一个人在做，所以大的变化不多。这次这个版本里有些改动还是要感谢<a href="http://ihacklog.com/">荒野无灯</a>童鞋，他的<a href="https://github.com/xbot/UltraBlog.vim/issues/3">建议</a>很大程度上催生了这个新版本：比如加入保存命令的热键映射选项，这样可以使用保存普通文件的热键保存UB中的内容，我自己以前也经常习惯性地用错热键；还有socket超时时间的选项，最近我这里也经常性地在UB操作博客时报超时的异常，荒童鞋关于自定义socket超时时间的建议一语惊醒了梦中人。</p>

<p>此外，似乎与近期Vim的一次升级<a href="http://comments.gmane.org/gmane.editors.vim.devel/34092">有关</a>，在Vim中调用Python接口打印任何内容到标准IO都会导致Vim崩溃，所以这次把所有的输出都改成了调用Vim的输出命令来做。</p>

<p>本次修改的内容中，还有一项比较重要的内容就是实现了国际化，目前只提供英文和简体中文两种语言，由于Vimball不能处理二进制文件，所以从此以后UB改用zip格式压缩包打包。</p>

<p>以下是本次更新的详细内容：</p>

<ul>
<li>Feature: Add i18n support !</li>
<li>Feature: Add a new option <strong>ub_hotkey_save_current_item</strong>, users can define their own hotkey for <strong>:UBSave</strong>.</li>
<li>Feature: Add a new option <strong>ub_socket_timeout</strong>, users can customize the timeout period in seconds, useful for slow networks.</li>
<li>Bugfix:  Exception raised when one event is processed by more than one event handlers.</li>
<li>Change:  Echoing messages now uses the command :echoerr instead of python's sys.stderr, because Vim crashes on this due to an upgrade recently.</li>
<li>Change:  Change commands <strong>:UBSave</strong>, <strong>:UBSend</strong>, <strong>:UBUpload</strong>, <strong>:UBConv</strong>, <strong>:UBPreview</strong> to be available only in their effective views.</li>
<li>Change:  Lists are set nowrapped.</li>
<li>Bugfix:  Stop complaining '<strong>_pop from empty list_</strong>' while doing almost everything.</li>
</ul>

<p>关于UB的详细信息在<a href="http://0x3f.org/?p=1894">这里</a>。</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

