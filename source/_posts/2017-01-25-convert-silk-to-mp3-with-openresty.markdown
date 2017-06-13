---
layout: post
title: "用OpenResty自動轉換silk到MP3"
date: 2017-01-25 18:59
comments: true
categories: 計算機
tags:
- openresty
- nginx
- 編程
- lua
---

PHP沒有原生的轉換silk格式音頻到mp3的方案，所以考慮用NGINX+Lua調用命令行工具實現。

## 思路
當訪問以「.silk.mp3」為後綴的文件時，查找對應的mp3文件，存在則返回，否則，如果對應的silk文件存在，調用命令行工具轉換文件格式，然後內部重定向到新生成的mp3。

## 安裝
* [OpenResty](https://openresty.org/cn/download.html)
* [silk-v3-decoder](https://github.com/kn007/silk-v3-decoder)
* [ffmpeg](https://ffmpeg.org)

## NGINX配置
```nginx
location ~* /.*\.silk\.mp3$ {
    content_by_lua_file "/opt/script/silk2mp3.lua";
}
```

## Lua腳本
```lua
function file_exists(name)
    local f=io.open(name,"r")
    if f~=nil then io.close(f) return true else return false end
end

local resource     = ngx.var.request_filename;
local realResource = resource:sub(1, #resource - 9)..".mp3";
local realURI      = ngx.var.uri:sub(1, #ngx.var.uri - 9)..".mp3";

if file_exists(realResource) then
    return ngx.exec(realURI);
end

local silk = resource:sub(1, #resource - 4);

if not file_exists(silk) then
    return ngx.exit(404);
end

os.execute("/bin/sh /opt/silk-v3-decoder/converter.sh \""..silk.."\" mp3 > /dev/null 2>&1");

if file_exists(realResource) then
    return ngx.exec(realURI);
else
    return ngx.exit(404)
end
```

## 遇到的問題
### OpenResty調用命令轉換失敗，而手工可以

silk-v3-decoder的converter.sh把所有錯誤信息都屏蔽了，需要修改腳本查看。

實際情況是OpenResty的運行用戶沒有音頻文件所在目錄的權限。

### 編譯的ffmpeg轉換PCM到MP3時報錯

silk-v3-decoder自己的decoder只是把silk轉換成PCM，然後調用ffmpeg轉換成mp3。系統中的ffmpeg是自己編譯的，在這一步報錯，大意是mp3的encoder不存在。

原因是ffmpeg編譯時默認不激活mp3編碼器（猜測）或者沒安裝libmp3lame-dev，需要安裝這個開發庫並給configure指定參數「--enable-libmp3lame」。
