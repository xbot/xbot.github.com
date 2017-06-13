---
layout: post
title: "PHP數組的實現與操作"
date: 2015-04-24 12:09
comments: true
categories: 計算機
tags:
- PHP
- 源碼
---

## 存儲結構

和其它變量一樣，PHP的數組也是一個zval。存儲數據的哈希表存放在zval->value->ht中。

## 符號表操作

為實現可轉換成整數的字符串鍵與整數鍵指向同一個元素，在哈希表操作的基礎上封裝了一層，對可轉換成整數的字符串鍵轉換成整數，然後調用zend_hash_index\_\*操作，否則調用zend_hash\_\*操作。這就是符號表操作。

用ZEND_HANDLE_NUMERIC處理整數字符串鍵：

{% codeblock lang:c %}
static inline int zend_symtable_find(
    HashTable *ht, const char *arKey, uint nKeyLength, void **pData
) {
    ZEND_HANDLE_NUMERIC(arKey, nKeyLength, zend_hash_index_find(ht, idx, pData));
    return zend_hash_find(ht, arKey, nKeyLength, pData);
}
{% endcodeblock %}

其它符號表操作函數：

{% codeblock lang:c %}
static inline int zend_symtable_exists(HashTable *ht, const char *arKey, uint nKeyLength);
static inline int zend_symtable_del(HashTable *ht, const char *arKey, uint nKeyLength);
static inline int zend_symtable_update(
    HashTable *ht, const char *arKey, uint nKeyLength, void *pData, uint nDataSize, void **pDest
);
static inline int zend_symtable_update_current_key_ex(
    HashTable *ht, const char *arKey, uint nKeyLength, int mode, HashPosition *pos
);
{% endcodeblock %}

## 數組操作

### 初始化

{% codeblock lang:c %}
// 初始化數組
zval *zv1;
array_init(zv1);

// 初始化數組並指定哈希表nTableSize的值
array_init_size(zv1, 100);

// 在函數中返回數組：把返回值初始化為數組
array_init(return_value);
{% endcodeblock %}

### 插入和更新

{% codeblock lang:c %}
/* Insert at next index */
int add_next_index_*(zval *arg, ...);
/* Insert at specific index */
int add_index_*(zval *arg, ulong idx, ...);
/* Insert at specific key */
int add_assoc_*(zval *arg, const char *key, ...);
/* Insert at specific key of length key_len (for binary safety) */
int add_assoc_*_ex(zval *arg, const char *key, uint key_len, ...);
{% endcodeblock %}

星號表示類型名，可用類型名如下：

|Type	|Additional arguments|
| ----------- | ------------ |
|null	|none|
|bool	|int b|
|long	|long n|
|double	|double d|
|string	|const char *str, int duplicate|
|stringl	|const char *str, uint length, int duplicate|
|resource	|int r|
|zval	|zval *value|

#### 字符串長度的處理

上述操作對字符串鍵和字符串值的長度的要求不同。\_ex函數要求傳入字符串鍵的長度，此長度包含NUL字節。\_stringl函數要求傳入字符串值的長度，此長度不包含NUL字節。

### 栗子

{% codeblock lang:c %}
PHP_FUNCTION(donie_get_arr)
{
	array_init(return_value);

	// add an integer to the given position
	add_index_long(return_value, 1, 2015);

	// append a string to the array
	add_next_index_string(return_value, "dummy string", 1);

	// add a boolean value to the given key
	add_assoc_bool(return_value, "rightOrWrong", 0);

	// take care of string lengths
	add_assoc_stringl_ex(return_value, "keyStringL\0", sizeof("keyStringL\0")-1, "valueEx\0", sizeof("valueEx\0"), 1);

	// store an object in the array
	zval *obj;
	MAKE_STD_ZVAL(obj);
	object_init(obj);
	add_next_index_zval(return_value, obj);
}
{% endcodeblock %}
