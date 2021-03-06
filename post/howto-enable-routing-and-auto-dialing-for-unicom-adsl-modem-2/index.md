# 网通ADSL猫开启路由和自动拨号功能的方法之二

之前提到过对华为HG522、HG527的超级用户的<a href="http://0x3f.org/?p=1528">破解方法</a>。网通附送的另一种猫，是<strong>中兴ZXV10 H108B</strong>无线猫，体积更小，而且天线是内置的。

上次的方法不能用在这个型号的猫上，但可以用<strong>ftp</strong>的方式破解。以下步骤基于Linux，并只在硬件版本为<strong>V1.1.02</strong>、软件版本为<strong>V1.1.02T18_N</strong>的H108B上测试通过：

1. 使用<strong>普通用户</strong>进入猫的设置页面并开启FTP服务，将用户名和密码均设为<strong>admin</strong>

2. 连接ftp服务器：

<blockquote>
ftp 192.168.1.1
</blockquote>

输入用户名和密码，成功登录FTP服务器：

<blockquote>
[lenin@archer ~]$ ftp 192.168.1.1
Connected to 192.168.1.1.
220 Welcome to virtual FTP service.
Name (192.168.1.1:lenin): admin
331 Please specify the password.
Password:
230 Login successful.
Remote system type is UNIX.
Using binary mode to transfer files.
ftp> 
</blockquote>

3. 切换到<strong>/etc</strong>目录并下载配置文件<strong>board.conf</strong>：

<blockquote>
cd ../
cd etc
get board.conf
</blockquote>

如下所示：

<blockquote>
ftp> cd ../
250 Directory successfully changed.
ftp> cd etc
250 Directory successfully changed.
ftp> get board.conf
200 PORT command successful. Consider using PASV.
150 Opening BINARY mode data connection for board.conf (39516 bytes).
226 File send OK.
39516 bytes received in 0.0496 seconds (796003 bytes/s)
ftp> 
</blockquote>

4. 在下载到本地的board.conf中查找<strong>right</strong>，后跟<strong>0</strong>表示超级用户，若为<strong>1</strong>则表示普通用户，如下所示：

```xml
<row id="0">
    <item>"right", "0"</item>
    <item>"username", "bjcnchgw"</item>
    <item>"enable", "1"</item>
    <item>"password", "bjcnchgw72915767"</item>
</row>
<row id="1">
    <item>"right", "1"</item>
    <item>"username", "user"</item>
    <item>"password", "mypasswd"</item>
</row>
```

5. 在如下页面使用超级用户登录：

<blockquote>
http://192.168.1.1/cnc.html
</blockquote>

6. 进入“<strong>网络</strong>”→“<strong>宽带设置</strong>”页面，删除所有“<strong>连接名称</strong>”列出的连接（<em>其中，TR069是网通远程控制猫并修改超级用户密码的连接；INTERNET被设置成了桥接，所以不能实现路由功能；另外两个应该是用来屏蔽第三、四个LAN口的。所以要全部删除。</em>），并创建新的连接：

<a href="http://picasaweb.google.com/lh/photo/G5p8or5AvVumvFOr89XLqQ?feat=embedwebsite"><img src="http://lh5.ggpht.com/_ceUJ_lBTHzc/TEHd0wXELFI/AAAAAAAABbs/gtxn0tKGyJM/s400/2010-07-17.19%3A48%3A30.%E6%88%AA%E5%8F%96%E9%80%89%E5%8C%BA.01.png" /></a>

7. 进入“<strong>网络</strong>”→“<strong>远程管理</strong>”页面，取消“<strong>周期上报功能</strong>”。

8. 由于“<strong>用户管理</strong>”页面不提供超级用户改密码的功能，所以此时应下载最新的board.conf，修改里面的超级用户的密码，再上传到/etc目录，假设已登入FTP并切换到/etc目录下：

<blockquote>
ftp> put board.conf
200 PORT command successful. Consider using PASV.
150 Ok to send data.
226 File receive OK.
39516 bytes sent in 0.0195 seconds (2026565 bytes/s)
ftp> 
</blockquote>

9. 最后重启猫即可。

据说有使用TTL转USB线登入H108B的嵌入式Linux操作系统来破解超级用户的，但是我的猫的集成电路板上居然没有焊TTL引脚，显然H108B不只一个硬件版本，TTL线白买了。

<a href="http://picasaweb.google.com/lh/photo/ceYq8gDnT3qY6IgBQuyjFg?feat=embedwebsite"><img src="http://lh6.ggpht.com/_ceUJ_lBTHzc/TEHb1yqtElI/AAAAAAAABbY/KKlqPgcBMWo/s400/C360_2010-07-17%2011-07-10.jpg" /></a>

<a href="http://picasaweb.google.com/lh/photo/omFbqTXkvVV4L86vGqyyng?feat=embedwebsite"><img src="http://lh3.ggpht.com/_ceUJ_lBTHzc/TEHb19lGCzI/AAAAAAAABbc/7zaShWAUvUM/s400/C360_2010-07-17%2011-06-21.jpg" /></a>

<a href="http://picasaweb.google.com/lh/photo/JfG_3EgD8BDChZVBM6xx9Q?feat=embedwebsite"><img src="http://lh6.ggpht.com/_ceUJ_lBTHzc/TEHb2HXkwhI/AAAAAAAABbg/HazHHwsfXHk/s400/C360_2010-07-17%2011-06-09.jpg" /></a>

<em>以上照片出自G7+Camera360</em>

