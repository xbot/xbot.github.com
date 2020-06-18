# 用Charles拦截iOS APP HTTPS请求

## 实践环境
- Charles 4.2
- iOS 11.2.1

## 安装证书
按照Charles菜单（Help -> SSL Proxying -> Install Charles Root Certificate on a Mobile Device or Remote Browser）的指示，设置iOS的系统代理。

为了以后使用方便，可以在代理工具（如ShadowRocket）里设置，注意使用时应配置代理工具对全部流量使用代理，如果使用PAC之类动态代理可能会拦截不到。

在iOS的Safari中访问上面提示中的网址，会弹出提示安装证书。iOS 10.3以后还要设置信任该证书（设置->通用->关于本机->证书信任设置）。

## 配置Charles拦截HTTPS请求
路径为：Proxy -> SSL Proxying Settings

可以设置对特定的主机名和端口拦截，也可以直接保存，此时对所有请求均会拦截。

## 拦截
iOS中打开上述代理，APP中发送的请求就会在Charles里列出来。


