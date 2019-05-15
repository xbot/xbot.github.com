---
layout: post
title: "PHP数组的实现与操作"
date: 2015-04-24 12:09
comments: true
categories: 计算机
tags:
- PHP
- 源码
---

## 存储结构

和其它变量一样，PHP的数组也是一个zval。存储数据的哈希表存放在zval->value->ht中。

## 符号表操作

为实现可转换成整数的字符串键与整数键指向同一个元素，在哈希表操作的基础上封装了一层，对可转换成整数的字符串键转换成整数，然后调用zend_hash_index\_\*操作，否则调用zend_hash\_\*操作。这就是符号表操作。

用ZEND_HANDLE_NUMERIC处理整数字符串键：

{% codeblock lang:c %}
static inline int zend_symtable_find(
    HashTable *ht, const char *arKey, uint nKeyLength, void **pData
) {
    ZEND_HANDLE_NUMERIC(arKey, nKeyLength, zend_hash_index_find(ht, idx, pData));
    return zend_hash_find(ht, arKey, nKeyLength, pData);
}
{% endcodeblock %}

其它符号表操作函数：

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

## 数组操作

### 初始化

{% codeblock lang:c %}
// 初始化数组
zval *zv1;
array_init(zv1);

// 初始化数组并指定哈希表nTableSize的值
array_init_size(zv1, 100);

// 在函数中返回数组：把返回值初始化为数组
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

星号表示类型名，可用类型名如下：

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

#### 字符串长度的处理

上述操作对字符串键和字符串值的长度的要求不同。\_ex函数要求传入字符串键的长度，此长度包含NUL字节。\_stringl函数要求传入字符串值的长度，此长度不包含NUL字节。

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
