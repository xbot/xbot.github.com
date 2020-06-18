# PHP常量的实现和操作


## 存储结构

常量存储在哈希表EG(zend\_constants)中。

常量的结构定义为：

```c
typedef struct _zend_constant {
	zval value;
	int flags;
	char *name;
	uint name_len;
	int module_number;
} zend_constant;
```

value是常量的值，是一个zval。name是常量名。module\_number是模块被加载时，PHP内核在MINIT和RINIT方法的原型里默认传递的一个值，作为模块清理时的线索，在注册常量的接口里直接传递即可。

flags是常量的标识或标识组合：

  - CONST\_CS
  - CONST\_PERSISTENT
  - CONST\_CT\_SUBST

CONST\_CS表示常量名对大小写敏感，对应PHP函数define()的第三个参数，TRUE、FALSE、NULL这些常量名对大小写是不敏感的。CONST\_PERSISTENT表示常量在请求结束后被保存，只在PHP进程结束时才销毁，一般在MINIT中定义的常量应该指定此参数，RINIT中定义的不指定。CONST\_CT\_SUBST表示在编译时可替换，TRUE、FALSE、NULL、ZEND\_THREAD\_SAFE、ZEND\_DEBUG\_BUILD属于此类。

## 常量的声明

常量的声明方法有两种，简单的使用宏函数族REGISTER\_\*\_CONSTANT()：

>REGISTER\_NULL\_CONSTANT(name, flags)
>REGISTER\_BOOL\_CONSTANT(name, bval, flags)
>REGISTER\_LONG\_CONSTANT(name, lval, flags)
>REGISTER\_DOUBLE\_CONSTANT(name, dval, flags)
>REGISTER\_STRING\_CONSTANT(name, str, flags)
>REGISTER\_STRINGL\_CONSTANT(name, str, len, flags)

由于不需指定常量名长度，所以name参数应直接使用字符串，而不是char\*。

如需使用变量作为name参数，使用zend\_register\_\*\_constant()函数族，并指定变量名长度（sizeof(name)）。上面的宏函数其实是对这族函数的封装。

>void zend\_register\_long\_constant(char \*name, uint name\_len, long lval, int flags, int module\_number TSRMLS\_DC)
>void zend\_register\_double\_constant(char \*name, uint name\_len, double dval, int flags, int module\_number TSRMLS\_DC)
>void zend\_register\_bool\_constant(const char \*name, uint name\_len, zend\_bool bval, int flags, int module\_number TSRMLS\_DC)
>void zend\_register\_string\_constant(char \*name, uint name\_len, char \*strval, int flags, int module\_number TSRMLS\_DC)
>void zend\_register\_stringl\_constant(char \*name, uint name\_len, char \*strval, uint strlen, int flags, int module\_number TSRMLS\_DC)

除此之外，还有REGISTER\_MAIN\_\*\_CONSTANT和REGISTER\_NS\_\*\_CONSTANT两组宏函数。前者用于定义像E\_ERROR这样的PHP标准常量，后者定义有命令空间的常量。

## define()和const

  - define()是函数，在运行时定义常量
    - 不能定义类常量
    - 可以在条件语句中使用
    - 可以指定常量是否对大小写敏感
    - 可以用表达式作为常量值
    - 只定义全局常量，不支持命名空间
  - const是语句，在编译时定义常量
    - 可以定义类常量
    - 不能在条件语句中使用
    - 定义的常量对大小写敏感
    - 不支持表达式作为常量值
    - 若脚本定义了命名空间，声明的常量属于该命名空间

## 魔术常量

>\_\_LINE\_\_  
>\_\_FILE\_\_  
>\_\_DIR\_\_  
>\_\_FUNCTION\_\_  
>\_\_CLASS\_\_  
>\_\_METHOD\_\_  
>\_\_NAMESPACE\_\_  

魔术常量是在编译时（具体地说是词法分析时，见Zend/zend\_language\_scanner.l）被替换，确切地说，这些不是真正意义上的常量，只是个模板占位符。

