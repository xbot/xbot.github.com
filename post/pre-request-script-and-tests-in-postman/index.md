# Postman的Pre-request Script和Tests

Pre-request Script和Tests都是Javascript代码块。前者在请求发起前执行，通常用来生成请求数据，例如对用户注册接口随机生成用户名。后者在请求结束后执行，通常用来测试和处理返回值。

以下是随机生成用户名的Pre-request Script：
```javascript
postman.setEnvironmentVariable("random_username", "测试用户" + Math.floor(Math.random()*1000000));
```

产生的值存储在环境变量里，表单里直接引用环境变量即可。

对于REST接口普遍使用的JWT，可以在登录接口的Tests里直接把返回的token更新到环境变量里，这样其它接口直接引用这个环境变量即可：
```javascript
var data = JSON.parse(responseBody);
tests["token is returned"] = typeof(data.token) == "string" && data.token.length > 0

postman.setEnvironmentVariable("token", data.token);
```


