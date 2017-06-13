---
layout: post
title: "PHP哈希表的實現與操作"
date: 2015-04-23 15:56
comments: true
categories: 計算機
tags:
- PHP
- 源碼
---

## 結構

{% codeblock lang:c %}
// 哈希表結構
typedef struct _hashtable {
    uint nTableSize;
    uint nTableMask;
    uint nNumOfElements;           // 全部元素數
    ulong nNextFreeElement;        // 下一個可用的整數鍵
    Bucket *pInternalPointer;      // 枚舉操作時使用，指向當前Bucket
    Bucket *pListHead;
    Bucket *pListTail;
    Bucket **arBuckets;
    dtor_func_t pDestructor;       // 元素的析構函數
    zend_bool persistent;          // 是否在本次請求結束後保留哈希表
    unsigned char nApplyCount;     // 循環級別，防止循環引用導致遍歷哈希表時死循環
    zend_bool bApplyProtection;    // 是否防止死循環
#if ZEND_DEBUG
    int inconsistent;
#endif
} HashTable;

// Bucket結構
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

### 哈希衝突處理

哈希表通過計算鍵值的哈希值，將對應的數據映射到對應的槽上。理論上會存在不同的鍵的哈希值相同的情況。

處理哈希衝突的方法一般有兩種：開放尋址和鏈表。開放尋址法是將衝突的元素順序放到下一個空槽，理論上會導致衝突越來越多，性能快速下降。鏈表法是將衝突的元素插入對應的槽，與前一個元素組成一個鏈表。PHP使用鏈表法。

PHP的哈希表中的Buckets組成兩種雙向鏈表。一種由每個槽中的所有Bucket分別組成，一種是整個哈希表中的Bucket組成一個。Bucket結構裡，pNext指向該槽的鏈表中的下一個Bucket，pLast指向上一個；pListNext指向整個哈希表鏈表的下一個Bucket，pListLast指向上一個。

### pData與pDataPtr

賦值到Bucket時，數據會被複製一份，pData中保存指向該數據拷貝的指針。特別地，如果保存一個指針到Bucket，會先將該指針保存到pDataPtr，然後將pData指向pDataPtr，即pData中保存的是指向pDataPtr中保存的指針的指針。這樣可以避免一次拷貝數據時分配內存的操作，提高效率。

### nTableSize與nTableMask

nTableSize保存的是arBuckets的Bucket個數。它的值永遠是個大於等於8的、2的n次方的整數。當現有容量不滿足需要時，arBuckets會重新分配一個大小是原來兩倍的空間，nTableSize相應地被更新為新的數值。

nTableMask = nTableSize - 1

哈希值h一般比nTableSize大，所以要用哈希值對nTableSize取模，以確定對應的Bucket。由於取模操作運算量大，且nTableSize永遠是2的n次冪，所以用“**h & (nTableSize - 1)**”替代。

## 初始化與銷毀

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

ALLOC_HASHTABLE就是用emalloc()分配內存。

#### zend_hash_init()

>zend_hash_init(HashTable *ht, uint nSize, dtor_func_t pDestructor, zend_bool persistent ZEND_FILE_LINE_DC)

nSize是哈希表的初始長度，實際分配為最接近指定值的2的n次方，最小為8。

pDestructor是被存儲數據的析構函數，默認為ZVAL_PTR_DTOR，對於一般情況（Bucket中存儲的是zval）適用。

persistent，1表示本次請求結束後保留哈希表，0反之。

### 銷毀哈希表

zend_hash_clean()對HT所有Bucket調用析構函數，並重置HT的所有指針。

zend_hash_destroy()除了銷毀所有Bucket存儲的數據，連arBuckets的空間也釋放掉。

FREE_HASHTABLE宏其實就是efree()。

## 操作數字鍵

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

## 操作字符串鍵

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

### 鍵的長度

鍵長包括鍵字符串末尾的NUL字節。如果直接指定，應是**sizeof("key1")**；如果是char\*類型變量，應是**strlen(key1)+1**。

### 快速操作

適用於頻繁操作特定鍵的場景，只計算一次哈希值，加速操作。

對應的，有一組名帶“quick”的函數。

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

## 遍歷

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

### 三個函數

遍歷哈希表的三個函數：

{% codeblock lang:c %}
void zend_hash_apply(HashTable *ht, apply_func_t apply_func TSRMLS_DC);
void zend_hash_apply_with_argument(
    HashTable *ht, apply_func_arg_t apply_func, void *argument TSRMLS_DC
);
void zend_hash_apply_with_arguments(
    HashTable *ht TSRMLS_DC, apply_func_args_t apply_func, int num_args, ...
);
{% endcodeblock %}

三個函數接受的回調函數的類型：

{% codeblock lang:c %}
typedef int (*apply_func_t)(void *pDest TSRMLS_DC);
typedef int (*apply_func_arg_t)(void *pDest, void *argument TSRMLS_DC);
typedef int (*apply_func_args_t)(
    void *pDest TSRMLS_DC, int num_args, va_list args, zend_hash_key *hash_key
);
{% endcodeblock %}

zend_hash_key的定義為：

{% codeblock lang:c %}
typedef struct _zend_hash_key {
    const char *arKey;
    uint nKeyLength;
    ulong h;
} zend_hash_key;
{% endcodeblock %}

nKeyLength為0表示索引是整數，值為h；否則是字符串，值為arKey。

回調函數的返回值：

  - ZEND_HASH_APPLY_KEEP：繼續遍歷。
  - ZEND_HASH_APPLY_REMOVE：遍歷後刪除遍歷過的元素。
  - ZEND_HASH_APPLY_STOP：遍歷當前元素後停止。
  - ZEND_HASH_APPLY_REMOVE | ZEND_HASH_APPLY_STOP：遍歷當前元素後，刪除該元素並停止。

## 枚舉

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

### 三個函數

三個函數均帶“\_ex”後綴，使用外部指針。不帶此後綴的函數使用哈希表內部指針，此時嵌套地遍歷哈希表可能導致指針修改錯誤。

### 取鍵的新方式

PHP 5.5以上版本新增函數，直接取鍵值到zval：

{% codeblock lang:c %}
zval *key;
MAKE_STD_ZVAL(key);
zend_hash_get_current_key_zval_ex(myht, key, &pos);
{% endcodeblock %}

## 複製與合併

{% codeblock lang:c %}
zend_hash_copy(ht_target, ht_source, (copy_ctor_func_t) zval_add_ref, NULL, sizeof(zval *));
{% endcodeblock %}

zval_add_ref是適用於zval的回調函數，直接引用原數據。

當目標哈希表已存在對應鍵值的數據時，目標元素會被源元素覆蓋。使用zend_hash_merge()可通過最後一個參數指定是否用源數據覆蓋目標數據。

函數zend_hash_merge_ex()可指定一個回調函數，用於過濾要合併的元素：

{% codeblock lang:c %}
zend_hash_merge_ex(
    Z_ARRVAL_P(return_value), Z_ARRVAL_P(array2), (copy_ctor_func_t) zval_add_ref,
    sizeof(zval *), (merge_checker_func_t) merge_greater, NULL
);
{% endcodeblock %}

回調函數的格式為：

{% codeblock lang:c %}
typedef zend_bool (*merge_checker_func_t)(
    HashTable *target_ht, void *source_data, zend_hash_key *hash_key, void *pParam
);
{% endcodeblock %}

## 比較、排序和極值

比較函數：

{% codeblock lang:c %}
int zend_hash_compare(
    HashTable *ht1, HashTable *ht2, compare_func_t compar, zend_bool ordered TSRMLS_DC
);

// 回調函數：
typedef int (*compare_func_t)(const void *left, const void *right TSRMLS_DC);
{% endcodeblock %}

排序函數：

{% codeblock lang:c %}
int zend_hash_sort(HashTable *ht, sort_func_t sort_func, compare_func_t compar, int renumber TSRMLS_DC);

// 回調函數
typedef void (*sort_func_t)(
    void *buckets, size_t num_of_buckets, register size_t size_of_bucket,
    compare_func_t compare_func TSRMLS_DC
);
{% endcodeblock %}

極值函數：

{% codeblock lang:c %}
int zend_hash_minmax(
    const HashTable *ht, compare_func_t compar, int flag, void **pData TSRMLS_DC
);
{% endcodeblock %}

flag=0，極小值寫入pData；flag=1，極大值寫入pData。
