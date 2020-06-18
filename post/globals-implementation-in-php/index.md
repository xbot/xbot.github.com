# PHP全局变量的实现和操作


## 扩展内部的全局变量

```c php_donie.h
ZEND_BEGIN_MODULE_GLOBALS(donie)
	unsigned long global_long;
	char *global_string;
ZEND_END_MODULE_GLOBALS(donie)
```

```c
ZEND_DECLARE_MODULE_GLOBALS(donie);

static void php_donie_init_globals(zend_donie_globals *donie_globals)
{
	donie_globals->global_long = 2015;
	donie_globals->global_string = "Long live Donie Leigh !";
}
static void php_donie_globals_dtor(zend_donie_globals *donie_globals)
{
	php_printf("php_donie_globals_dtor triggered.");
}

PHP_MINIT_FUNCTION(donie)
{
	/* init extension globals */
	ZEND_INIT_MODULE_GLOBALS(donie, php_donie_init_globals, php_donie_globals_dtor);

	return SUCCESS;
}

PHP_RSHUTDOWN_FUNCTION(donie)
{
#ifndef ZTS
	php_donie_globals_dtor(&donie_globals);
#endif

	return SUCCESS;
}

ZEND_FUNCTION(donie_test_ext_globals)
{
	php_printf("%s", DONIE_G(global_string));
}
```

### 声明

ZEND\_BEGIN\_MODULE\_GLOBALS和ZEND\_END\_MODULE\_GLOBALS及其间的内容实际上声明了一个结构体zend\_donie\_globals。

根据是否开启线程安全的情况，ZEND\_DECLARE\_MODULE\_GLOBALS做不同的事：未开启线程安全，直接声明zend\_donie\_globals类型的变量；已开启线程安全，声明一个整形变量donie\_globals\_id。

### 初始化

在未开启线程安全时，ZEND\_INIT\_MODULE\_GLOBALS调用第二个参数指定的函数初始化全局变量；已开启线程安全时，调用ts\_allocate\_id()分配一个资源ID，并调用第二个参数代表的函数。

### 访问

DONIE\_G在扩展的头文件里，生成扩展框架时默认就有。

### 销毁

开启线程安全时，ZEND\_INIT\_MODULE\_GLOBALS的第三个参数指定的析构函数会自动被调用。未开启线程安全时，由于该宏只调用第二个参数初始化全局变量，第三个参数没有用，所以需要在MSHUTDOWN中手工调用析构函数。

## 用户空间的超级全局变量

```c
static zend_bool php_donie_autoglobal_callback(const char *name, uint name_len TSRMLS_DC)
{
	zval *donie_val;
	MAKE_STD_ZVAL(donie_val);
	array_init(donie_val);
	add_next_index_string(donie_val, "Hello autoglobals !", 1);
	ZEND_SET_SYMBOL(&EG(symbol_table), "_DONIE", donie_val);
	return 0;
}

PHP_MINIT_FUNCTION(donie)
{
	/* declare userspace super globals */
	zend_register_auto_global("_DONIE", sizeof("_DONIE")-1, 0, php_donie_autoglobal_callback TSRMLS_CC);

	return SUCCESS;
}
```

zend\_register\_auto\_global()注册了$\_DONIE这样一个全局变量。在代码的编译时，如果PHP内核发现代码中没有使用这个全局变量，不会进行初始化；若有使用，会调用php\_donie\_autoglobal\_callback进行初始化。PHP4中没有php\_donie\_autoglobal\_callback这个参数。

php\_donie\_autoglobal\_callback做的事就是初始化一个zval并加入符号表。如果此函数返回0，则只会被调用一次，如果返回非0，在代码编译时，每发现一次该全局变量，就调用一次这个函数。


