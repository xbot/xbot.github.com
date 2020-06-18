# 用OpenResty自动转换silk到MP3


PHP没有原生的转换silk格式音频到mp3的方案，所以考虑用NGINX+Lua调用命令行工具实现。

## 思路
当访问以「.silk.mp3」为后缀的文件时，查找对应的mp3文件，存在则返回，否则，如果对应的silk文件存在，调用命令行工具转换文件格式，然后内部重定向到新生成的mp3。

## 安装
* [OpenResty](https://openresty.org/cn/download.html)
* [silk-v3-decoder](https://github.com/kn007/silk-v3-decoder)
* [ffmpeg](https://ffmpeg.org)

## NGINX配置
```nginx
location ~* /.*\.silk\.mp3$ {
    content_by_lua_file "/opt/script/silk2mp3.lua";
}
```

## Lua脚本
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

## 遇到的问题
### OpenResty调用命令转换失败，而手工可以

silk-v3-decoder的converter.sh把所有错误信息都屏蔽了，需要修改脚本查看。

实际情况是OpenResty的运行用户没有音频文件所在目录的权限。

### 编译的ffmpeg转换PCM到MP3时报错

silk-v3-decoder自己的decoder只是把silk转换成PCM，然后调用ffmpeg转换成mp3。系统中的ffmpeg是自己编译的，在这一步报错，大意是mp3的encoder不存在。

原因是ffmpeg编译时默认不激活mp3编码器（猜测）或者没安装libmp3lame-dev，需要安装这个开发库并给configure指定参数「--enable-libmp3lame」。

