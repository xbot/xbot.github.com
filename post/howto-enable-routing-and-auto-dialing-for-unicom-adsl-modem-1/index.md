# 网通ADSL猫开启路由和自动拨号功能的方法之一

北京网通送的无线猫有的是华为HG522，有的是HG527，屏蔽了路由和PPPOE自动拨号功能，须使用超级用户登入才能使用这些功能。

1. 假设网关为默认的192.168.1.1，先使用普通用户登录，然后访问地址：

<blockquote>
http://192.168.1.1/downloadconfigfile.conf
</blockquote>

下载该文件并搜索<strong>username</strong>，找到超级用户的用户名和密码，如：

```xml
<X_ATP_UserInfoInstance InstanceID="1" Username="bjcnchgw" Userpassword="bjcnchgw27852654" Userlevel="2" Busy="0" LoginIP=""/>
```

2. 在如下地址使用超级用户登录：

<blockquote>
http://192.168.1.1/cnc.html
</blockquote>

3. 设置内容如下图：

<a href="http://picasaweb.google.com/lh/photo/MwzSxgWShGMJmYJdSQRw8w?feat=embedwebsite"><img src="http://lh6.ggpht.com/_ceUJ_lBTHzc/TDAy1kazOFI/AAAAAAAABa8/jRmlkSC4MaM/s400/32144754dc04625b375cf1d83f3587fc.media.812x604.png" /></a>

<em>终于不用再忍受公共网络了……</em>

