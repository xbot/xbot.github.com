# 搭建自己的Twitter API

<h2> 创建Twitter应用程序 </h2>

从2010年9月起，twitter只支持第三方应用程序使用oauth方式登录。要使用第三方API程序，就必须先在twitter中创建应用程序。

申请地址如下：

https://twitter.com/apps/

填写信息时，<b>Application Type</b>应该选<b>Browser</b>，<b>Default Access type</b>应选<b>Read & Write</b>，<b>User Twitter for login</b>不选，其它随便。

创建成功后，该应用程序的<b>Consumer key</b>和<b>Consumer secret</b>在部署API时一般都要用到。

<h2> 第三方API程序 </h2>

<h3> gtap </h3>

<a href="http://code.google.com/p/gtap/">gtap</a>使用python开发，可部署在appspot上。

部署前，先修改<b>app.yaml</b>，填写在appspot上创建的应用程序的ID，并使用符号#注释掉<b>secure: always</b>，因为GFW屏蔽了appspot的https。

然后修改<b>main.py</b>，填写Consumer key和Consumer secret。

最后安装<a href="http://code.google.com/intl/zh-CN/appengine/downloads.html">App Engine SDK</a>，使用其中的<b>appcfg.py</b>上传gtap文件夹：

```bash
appcfg.py update gtap
```

在浏览器中（此时应使用可以翻墙的代理）访问在appspot上创建的应用程序的URL地址（如：http://xxx.appspot.com ），在打开的页面中点击<b>Sign in with Twitter</b>，然后在跳转到的页面中点击<b>Allow</b>按钮。在跳转到的页面上修改API的密码（据说有些客户端要求API的密码与Twitter的密码一致）。

在客户端中使用API的地址如下：

<blockquote>
http://xxx.appspot.com/
</blockquote>

其中，<b>xxx</b>是appspot应用程序的ID，末尾的斜杠必不可少。

<h3> twip </h3>

<a href="http://code.google.com/p/twip/">twip</a>使用php开发，应部署在支持PHP的服务器上。

首先将<b>config-example.php</b>改名为<b>config.php</b>，然后修改其中的OAUTH_KEY/OAUTH_SECRET/BASE_URL三项内容，前两项分别是Consumer key和Consumer secret，base_url应该是twip部署到服务器上后的URL地址（如http://myblog.com/twip/ ，最后的斜杠必不可少）。

然后在浏览器中访问上面的base_url，选择使用o模式，API地址会在最后的页面中输出。

<h2> 补充说明 </h2>

<ol>
<li>当前版本的Google App Engine SDK要求使用python 2.5，但如果只是用来上传应用程序到appspot，2.5以上版本的python也可以用，只是在上传时会报某些模块已不推荐使用。如果使用appcfg.py时报找不到2.5版本的python，可以强制使用已安装的新版本的python执行之，即：python appcfg.py update gtap</li>
</ol>

