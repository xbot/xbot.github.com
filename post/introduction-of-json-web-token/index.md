# JSON Web Token简介


## 什么是JWT？

JSON Web Token（简称JWT）是一种token格式，通常用来解决身份认证的问题，和会话（session）作用相同，常用在RESTful API或者OAuth认证上。

和session相比，JWT有以下优势：

* 服务器端的开销低
* 服务器端扩展方便
* 不用专门处理CSRF

由于session的实现方式需要在服务器端存储会话数据，所以当存在大量会话时，服务器端的存储压力会很大，并由此带来扩展上的复杂度（共享session）。而JWT可以把这些数据存储在token里，不需要服务器端存储，优势自然很明显。

对于CSRF，由于session一般使用cookie实现，所以需要专门处理这个安全隐患。JWT借助HTTP请求的header传递，所以伪造成本更高。

### JWT和Bearer Token的关系
JWT在使用的时候要在token前面加上字符串「bearer」再填到HTTP请求的header里，这个值就是「Bearer Token」。为什么要这么做？它和JWT是什么关系？

简单地说，JWT是一种token格式，Bearer Token是一种鉴权方案。

HTTP的header项「Authorization」是在HTTP 1.0引入的，它的值的格式是`类型+token`，支持多种鉴权方案，bearer只是其中的一种。bearer方案中使用的token是JWT格式，这就是它们之间的关系。

## JWT的格式

JWT是一个用英文点号连接起来的、分成三个部分的字符串：header、payload和signature。

### header
header是个base64字符串，解密后是个JSON对象，包含一些元数据。例如：
```json
{
  'typ': 'JWT',
  'alg': 'HS256'
}
```
「typ」是格式，「alg」是加密方法。

### payload
payload也是个base64字符串，解密后也是个JSON对象，一般包含两种数据：JWT标准数据和自定义数据。

JWT标准数据常见的有：
* iss：提供方。
* sub：主题，一般是用户ID。
* exp：过期时间。
* iat：创建时间。
* jti：token的唯一标识。

此外，和session一样，可以把一些自定义的数据存储在payload里。但由于token存储在客户端，所以不应该在这里存储敏感数据。

### signature
signature也是个base64字符串，解密后是个使用header里描述的加密方法针对header和payload加密的字符串。目的是防止这两部分的数据被篡改。

## JWT的使用原则

* 不存放敏感信息
* 保护好私钥
* 结合https使用

都是出于安全角度考虑。token存储在客户端，而且base64可以被解密，所以不能存储敏感数据。如果私钥泄漏，意味著签名可以被随意伪造。使用https可以更好的保护数据，防止中间人攻击。

## JWT相关开发资源

[jwt.io](https://jwt.io/)有各种语言的JWT开发资源。


