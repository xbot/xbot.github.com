# SSL证书生成方法

<p>一般情况下，如果能找到可用的证书，就可以直接使用，只不过会因证书的某些信息不正确或与部署证书的主机不匹配而导致浏览器提示证书无效，但这并不影响使用。</p>

<p>需要手工生成证书的情况有：</p>

<ol>
<li>找不到可用的证书</li>
<li>需要配置双向SSL，但缺少客户端证书</li>
<li>需要对证书作特别的定制</li>
</ol>

<p>首先，无论是在Linux下还是在Windows下的Cygwin中，进行下面的操作前都须确认已安装OpenSSL软件包。</p>

<p>1. 创建根证书密钥文件<strong>root.key</strong>：</p>

```
openssl genrsa -des3 -out root.key
```

<p>输出内容为：</p>

```
[lenin@archer ~]$ openssl genrsa -des3 -out root.key
Generating RSA private key, 512 bit long modulus
.................++++++++++++
..++++++++++++
e is 65537 (0x10001)
Enter pass phrase for root.key:                ← 输入一个新密码
Verifying - Enter pass phrase for root.key:    ← 重新输入一遍密码
```

<p>2. 创建根证书的申请文件<strong>root.req</strong>：</p>

```
openssl req -new -key root.key -out root.req
```

<p>输出内容为：</p>

```
[lenin@archer ~]$ openssl req -new -key root.key -out root.req
Enter pass phrase for root.key:                                              ← 输入前面创建的密码
You are about to be asked to enter information that will be incorporated
into your certificate request.
What you are about to enter is what is called a Distinguished Name or a DN.
There are quite a few fields but you can leave some blank
For some fields there will be a default value,
If you enter '.', the field will be left blank.
-----
Country Name (2 letter code) [AU]:CN                                         ← 国家代号，中国输入CN
State or Province Name (full name) [Some-State]:BeiJing                      ← 省的全名，拼音
Locality Name (eg, city) []:BeiJing                                          ← 市的全名，拼音
Organization Name (eg, company) [Internet Widgits Pty Ltd]:MyCompany Corp.   ← 公司英文名
Organizational Unit Name (eg, section) []:                                   ← 可以不输入
Common Name (eg, YOUR name) []:                                              ← 此时不输入
Email Address []:admin@mycompany.com                                         ← 电子邮箱，可随意填</p>

Please enter the following 'extra' attributes
to be sent with your certificate request
A challenge password []:                                                     ← 可以不输入
An optional company name []:                                                 ← 可以不输入</p>
```

<p>3. 创建一个自当前日期起为期十年的根证书<strong>root.crt</strong>：</p>

```
openssl x509 -req -days 3650 -sha1 -extensions v3_ca -signkey root.key -in root.req -out root.crt
```

<p>输出内容为：</p>

```
[lenin@archer ~]$ openssl x509 -req -days 3650 -sha1 -extensions v3_ca -signkey root.key -in root.req -out root.crt
Signature ok
subject=/C=CN/ST=BeiJing/L=BeiJing/O=MyCompany Corp./emailAddress=admin@mycompany.com
Getting Private key
Enter pass phrase for root.key:    ← 输入前面创建的密码
```

<p>4. 创建服务器证书密钥<strong>server.key</strong>：</p>

```
openssl genrsa -out server.key 2048
```

<p>输出内容为：</p>

```
[lenin@archer ~]$ openssl genrsa -out server.key 2048
Generating RSA private key, 2048 bit long modulus
....+++
..................................................+++
e is 65537 (0x10001)
```

<p>5.创建服务器证书的申请文件<strong>server.req</strong>：</p>

```
openssl req -new -key server.key -out server.req
```

<p>输出内容为：</p>

```
[lenin@archer ~]$ openssl req -new -key server.key -out server.req
You are about to be asked to enter information that will be incorporated
into your certificate request.
What you are about to enter is what is called a Distinguished Name or a DN.
There are quite a few fields but you can leave some blank
For some fields there will be a default value,
If you enter '.', the field will be left blank.
-----
Country Name (2 letter code) [AU]:CN                                          ← 国家名称，中国输入CN
State or Province Name (full name) [Some-State]:BeiJing                       ← 省名，拼音
Locality Name (eg, city) []:BeiJing                                           ← 市名，拼音
Organization Name (eg, company) [Internet Widgits Pty Ltd]:MyCompany Corp.    ← 公司英文名
Organizational Unit Name (eg, section) []:                                    ← 可以不输入
Common Name (eg, YOUR name) []:www.mycompany.com                              ← 服务器主机名，若填写不正确，浏览器会报告证书无效，但并不影响使用
Email Address []:admin@mycompany.com                                          ← 电子邮箱，可随便填

Please enter the following 'extra' attributes
to be sent with your certificate request
A challenge password []:                                                      ← 可以不输入
An optional company name []:                                                  ← 可以不输入
```

<p>6. 创建自当前日期起有效期为期两年的服务器证书<strong>server.crt</strong>：</p>

```
openssl x509 -req -days 730 -sha1 -extensions v3_req -CA root.crt -CAkey root.key -CAserial root.srl -CAcreateserial -in server.req -out server.crt
```

<p>输出内容为：</p>

```
[lenin@archer ~]$ openssl x509 -req -days 730 -sha1 -extensions v3_req -CA root.crt -CAkey root.key -CAserial root.srl -CAcreateserial -in server.req -out server.crt
Signature ok
subject=/C=CN/ST=BeiJing/L=BeiJing/O=MyCompany Corp./CN=www.mycompany.com/emailAddress=admin@mycompany.com
Getting CA Private Key
Enter pass phrase for root.key:    ← 输入前面创建的密码
```

<p>7. 创建客户端证书密钥文件<strong>client.key</strong>：</p>

```
openssl genrsa -des3 -out client.key 2048
```

<p>输出内容为：</p>

```
[lenin@archer ~]$ openssl genrsa -des3 -out client.key 2048
Generating RSA private key, 2048 bit long modulus
.........................................................................................+++
......................................................................................................................+++
e is 65537 (0x10001)
Enter pass phrase for client.key:              ← 输入一个新密码
Verifying - Enter pass phrase for client.key:  ← 重新输入一遍密码
```

<p>8. 创建客户端证书的申请文件<strong>client.req</strong>：</p>

```
openssl req -new -key client.key -out client.req
```

<p>输出内容为：</p>

```
[lenin@archer ~]$ openssl genrsa -des3 -out client.key 2048
Generating RSA private key, 2048 bit long modulus
.........................................................................................+++
......................................................................................................................+++
e is 65537 (0x10001)
Enter pass phrase for client.key:
Verifying - Enter pass phrase for client.key:
[lenin@archer ~]$ openssl req -new -key client.key -out client.req
Enter pass phrase for client.key:                                              ← 输入上一步中创建的密码
You are about to be asked to enter information that will be incorporated
into your certificate request.
What you are about to enter is what is called a Distinguished Name or a DN.
There are quite a few fields but you can leave some blank
For some fields there will be a default value,
If you enter '.', the field will be left blank.
-----
Country Name (2 letter code) [AU]:CN                                           ← 国家名称，中国输入CN
State or Province Name (full name) [Some-State]:BeiJing                        ← 省名称，拼音
Locality Name (eg, city) []:BeiJing                                            ← 市名称，拼音
Organization Name (eg, company) [Internet Widgits Pty Ltd]:MyCompany Corp.     ← 公司英文名
Organizational Unit Name (eg, section) []:                                     ← 可以不填
Common Name (eg, YOUR name) []:Lenin                                           ← 自己的英文名，可以随便填
Email Address []:admin@mycompany.com                                           ← 电子邮箱，可以随便填

Please enter the following 'extra' attributes
to be sent with your certificate request
A challenge password []:                                                       ← 可以不填
An optional company name []:                                                   ← 可以不填
```

<p>9. 创建一个自当前日期起有效期为两年的客户端证书<strong>client.crt</strong>：</p>

```
openssl x509 -req -days 730 -sha1 -extensions v3_req -CA root.crt -CAkey root.key -CAserial root.srl -CAcreateserial -in client.req -out client.crt
```

<p>输出内容为：</p>

```
[lenin@archer ~]$ openssl x509 -req -days 730 -sha1 -extensions v3_req -CA root.crt -CAkey root.key -CAserial root.srl -CAcreateserial -in client.req -out client.crt
Signature ok
subject=/C=CN/ST=BeiJing/L=BeiJing/O=MyCompany Corp./CN=www.mycompany.com/emailAddress=admin@mycompany.com
Getting CA Private Key
Enter pass phrase for root.key:          ← 输入上面创建的密码
```

<p>10. 将客户端证书文件<strong>client.crt</strong>和客户端证书密钥文件<strong>client.key</strong>合并成客户端证书安装包<strong>client.pfx</strong>：</p>

```
openssl pkcs12 -export -in client.crt -inkey client.key -out client.pfx
```

<p>输出内容为：</p>

```
[lenin@archer ~]$ openssl pkcs12 -export -in client.crt -inkey client.key -out client.pfx
Enter pass phrase for client.key:        ← 输入上面创建的密码
Enter Export Password:                   ← 输入一个新的密码，用作客户端证书的保护密码，在客户端安装证书时需要输入此密码
Verifying - Enter Export Password:       ← 确认密码
```

<p>11. 保存生成的文件备用，其中<strong>server.crt</strong>和<strong>server.key</strong>是配置单向SSL时需要使用的证书文件，<strong>client.crt</strong>是配置双向SSL时需要使用的证书文件，<strong>client.pfx</strong>是配置双向SSL时需要客户端安装的证书文件</p>

