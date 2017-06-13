---
layout: post
title: "PHP全局變量的實現和操作"
date: 2015-05-08 17:23
comments: true
categories: 計算機
tags:
- PHP
- 源碼
---

## 擴展內部的全局變量

{% codeblock lang:c php_donie.h %}
ZEND_BEGIN_MODULE_GLOBALS(donie)
	unsigned long global_long;
	char *global_string;
ZEND_END_MODULE_GLOBALS(donie)
{% endcodeblock %}

{% codeblock lang:c donie.c %}
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
{% endcodeblock %}

### 聲明

ZEND\_BEGIN\_MODULE\_GLOBALS和ZEND\_END\_MODULE\_GLOBALS及其間的內容實際上聲明了一個結構體zend\_donie\_globals。

根據是否開啟線程安全的情況，ZEND\_DECLARE\_MODULE\_GLOBALS做不同的事：未開啟線程安全，直接聲明zend\_donie\_globals類型的變量；已開啟線程安全，聲明一個整形變量donie\_globals\_id。

### 初始化

在未開啟線程安全時，ZEND\_INIT\_MODULE\_GLOBALS調用第二個參數指定的函數初始化全局變量；已開啟線程安全時，調用ts\_allocate\_id()分配一個資源ID，並調用第二個參數代表的函數。

### 訪問

DONIE\_G在擴展的頭文件裡，生成擴展框架時默認就有。

### 銷毀

開啟線程安全時，ZEND\_INIT\_MODULE\_GLOBALS的第三個參數指定的析構函數會自動被調用。未開啟線程安全時，由於該宏只調用第二個參數初始化全局變量，第三個參數沒有用，所以需要在MSHUTDOWN中手工調用析構函數。

## 用戶空間的超級全局變量

{% codeblock lang:c %}
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
{% endcodeblock %}

zend\_register\_auto\_global()註冊了$\_DONIE這樣一個全局變量。在代碼的編譯時，如果PHP內核發現代碼中沒有使用這個全局變量，不會進行初始化；若有使用，會調用php\_donie\_autoglobal\_callback進行初始化。PHP4中沒有php\_donie\_autoglobal\_callback這個參數。

php\_donie\_autoglobal\_callback做的事就是初始化一個zval並加入符號表。如果此函數返回0，則只會被調用一次，如果返回非0，在代碼編譯時，每發現一次該全局變量，就調用一次這個函數。

