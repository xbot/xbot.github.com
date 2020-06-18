# 加速Homebrew


两个方法：走代理和使用国内镜像。镜像有同步时间差，而且遇到国外资源还是慢。

homebrew用curl下载，用proxychains和环境变量http_proxy都没用，需要在`~/.curlrc`里配置：

```
socks5 = "127.0.0.1:1080"
```

