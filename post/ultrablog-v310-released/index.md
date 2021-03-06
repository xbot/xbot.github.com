# UltraBlog.vim v3.1释出：代码重构与MacVim支持

<p>距离上一个版本的释出已经有三个月了，这次新版并没有增加多少功能性的内容，主要是代码的重构和对在MacVim下稳定性的改进。</p>

<p>对依赖关系检查的不细致导致了一些麻烦，抛出的错误信息有时与实际原因相距甚远。由于开始时是以实现功能为主，所以都是直接用函数实现的，之后修修补补，代码冗余而且维护麻烦。于是著手重构主要功能的源码，面向对象，把所有检查逻辑放到父类中去实现，子类只关注自身逻辑，最后新版减少了二百多行的代码量。</p>

<p>在重构的过程中偶然发现，在MacVim中，程序抛出任何异常，即便是有捕获逻辑，也会导致编辑器崩溃。在<a href="http://stackoverflow.com/questions/5574702/how-to-print-to-stderr-in-python">这里</a>简略地提到：</p>

<blockquote>
  <p>the failure was actual a crash, through some strange combination of threading, matplotlib pyplot backend, and ssh X11 forwarding, sys.stderr had somehow been assigned to something which python complained wasn't a file-like object</p>
</blockquote>

<p>于是将所有标准输出与标准错误输出语句换成另一种形式：</p>

```python
# 原形式
sys.stdout.write('xxx')
sys.stderr.write('xxx')

# 新形式
print >> sys.stdout, 'xxx'
print >> sys.stderr, 'xxx'
```

<p>以上是新版主要变更的内容，此外，也顺便解决了开发过程中发现的个别Bug和做了其它一些改动：</p>

<ul>
<li>Change:  The key "xmlrpc" of the settings list is dropped, a new one with the name "url" is added, you should set its value to the blog url.</li>
<li>Change:  Source code refactorings.</li>
<li>Change:  Solve the crash problem existing in MacVim only.</li>
<li>Change:  <strong>:UBConv</strong> can be used in any buffer.</li>
<li>Change:  <strong>:UBThis</strong> now has a third parameter, which enables convertions between syntaxes.</li>
<li>Bugfix:  The second parameter of <strong>:UBThis</strong> cannot take effect.</li>
</ul>

<p>这也是将源码从Google Code迁移到GitHub后发布的第一个新版本，更详细的信息请移步<a href="http://0x3f.org/?p=1894">插件主页</a>或GitHub上的<a href="https://github.com/xbot/UltraBlog.vim">代码仓库</a>。</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

