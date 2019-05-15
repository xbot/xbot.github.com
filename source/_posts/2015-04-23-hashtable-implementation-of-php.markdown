---
layout: post
title: "PHP哈希表的实现与操作"
date: 2015-04-23 15:56
comments: true
categories: 计算机
tags:
- PHP
- 源码
---

## 结构

{% codeblock lang:c %}
// 哈希表结构
typedef struct _hashtable {
    uint nTableSize;
    uint nTableMask;
    uint nNumOfElements;           // 全部元素数
    ulong nNextFreeElement;        // 下一个可用的整数键
    Bucket *pInternalPointer;      // 枚举操作时使用，指向当前Bucket
    Bucket *pListHead;
    Bucket *pListTail;
    Bucket **arBuckets;
    dtor_func_t pDestructor;       // 元素的析构函数
    zend_bool persistent;          // 是否在本次请求结束后保留哈希表
    unsigned char nApplyCount;     // 循环级别，防止循环引用导致遍历哈希表时死循环
    zend_bool bApplyProtection;    // 是否防止死循环
#if ZEND_DEBUG
    int inconsistent;
#endif
} HashTable;

// Bucket结构
typedef struct bucket {
    ulong h;
    uint nKeyLength;
    void *pData;
    void *pDataPtr;
    struct bucket *pListNext;
    struct bucket *pListLast;
    struct bucket *pNext;
    struct bucket *pLast;
    char *arKey;
} Bucket;
{% endcodeblock %}

### 哈希冲突处理

哈希表通过计算键值的哈希值，将对应的数据映射到对应的槽上。理论上会存在不同的键的哈希值相同的情况。

处理哈希冲突的方法一般有两种：开放寻址和链表。开放寻址法是将冲突的元素顺序放到下一个空槽，理论上会导致冲突越来越多，性能快速下降。链表法是将冲突的元素插入对应的槽，与前一个元素组成一个链表。PHP使用链表法。

PHP的哈希表中的Buckets组成两种双向链表。一种由每个槽中的所有Bucket分别组成，一种是整个哈希表中的Bucket组成一个。Bucket结构里，pNext指向该槽的链表中的下一个Bucket，pLast指向上一个；pListNext指向整个哈希表链表的下一个Bucket，pListLast指向上一个。

### pData与pDataPtr

赋值到Bucket时，数据会被复制一份，pData中保存指向该数据拷贝的指针。特别地，如果保存一个指针到Bucket，会先将该指针保存到pDataPtr，然后将pData指向pDataPtr，即pData中保存的是指向pDataPtr中保存的指针的指针。这样可以避免一次拷贝数据时分配内存的操作，提高效率。

### nTableSize与nTableMask

nTableSize保存的是arBuckets的Bucket个数。它的值永远是个大于等于8的、2的n次方的整数。当现有容量不满足需要时，arBuckets会重新分配一个大小是原来两倍的空间，nTableSize相应地被更新为新的数值。

nTableMask = nTableSize - 1

哈希值h一般比nTableSize大，所以要用哈希值对nTableSize取模，以确定对应的Bucket。由于取模操作运算量大，且nTableSize永远是2的n次幂，所以用“**h & (nTableSize - 1)**”替代。

## 初始化与销毁

{% codeblock lang:c %}
// init hashtable
HashTable *myht;
ALLOC_HASHTABLE(myht);
if (zend_hash_init(myht, 100, NULL, NULL, 0) == FAILURE)
{
    FREE_HASHTABLE(myht);
    return FAILURE;
}

// destroy hashtable
zend_hash_destroy(myht);
FREE_HASHTABLE(myht);
return SUCCESS;
{% endcodeblock %}

### 初始化哈希表

ALLOC_HASHTABLE就是用emalloc()分配内存。

#### zend_hash_init()

>zend_hash_init(HashTable *ht, uint nSize, dtor_func_t pDestructor, zend_bool persistent ZEND_FILE_LINE_DC)

nSize是哈希表的初始长度，实际分配为最接近指定值的2的n次方，最小为8。

pDestructor是被存储数据的析构函数，默认为ZVAL_PTR_DTOR，对于一般情况（Bucket中存储的是zval）适用。

persistent，1表示本次请求结束后保留哈希表，0反之。

### 销毁哈希表

zend_hash_clean()对HT所有Bucket调用析构函数，并重置HT的所有指针。

zend_hash_destroy()除了销毁所有Bucket存储的数据，连arBuckets的空间也释放掉。

FREE_HASHTABLE宏其实就是efree()。

## 操作数字键

{% codeblock lang:c %}
// add a string with an integer key 2 to myht
zval *zv1;
MAKE_STD_ZVAL(zv1);
ZVAL_STRING(zv1, "Hello HT !", 1);
zend_hash_index_update(myht, 2, &zv1, sizeof(zval *), NULL);

// get the next free key
php_printf("The next free key will be %ld.\n", zend_hash_next_free_element(myht));

// append an integer to myht
zval *zv2;
MAKE_STD_ZVAL(zv2);
ZVAL_LONG(zv2, 2015);
if (zend_hash_next_index_insert(myht, &zv2, sizeof(zval *), NULL) == FAILURE)
{
    php_printf("HashTable appendation failed.\n");
}
else
{
    php_printf("HashTable appendation succeeded.\n");
}

// get the size
php_printf("HashTable has a size of %d.\n", zend_hash_num_elements(myht));

// check if an integer key exists
int idx = 3;
if (zend_hash_index_exists(myht, idx))
{
    php_printf("HashTable has an index of the value %ld.\n", idx);
}
else
{
    php_printf("HashTable does not have an index of the value %ld.\n", idx);
}

// get a value by its key
zval **zval_dest;
if (zend_hash_index_find(myht, idx, (void **) &zval_dest) == SUCCESS)
{
    php_printf("The value indexed by %ld is %Z.\n", idx, *zval_dest);
}
else
{
    php_printf("The value indexed by %ld does not exist.\n", idx);
}

// delete the specified value from myht
if (zend_hash_index_del(myht, idx) == FAILURE)
{
    php_printf("The value indexed by %ld failed to be deleted.\n", idx);
}
else
{
    php_printf("The value indexed by %ld is deleted.\n", idx);
}
{% endcodeblock %}

## 操作字符串键

{% codeblock lang:c %}
// add an integer indexed by a string key, using zend_hash_update()
zval *zv3;
MAKE_STD_ZVAL(zv3);
ZVAL_LONG(zv3, 1985);
zend_hash_update(myht, "year", sizeof("year"), &zv3, sizeof(zval *), NULL);
php_printf("An integer is updated to the hash-table indexed by a string key.\n");

// add a string indexed by a string key, using zend_hash_add()
zval *zv4;
MAKE_STD_ZVAL(zv4);
ZVAL_STRING(zv4, "Great Donie !", 1);
if (zend_hash_add(myht, "motto", sizeof("motto"), &zv4, sizeof(zval *), NULL) == FAILURE)
{
	php_printf("Cannot add a string indexed by a string key to the hash-table, may be the index already exists.\n");
}
else
{
	php_printf("A string is added to the hash-table indexed by a string key.\n");
}

// get the next free key
php_printf("The next free key will be %ld.\n", zend_hash_next_free_element(myht));

// check if a string key exists
char *key1 = "year";
if (zend_hash_exists(myht, key1, strlen(key1)+1))
{
	php_printf("The key %s exists.\n", key1);
}
else
{
	php_printf("The key %s does not exist.\n", key1);
}

// get the value indexed by a string key
zval **zv_dest2;
if (zend_hash_find(myht, key1, strlen(key1)+1, (void **) &zv_dest2) == SUCCESS)
{
	php_printf("The value indexed by %s is %Z.\n", key1, *zv_dest2);
}
else
{
	php_printf("Failed fetching the value indexed by %s.\n", key1);
}

// delete the value indexed by a string key
if (zend_hash_del(myht, key1, strlen(key1)+1) == SUCCESS)
{
	php_printf("The value indexed by %s is deleted.\n", key1);
}
else
{
	php_printf("The value indexed by %s failed to be deleted.\n", key1);
}
{% endcodeblock %}

### 键的长度

键长包括键字符串末尾的NUL字节。如果直接指定，应是**sizeof("key1")**；如果是char\*类型变量，应是**strlen(key1)+1**。

### 快速操作

适用于频繁操作特定键的场景，只计算一次哈希值，加速操作。

对应的，有一组名带“quick”的函数。

{% codeblock lang:c %}
// quick operations leveraging a one-time hashed value
zval *zv5;
MAKE_STD_ZVAL(zv5);
ZVAL_STRING(zv5, "Great Donie Leigh !", 1);

ulong h;
h = zend_get_hash_value("motto", sizeof("motto"));
zend_hash_quick_update(myht, "motto", sizeof("motto"), h, &zv5, sizeof(zval *), NULL);
php_printf("The value indexed by motto is updated with the quick operation.\n");
{% endcodeblock %}

## 遍历

{% codeblock lang:c %}
static int hashtable_traverse_callback(void *pDest TSRMLS_DC, int num_args, va_list args, zend_hash_key *hash_key)
{
	zval **zv = (zval **) pDest;
	char *arg1 = va_arg(args, char*);

	php_printf("The first argument is %s.\n", arg1);

	if (hash_key->nKeyLength == 0)
	{
		php_printf("K-V: %d=>%Z\n", hash_key->h, *zv);
	}
	else
	{
		php_printf("K-V: %s=>%Z\n", hash_key->arKey, *zv);
	}

	return ZEND_HASH_APPLY_KEEP;
}

// traverse the hash table.
zend_hash_apply_with_arguments(myht, hashtable_traverse_callback, 1, "nonsense");
{% endcodeblock %}

### 三个函数

遍历哈希表的三个函数：

{% codeblock lang:c %}
void zend_hash_apply(HashTable *ht, apply_func_t apply_func TSRMLS_DC);
void zend_hash_apply_with_argument(
    HashTable *ht, apply_func_arg_t apply_func, void *argument TSRMLS_DC
);
void zend_hash_apply_with_arguments(
    HashTable *ht TSRMLS_DC, apply_func_args_t apply_func, int num_args, ...
);
{% endcodeblock %}

三个函数接受的回调函数的类型：

{% codeblock lang:c %}
typedef int (*apply_func_t)(void *pDest TSRMLS_DC);
typedef int (*apply_func_arg_t)(void *pDest, void *argument TSRMLS_DC);
typedef int (*apply_func_args_t)(
    void *pDest TSRMLS_DC, int num_args, va_list args, zend_hash_key *hash_key
);
{% endcodeblock %}

zend_hash_key的定义为：

{% codeblock lang:c %}
typedef struct _zend_hash_key {
    const char *arKey;
    uint nKeyLength;
    ulong h;
} zend_hash_key;
{% endcodeblock %}

nKeyLength为0表示索引是整数，值为h；否则是字符串，值为arKey。

回调函数的返回值：

  - ZEND_HASH_APPLY_KEEP：继续遍历。
  - ZEND_HASH_APPLY_REMOVE：遍历后删除遍历过的元素。
  - ZEND_HASH_APPLY_STOP：遍历当前元素后停止。
  - ZEND_HASH_APPLY_REMOVE | ZEND_HASH_APPLY_STOP：遍历当前元素后，删除该元素并停止。

## 枚举

{% codeblock lang:c %}
// iterating the hash table
php_printf("Begin iterating the hash table:\n");
HashPosition pos;
zval **data;
char *str_idx;
uint str_len;
ulong num_idx;
for (zend_hash_internal_pointer_reset_ex(myht, &pos);
	zend_hash_get_current_data_ex(myht, (void **) &data, &pos) == SUCCESS;
	zend_hash_move_forward_ex(myht, &pos)
) {
	switch (zend_hash_get_current_key_ex(myht, &str_idx, &str_len, &num_idx, 0, &pos)) {
		case HASH_KEY_IS_LONG:
			php_printf("K-V: %d=>%Z\n", num_idx, *data);
			break;
		case HASH_KEY_IS_STRING:
			php_printf("K-V: %s=>%Z\n", str_idx, *data);
			break;
	}
}
{% endcodeblock %}

### 三个函数

三个函数均带“\_ex”后缀，使用外部指针。不带此后缀的函数使用哈希表内部指针，此时嵌套地遍历哈希表可能导致指针修改错误。

### 取键的新方式

PHP 5.5以上版本新增函数，直接取键值到zval：

{% codeblock lang:c %}
zval *key;
MAKE_STD_ZVAL(key);
zend_hash_get_current_key_zval_ex(myht, key, &pos);
{% endcodeblock %}

## 复制与合并

{% codeblock lang:c %}
zend_hash_copy(ht_target, ht_source, (copy_ctor_func_t) zval_add_ref, NULL, sizeof(zval *));
{% endcodeblock %}

zval_add_ref是适用于zval的回调函数，直接引用原数据。

当目标哈希表已存在对应键值的数据时，目标元素会被源元素覆盖。使用zend_hash_merge()可通过最后一个参数指定是否用源数据覆盖目标数据。

函数zend_hash_merge_ex()可指定一个回调函数，用于过滤要合并的元素：

{% codeblock lang:c %}
zend_hash_merge_ex(
    Z_ARRVAL_P(return_value), Z_ARRVAL_P(array2), (copy_ctor_func_t) zval_add_ref,
    sizeof(zval *), (merge_checker_func_t) merge_greater, NULL
);
{% endcodeblock %}

回调函数的格式为：

{% codeblock lang:c %}
typedef zend_bool (*merge_checker_func_t)(
    HashTable *target_ht, void *source_data, zend_hash_key *hash_key, void *pParam
);
{% endcodeblock %}

## 比较、排序和极值

比较函数：

{% codeblock lang:c %}
int zend_hash_compare(
    HashTable *ht1, HashTable *ht2, compare_func_t compar, zend_bool ordered TSRMLS_DC
);

// 回调函数：
typedef int (*compare_func_t)(const void *left, const void *right TSRMLS_DC);
{% endcodeblock %}

排序函数：

{% codeblock lang:c %}
int zend_hash_sort(HashTable *ht, sort_func_t sort_func, compare_func_t compar, int renumber TSRMLS_DC);

// 回调函数
typedef void (*sort_func_t)(
    void *buckets, size_t num_of_buckets, register size_t size_of_bucket,
    compare_func_t compare_func TSRMLS_DC
);
{% endcodeblock %}

极值函数：

{% codeblock lang:c %}
int zend_hash_minmax(
    const HashTable *ht, compare_func_t compar, int flag, void **pData TSRMLS_DC
);
{% endcodeblock %}

flag=0，极小值写入pData；flag=1，极大值写入pData。
