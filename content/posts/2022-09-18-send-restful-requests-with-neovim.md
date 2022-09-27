---
title: "用 Neovim 调试 RESTful 接口"
slug: "Send RESTful requests with Neovim"
date: 2022-09-18T11:31:16+08:00
categories: ["计算机"]
tags:
- Neovim
- Vim
toc: false
---

纯文本有很多好处，比如可以用 Git 管理，方便备份和追溯，方便多机同步和分享。

之前使用 VS Code 的插件 Rest Client ，从浏览器后台复制请求数据过来就可以用，很方便。但是存在一些用起来不舒服的地方，比如：

- 不方便对接口分组管理。
- 不方便快速查找接口。

这些在对纯文本操作效率更高的 Vim 里都可以解决，但是之前尝试过的一些 Vim 的 RESTful 客户端插件还不成熟，最近发现 [rest.nvim](https://github.com/rest-nvim/rest.nvim) 基本可以满足日常使用需要了。

rest.nvim 是个 Neovim 的插件，和 Rest Client 支持的格式差异较小，迁移很方便。这样我就可以用 Vim 的折叠特性对接口分组管理，用插件 Leaderf 的模糊查找特性快速定位接口，当然，对纯文本的任何编辑和操作在 Vim 里就是浑然天成、行云流水的。

它的大致用法如下：

在工作目录下创建环境变量文件 `.env` ：

```ini
base_url=http://myapp.dev
header_accept_json=application/json, application/problem+json, text/plain, */*
header_content_type_json=application/json;charset=utf-8
header_cookie_debug_session=XDEBUG_SESSION=1;app_session=1ObUjvLvEYjVhJ8tbzn5BorN7TViNtI1S625140e
user_email=user@test.com
user_password=password
```

在工作目录下创建请求文件（例如 `myapp.http` ）：

```http
### Login
POST {{base_url}}/v1/sessions
Accept: {{header_accept_json}}
Content-Type: {{header_content_type_json}}
Cookie: {{header_cookie_debug_session}}

{"email": "{{user_email}}", "password": "{{user_password}}"}

### Get an article
GET {{base_url}}/v1/articles/DiJeb7IQHo8FOFkXulieyA
Accept: {{header_accept_json}}
Cookie: {{header_cookie_debug_session}}

### Create an article
POST {{base_url}}/v1/articles
Accept: {{header_accept_json}}
Cookie: {{header_cookie_debug_session}}
Content-Type: {{header_content_type_json}}

{
    "title": "Hello world",
    "Content": "This is a dummy post."
}
```

然后就可以用插件提供的命令触发请求了。

不过这个插件目前还不能保持会话，所以每次请求完登录接口都要手动把会话信息复制到环境变量中，比较麻烦。这里我用 Vim 自身的机制来解决：

{{< gist xbot ec76bf726f64f285f1fe1ccdc76f0668 >}}

这样，每次请求完登录接口就会自动把 cookie 写入 `.env` 文件中了。

---
## 2022-09-27 更新

关于会话的保持，有种更简单的实现方式。

rest.nvim 支持在请求中使用 curl 命令的参数，所以上述请求文件可以写成如下形式：

```http
### Login
POST {{base_url}}/v1/sessions
Accept: {{header_accept_json}}
Content-Type: {{header_content_type_json}}

-c cookies

{"email": "{{user_email}}", "password": "{{user_password}}"}

### Get an article
GET {{base_url}}/v1/articles/DiJeb7IQHo8FOFkXulieyA
Accept: {{header_accept_json}}

-b cookies

### Create an article
POST {{base_url}}/v1/articles
Accept: {{header_accept_json}}
Content-Type: {{header_content_type_json}}

-b cookies

{
    "title": "Hello world",
    "Content": "This is a dummy post."
}
```

通过 `-c` 参数将响应中的 cookie 自动存储到名为 `cookies` 的文件中，并通过 `-b` 参数指定发送请求时使用该文件中的 cookie 。不过，当前版本存在一个小问题，生成的文件名前会带一个空格，不影响正常使用，但如果要加入 `.gitignore` 中的话需要注意这点。

之前的实现方法对于其它鉴权方式仍有意义。