---
layout: post
title: "PHP资源的实现和操作"
date: 2015-05-05 18:53:00
comments: true
categories:
- 计算机
tags:
- PHP
- 源码
---

## 存储结构

资源变量也是一个zval结构，zval->type == IS_RESOURCE，zval->value->lval存储一个整数，此整数为资源数据在存储资源的哈希表中的索引。

资源数据的结构为：

```c
typedef struct _zend_rsrc_list_entry
{
    void *ptr;
    int type;
    int refcount;
}zend_rsrc_list_entry;
```

### 常规资源与持久资源

有两个存储资源数据的哈希表。EG(regular_list)存储常规资源，EG(persistent_list)存储持久资源。

常规资源对应的变量在作用域结束后会被内核回收，对应的资源数据也会被销毁。持久资源可以保持并被多次请求使用。持久资源的自动析构发生在PHP进程退出时。

## 实现

重新实现基本的文件句柄和相关操作。

```c
// 资源名称
#define PHP_DONIE_RES_NAME_FILE "Donie's File Descriptor"

// 资源类型
static int le_donie_file_descriptor;
static int le_donie_file_descriptor_persist;

// 资源析构函数
static void php_donie_file_descriptor_dtor(zend_rsrc_list_entry *rsrc TSRMLS_CC)
{
	FILE *fp = (FILE*)rsrc->ptr;
	fclose(fp);
}

// 在扩展的MINIT方法里创建资源类型
PHP_MINIT_FUNCTION(donie)
{
	/* create a new resource type */
	le_donie_file_descriptor = zend_register_list_destructors_ex(
		php_donie_file_descriptor_dtor, NULL, PHP_DONIE_RES_NAME_FILE, module_number
	);

	/* create a persistent resource type */
	le_donie_file_descriptor_persist = zend_register_list_destructors_ex(
		NULL, php_donie_file_descriptor_dtor, PHP_DONIE_RES_NAME_FILE, module_number
	);

	return SUCCESS;
}

// 文件打开操作
PHP_FUNCTION(donie_fopen)
{
	FILE *fp;
	char *filename, *mode;
	int filename_len, mode_len;
	zend_bool persist = 0;
	char *hash_key;
	int hash_key_len;
	list_entry *persist_file;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss|b", &filename, &filename_len, &mode, &mode_len, &persist) == FAILURE)
	{
		RETURN_NULL();
	}
	if (!filename_len || !mode_len)
	{
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Invalid file name or mode.");
		RETURN_FALSE;
	}

	/* reuse persistent resource if exists */
	hash_key_len = spprintf(&hash_key, 0, "php_donie_file_descriptor:%s-%s", filename, mode);
	if (zend_hash_find(&EG(persistent_list), hash_key, hash_key_len+1, (void **)&persist_file) == SUCCESS)
	{
		ZEND_REGISTER_RESOURCE(return_value, persist_file->ptr, le_donie_file_descriptor_persist);
		efree(hash_key);
		return;
	}

	fp = fopen(filename, mode);
	if (!fp)
	{
		php_error_docref(NULL TSRMLS_CC, E_WARNING, "Failed opening %s with mode %s.", filename, mode);
		RETURN_FALSE;
	}

	/* this is the key point for registering resources */
	if (persist)
	{
		ZEND_REGISTER_RESOURCE(return_value, fp, le_donie_file_descriptor_persist);
		list_entry le;
		le.type = le_donie_file_descriptor_persist;
		le.ptr = fp;
		zend_hash_update(&EG(persistent_list), hash_key, hash_key_len+1, (void*)&le, sizeof(list_entry), NULL);
	}
	else
	{
		ZEND_REGISTER_RESOURCE(return_value, fp, le_donie_file_descriptor);
	}
	efree(hash_key);
}

// 文件写操作
PHP_FUNCTION(donie_fwrite)
{
	FILE *fp;
	zval *file_resource;
	char *data;
	int data_len;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rs", &file_resource, &data, &data_len) == FAILURE)
	{
		RETURN_NULL();
	}

	ZEND_FETCH_RESOURCE2(fp, FILE*, &file_resource, -1, PHP_DONIE_RES_NAME_FILE, le_donie_file_descriptor, le_donie_file_descriptor_persist);
	RETURN_LONG(fwrite(data, 1, data_len, fp));
}

// 文件关闭操作
PHP_FUNCTION(donie_fclose)
{
	FILE *fp;
	zval *file_resource;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r", &file_resource) == FAILURE)
	{
		RETURN_NULL();
	}

	ZEND_FETCH_RESOURCE2(fp, FILE*, &file_resource, -1, PHP_DONIE_RES_NAME_FILE, le_donie_file_descriptor, le_donie_file_descriptor_persist);
	zend_hash_index_del(&EG(regular_list), Z_RESVAL_P(file_resource));
	RETURN_TRUE;
}
```

### 创建新资源类型

zend_register_list_destructors_ex()创建新资源类型，并注册该资源类型的析构函数、资源名称。第一个参数是常规资源的析构函数，第二个是持久资源的析构函数，此处创建的是常规资源类型，故第二个参数不指定。

### 注册资源

宏函数ZEND_REGISTER_RESOURCE()注册新生成的资源到EG(regular_list)，并保存资源的索引到zval->value->lval中。

### 双重引用计数

资源变量zval中存在一个引用计数，资源数据zend_rsrc_list_entry中也存在一个。前者遵循与其它变量一致的计数原则，后者取决于资源数据被几个资源变量zval引用。

例如对于以下场景：

```php
<?php
$a = donie_fopen('/tmp/donie.txt', 'r');
$b = $a;
$c = &$a;
?>
```

a赋值给b时，zval的引用计数加一。a的引用赋值给c时，发生zval的拆分，b获得新的zval，引用计数是1，a和c共用一个zval，引用计数是2。此时，资源数据的引用计数加一。

### 获取资源

ZEND_FETCH_RESOURCE()根据资源变量zval取出资源数据的ptr并验证资源类型。ZEND_FETCH_RESOURCE2()可以同时指定两个资源类型，任一类型匹配成功都可以。

### 销毁资源

根据上述二重计数原则，只有当资源数据的引用计数为0时，资源的析构函数才会被调用，而销毁资源变量不一定能销毁资源，所以需要手工强制销毁资源。

zend_hash_index_del()从EG(regular_list)中删除资源时，该资源类型注册的dtor会被自动调用，从而析构资源。
### 持久资源

#### 存储

EG(persistent_list)是个用字符串索引的哈希表。需要自行定义键的命名规则，做到全局唯一。

#### 创建持久资源类型

zend_register_list_destructors_ex()注册资源类型时，将析构函数指定为第二个参数，第一个参数为NULL。析构持久资源时，会自动调用该函数。

#### 注册持久资源

EG(persistent_list)中的资源数据并不被直接使用，对资源的操作仍然使用EG(regular_list)。故在注册持久资源时，两个哈希表中都需要保存一份。

往EG(persistent_list)中存资源数据：

```c
char *hash_key;
int hash_key_len;
zend_rsrc_list_entry le;
le.type = le_donie_file_descriptor_persist;
le.ptr = fp;
hash_key_len = spprintf(&hash_key, 0, "php_donie_file_descriptor:%s-%s", filename, mode);
zend_hash_update(&EG(persistent_list), hash_key, hash_key_len+1, (void*)&le, sizeof(list_entry), NULL);
```

#### 获取持久资源

对持久资源的常规操作和操作常规资源一样，仍使用EG(regular_list)，因为变量zval中存储的是EG(regular_list)中的索引。所以需要先在EG(persistent_list)中查询，若资源存在，先注册到EG(regular_list)中，再进行后续操作。

#### 手动析构持久资源

用zend_hash_del()从EG(persistent_list)中删除资源数据即可自动触发析构函数。
