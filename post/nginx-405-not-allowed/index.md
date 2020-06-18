# NGINX: 405 Not Allowed

<p>NGINX不允许向静态文件提交POST方式的请求，否则报405错误。测试方法为，使用curl向服务器上的静态文件提交POST请求：</p>

```bash
curl -d 1=1 http://localhost/version.txt
```

得到以下结果：

```html
<html>
<head><title>405 Not Allowed</title></head>
<body bgcolor="white">
<center><h1>405 Not Allowed</h1></center>
<hr><center>nginx/1.0.11</center>
</body>
</html>
```

<p>网上传抄的添加以下配置的解决办法不可用：</p>

```nginx
error_page 405 =200 @405;
location @405
{
    root /srv/http;
}
```

<p>一种不完美但可用的方法为：</p>

```nginx
upstream static_backend {
    server localhost:80;
}

server {
    listen 80;

    # ...

    error_page 405 =200 @405;
    location @405 {
        root /srv/http;
        proxy_method GET;
        proxy_pass http://static_backend;
    }
}
```

<p>即转换静态文件接收的POST请求到GET方式。</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

