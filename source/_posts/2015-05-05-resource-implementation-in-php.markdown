---
layout: post
title: "PHP资源的實現和操作"
date: 2015-05-05 18:53
comments: true
categories: 計算機
tags:
- PHP
- 源碼
---

## 存儲結構

資源變量也是一個zval結構，zval->type == IS_RESOURCE，zval->value->lval存儲一個整數，此整數為資源數據在存儲資源的哈希表中的索引。

資源數據的結構為：

{% codeblock lang:c %}
typedef struct _zend_rsrc_list_entry
{
    void *ptr;
    int type;
    int refcount;
}zend_rsrc_list_entry;
{% endcodeblock %}

### 常規資源與持久資源

有兩個存儲資源數據的哈希表。EG(regular_list)存儲常規資源，EG(persistent_list)存儲持久資源。

常規資源對應的變量在作用域結束後會被內核回收，對應的資源數據也會被銷毀。持久資源可以保持並被多次請求使用。持久資源的自動析構發生在PHP進程退出時。

## 實現

重新實現基本的文件句柄和相關操作。

{% codeblock lang:c %}
// 資源名稱
#define PHP_DONIE_RES_NAME_FILE "Donie's File Descriptor"

// 資源類型
static int le_donie_file_descriptor;
static int le_donie_file_descriptor_persist;

// 資源析構函數
static void php_donie_file_descriptor_dtor(zend_rsrc_list_entry *rsrc TSRMLS_CC)
{
	FILE *fp = (FILE*)rsrc->ptr;
	fclose(fp);
}

// 在擴展的MINIT方法裡創建資源類型
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

// 文件打開操作
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

// 文件寫操作
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

// 文件關閉操作
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
{% endcodeblock %}

### 創建新資源類型

zend_register_list_destructors_ex()創建新資源類型，並註冊該資源類型的析構函數、資源名稱。第一個參數是常規資源的析構函數，第二個是持久資源的析構函數，此處創建的是常規資源類型，故第二個參數不指定。

### 註冊資源

宏函數ZEND_REGISTER_RESOURCE()註冊新生成的資源到EG(regular_list)，並保存資源的索引到zval->value->lval中。

### 雙重引用計數

資源變量zval中存在一個引用計數，資源數據zend_rsrc_list_entry中也存在一個。前者遵循與其它變量一致的計數原則，後者取決於資源數據被幾個資源變量zval引用。

例如對於以下場景：

{% codeblock lang:php %}
<?php
$a = donie_fopen('/tmp/donie.txt', 'r');
$b = $a;
$c = &$a;
?>
{% endcodeblock %}

a賦值給b時，zval的引用計數加一。a的引用賦值給c時，發生zval的拆分，b獲得新的zval，引用計數是1，a和c共用一個zval，引用計數是2。此時，資源數據的引用計數加一。

### 獲取資源

ZEND_FETCH_RESOURCE()根據資源變量zval取出資源數據的ptr並驗證資源類型。ZEND_FETCH_RESOURCE2()可以同時指定兩個資源類型，任一類型匹配成功都可以。

### 銷毀資源

根據上述二重計數原則，只有當資源數據的引用計數為0時，資源的析構函數才會被調用，而銷毀資源變量不一定能銷毀資源，所以需要手工強制銷毀資源。

zend_hash_index_del()從EG(regular_list)中刪除資源時，該資源類型註冊的dtor會被自動調用，從而析構資源。
### 持久資源

#### 存儲

EG(persistent_list)是個用字符串索引的哈希表。需要自行定義鍵的命名規則，做到全局唯一。

#### 創建持久資源類型

zend_register_list_destructors_ex()註冊資源類型時，將析構函數指定為第二個參數，第一個參數為NULL。析構持久資源時，會自動調用該函數。

#### 註冊持久資源

EG(persistent_list)中的資源數據並不被直接使用，對資源的操作仍然使用EG(regular_list)。故在註冊持久資源時，兩個哈希表中都需要保存一份。

往EG(persistent_list)中存資源數據：

{% codeblock lang:c %}
char *hash_key;
int hash_key_len;
zend_rsrc_list_entry le;
le.type = le_donie_file_descriptor_persist;
le.ptr = fp;
hash_key_len = spprintf(&hash_key, 0, "php_donie_file_descriptor:%s-%s", filename, mode);
zend_hash_update(&EG(persistent_list), hash_key, hash_key_len+1, (void*)&le, sizeof(list_entry), NULL);
{% endcodeblock %}

#### 獲取持久資源

對持久資源的常規操作和操作常規資源一樣，仍使用EG(regular_list)，因為變量zval中存儲的是EG(regular_list)中的索引。所以需要先在EG(persistent_list)中查詢，若資源存在，先註冊到EG(regular_list)中，再進行後續操作。

#### 手動析構持久資源

用zend_hash_del()從EG(persistent_list)中刪除資源數據即可自動觸發析構函數。
