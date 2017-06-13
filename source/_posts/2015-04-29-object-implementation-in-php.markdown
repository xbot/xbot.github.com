---
layout: post
title: "PHP對象的實現和操作"
date: 2015-04-29 23:00
comments: true
categories: 計算機
tags:
- PHP
- 源碼
---

## Object的存儲結構

對象實例用zval存儲。zval->type == IS_OBJECT，zval->value->obj存儲zend_object_value類型的結構體變量。

{% codeblock lang:c %}
typedef struct _zend_object_value {
    zend_object_handle handle;
    const zend_object_handlers *handlers;
} zend_object_value;
{% endcodeblock %}

zend_object_handle是一個unsigned int，是對象的ID。zend_object_handlers存儲對象所有的行為。

## Object的實例化過程

Object的初始化用以下幾個宏函數：

  - object_init(zval *arg)
  - object_init_ex(zval \*arg, zend_class_entry *class_type)
  - object_and_properties_init(zval \*arg, zend_class_entry \*class_type, HashTable *properties)

底層都是調用_object_and_properties_init(zval \*arg, zend_class_entry \*class_type, HashTable *properties)實現。這個函數做以下幾件事：

  - 檢查類是否可實例化（例如接口、抽象類等不允許初始化）
  - 處理類常量
  - 檢查類是否存在自定義實例化邏輯
    - 若存在，調用自定義實例化邏輯
    - 若不存在，調用缺省的函數zend_objects_new(zend_object \*\*object, zend_class_entry *class_type)
  - 把實例化的zend_object類型的數據存入zval中

zend_objects_new()做這些事：

  - 分配一個zend_object類型的內存空間
  - 初始化zend_object類型數據
  - 把zend_object類型數據存入對象倉庫（Objects Store）
    - zend_objects_store_put(void *object, zend_objects_store_dtor_t dtor, zend_objects_free_object_storage_t free_storage, zend_objects_store_clone_t clone）

## zend_object的存儲結構

{% codeblock lang:c %}
typedef struct _zend_object {
    zend_class_entry *ce;
    HashTable *properties;
    zval **properties_table;
    HashTable *guards; /* protects from __get/__set ... recursion */
} zend_object;
{% endcodeblock %}

ce是類的定義。properties_table存儲類裡預定義的屬性。properties存儲非預定義屬性。

guards存儲屬性名到zend_guard結構的映射關係。

{% codeblock lang:c %}
typedef struct _zend_guard {
    zend_bool in_get;
    zend_bool in_set;
    zend_bool in_unset;
    zend_bool in_isset;
    zend_bool dummy; /* sizeof(zend_guard) must not be equal to sizeof(void*) */
} zend_guard;
{% endcodeblock %}

此結構用於在操作屬性時，防止遞歸調用。例如給對象一個新屬性賦值時，\_\_set()函數理論上會遞歸調用自己，所以此結構用於判斷該屬性是否已在\_\_set()中。

## 屬性的存儲結構

在zend_object的存儲結構裡，哈希表properties存儲類的非預定義屬性的名称和值。

對於預定義的屬性，由於PHP的哈希表的存儲開銷很大，所以把屬性信息（即下面的zend_property_info結構體）存儲在zend_class_entry裡，對象裡用C的數組存儲所有預定義屬性的zval的指針，並把偏移量記錄在屬性信息裡，這就是properties_table。

{% codeblock lang:c %}
typedef struct _zend_property_info {
    zend_uint flags;
    const char *name;
    int name_length;
    ulong h;                 /* hash of name */
    int offset;              /* storage offset */
    const char *doc_comment;
    int doc_comment_len;
    zend_class_entry *ce;    /* CE of declaring class */
} zend_property_info;
{% endcodeblock %}

### 屬性名的編碼

在類的繼承關係中，同名不同類型（public，private等）的屬性各自單獨存儲，所以屬性名在底層是經過編碼的，規則如下：

>class Foo { private $prop;   } => "\0Foo\0prop"  
>class Bar { private $prop;   } => "\0Bar\0prop"  
>class Rab { protected $prop; } => "\0*\0prop"  
>class Oof { public $prop;    } => "prop"  

大部分情況下，對屬性操作的API自動處理屬性名的編碼。只有當需要直接訪問property_info->name或zobj->properties時才需要自行處理，此時使用zend_(un)mangle_property_name()函數。

## Objects Store的存儲結構

對象倉庫是一個可變數組，存儲多個zend_object_store_bucket結構。

{% codeblock lang:c %}
typedef struct _zend_objects_store {
    zend_object_store_bucket *object_buckets;
    zend_uint top;
    zend_uint size;
    int free_list_head;
} zend_objects_store;
{% endcodeblock %}

size是對象倉庫的容量。top是下一個可用的對象句柄，對象句柄從1開始，以保證所有句柄都為真。對象倉庫通過每個Bucket的free_list結構維護一個可用的Bucket鏈表，free_list_head記錄鏈表的頭部。

### zend_object_store_bucket的存儲結構

每個對象的信息存儲在一個bucket裡。

{% codeblock lang:c %}
typedef struct _zend_object_store_bucket {
    zend_bool destructor_called;
    zend_bool valid;
    union _store_bucket {
        struct _store_object {
            void *object;
            zend_objects_store_dtor_t dtor;
            zend_objects_free_object_storage_t free_storage;
            zend_objects_store_clone_t clone;
            const zend_object_handlers *handlers;
            zend_uint refcount;
            gc_root_buffer *buffered;
        } obj;
        struct {
            int next;
        } free_list;
    } bucket;
} zend_object_store_bucket;
{% endcodeblock %}

桶被佔用的時候，valid為1，否則為0。

對象被銷毀時，dtor被調用後，destructor_called被置為1，防止在被free時重複調用dtor，具體見**Object的二階銷毀邏輯**。

_store_object裡存儲對象的主要信息。zend_objects_store_put()傳入的zend_object結構體存儲在object裡。dtor和free_storage見**Object的二階銷毀邏輯**。clone是對象的克隆函數。handlers存儲對象的一系列操作函數，缺省為std_object_handlers。refcount是對象的引用計數。buffered是垃圾回收需要用到的數據。

free_list記錄對象倉庫中可用的Bucket鏈表中下一個可用的Bucket。

### Object Store的操作

  - zend_objects_store_put()：註冊對象到倉庫
  - zend_object_store_get_object_by_handle()：通過對象句柄取對象
  - zend_object_store_get_object()：通過zval取對象，返回void*
  - zend_objects_get_address()：和zend_object_store_get_object()一樣，但返回zend_object*

## Object的二階銷毀邏輯

對象的銷毀分兩個步驟，一是對象的析構，一是內存的釋放。前者調用對象的dtor，後者調用free_storage。一般先析構，再釋放內存，但兩者可各自分開執行。

dtor中可以執行用戶空間的PHP代碼，主要是PHP類的__destruct()。PHP腳本執行完成後銷毀對象並結束進程（executor shutdown），在這個過程進行到一半的時候執行用戶空間代碼可能會出問題，所以這麼區別主要是為了在進程結束過程中不會調用用戶空間代碼。

此外，dtor並不是必須執行的，如果一個對象的dtor調用的用戶空間代碼裡執行了die()，後續對象的dtor不會被執行。所以大部分情況下，開發者可以自定義free_storage函數，而使用缺省的zend_objects_destroy_object作為dtor。
