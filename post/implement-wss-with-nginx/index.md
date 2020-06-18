# 用NGINX实现WSS


实现基于SSL的安全的WebSocket。

## NGINX配置

```
server {
    listen 4431;
    server_name ws.sample.com;

    ssl on;
    ssl_certificate ssl/server.crt;
    ssl_certificate_key ssl/server.key;
    ssl_session_timeout 5m;
    ssl_session_cache shared:SSL:50m;
    ssl_protocols SSLv3 SSLv2 TLSv1 TLSv1.1 TLSv1.2;
    ssl_ciphers ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv2:+EXP;

    location /
    {
        proxy_pass http://127.0.0.1:4759;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header X-Real-IP $remote_addr;
        proxy_read_timeout 60s;
    }
}
```

## 常见问题

### 连不上或连接时报错

> WebSocket network error: OSStatus Error -9807: Invalid certificate chain

如果使用的是自签证书或者证书针对的域名和连接所指定的不符，会出这种问题。

### wss一分钟自动断开

NGINX里设置proxy_read_timeout或者程序实现心跳。

