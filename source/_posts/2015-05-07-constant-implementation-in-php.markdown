---
layout: post
title: "PHP常量的實現和操作"
date: 2015-05-07 16:29
comments: true
categories: 計算機
tags:
- PHP
- 源碼
---

## 存儲結構

常量存儲在哈希表EG(zend\_constants)中。

常量的結構定義為：

{% codeblock lang:c %}
typedef struct _zend_constant {
	zval value;
	int flags;
	char *name;
	uint name_len;
	int module_number;
} zend_constant;
{% endcodeblock %}

value是常量的值，是一個zval。name是常量名。module\_number是模塊被加載時，PHP內核在MINIT和RINIT方法的原型裡默認傳遞的一個值，作為模塊清理時的線索，在註冊常量的接口裡直接傳遞即可。

flags是常量的標識或標識組合：

  - CONST\_CS
  - CONST\_PERSISTENT
  - CONST\_CT\_SUBST

CONST\_CS表示常量名對大小寫敏感，對應PHP函數define()的第三個參數，TRUE、FALSE、NULL這些常量名對大小寫是不敏感的。CONST\_PERSISTENT表示常量在請求結束後被保存，只在PHP進程結束時才銷毀，一般在MINIT中定義的常量應該指定此參數，RINIT中定義的不指定。CONST\_CT\_SUBST表示在編譯時可替換，TRUE、FALSE、NULL、ZEND\_THREAD\_SAFE、ZEND\_DEBUG\_BUILD屬於此類。

## 常量的聲明

常量的聲明方法有兩種，簡單的使用宏函數族REGISTER\_\*\_CONSTANT()：

>REGISTER\_NULL\_CONSTANT(name, flags)
>REGISTER\_BOOL\_CONSTANT(name, bval, flags)
>REGISTER\_LONG\_CONSTANT(name, lval, flags)
>REGISTER\_DOUBLE\_CONSTANT(name, dval, flags)
>REGISTER\_STRING\_CONSTANT(name, str, flags)
>REGISTER\_STRINGL\_CONSTANT(name, str, len, flags)

由於不需指定常量名長度，所以name參數應直接使用字符串，而不是char\*。

如需使用變量作為name參數，使用zend\_register\_\*\_constant()函數族，並指定變量名長度（sizeof(name)）。上面的宏函數其實是對這族函數的封裝。

>void zend\_register\_long\_constant(char \*name, uint name\_len, long lval, int flags, int module\_number TSRMLS\_DC)
>void zend\_register\_double\_constant(char \*name, uint name\_len, double dval, int flags, int module\_number TSRMLS\_DC)
>void zend\_register\_bool\_constant(const char \*name, uint name\_len, zend\_bool bval, int flags, int module\_number TSRMLS\_DC)
>void zend\_register\_string\_constant(char \*name, uint name\_len, char \*strval, int flags, int module\_number TSRMLS\_DC)
>void zend\_register\_stringl\_constant(char \*name, uint name\_len, char \*strval, uint strlen, int flags, int module\_number TSRMLS\_DC)

除此之外，還有REGISTER\_MAIN\_\*\_CONSTANT和REGISTER\_NS\_\*\_CONSTANT兩組宏函數。前者用於定義像E\_ERROR這樣的PHP標準常量，後者定義有命令空間的常量。

## define()和const

  - define()是函數，在運行時定義常量
    - 不能定義類常量
    - 可以在條件語句中使用
    - 可以指定常量是否對大小寫敏感
    - 可以用表達式作為常量值
    - 只定義全局常量，不支持命名空間
  - const是語句，在編譯時定義常量
    - 可以定義類常量
    - 不能在條件語句中使用
    - 定義的常量對大小寫敏感
    - 不支持表達式作為常量值
    - 若腳本定義了命名空間，聲明的常量屬於該命名空間

## 魔術常量

>\_\_LINE\_\_  
>\_\_FILE\_\_  
>\_\_DIR\_\_  
>\_\_FUNCTION\_\_  
>\_\_CLASS\_\_  
>\_\_METHOD\_\_  
>\_\_NAMESPACE\_\_  

魔術常量是在編譯時（具體地說是詞法分析時，見Zend/zend\_language\_scanner.l）被替換，確切地說，這些不是真正意義上的常量，只是個模板佔位符。
