---
layout: post
title: "PHP对象的实现和操作"
date: 2015-04-29 23:00:00
comments: true
categories:
- 计算机
tags:
- PHP
- 源码
---

## Object的存储结构

对象实例用zval存储。zval->type == IS_OBJECT，zval->value->obj存储zend_object_value类型的结构体变量。

```c
typedef struct _zend_object_value {
    zend_object_handle handle;
    const zend_object_handlers *handlers;
} zend_object_value;
```

zend_object_handle是一个unsigned int，是对象的ID。zend_object_handlers存储对象所有的行为。

## Object的实例化过程

Object的初始化用以下几个宏函数：

  - object_init(zval *arg)
  - object_init_ex(zval \*arg, zend_class_entry *class_type)
  - object_and_properties_init(zval \*arg, zend_class_entry \*class_type, HashTable *properties)

底层都是调用_object_and_properties_init(zval \*arg, zend_class_entry \*class_type, HashTable *properties)实现。这个函数做以下几件事：

  - 检查类是否可实例化（例如接口、抽象类等不允许初始化）
  - 处理类常量
  - 检查类是否存在自定义实例化逻辑
    - 若存在，调用自定义实例化逻辑
    - 若不存在，调用缺省的函数zend_objects_new(zend_object \*\*object, zend_class_entry *class_type)
  - 把实例化的zend_object类型的数据存入zval中

zend_objects_new()做这些事：

  - 分配一个zend_object类型的内存空间
  - 初始化zend_object类型数据
  - 把zend_object类型数据存入对象仓库（Objects Store）
    - zend_objects_store_put(void *object, zend_objects_store_dtor_t dtor, zend_objects_free_object_storage_t free_storage, zend_objects_store_clone_t clone）

## zend_object的存储结构

```c
typedef struct _zend_object {
    zend_class_entry *ce;
    HashTable *properties;
    zval **properties_table;
    HashTable *guards; /* protects from __get/__set ... recursion */
} zend_object;
```

ce是类的定义。properties_table存储类里预定义的属性。properties存储非预定义属性。

guards存储属性名到zend_guard结构的映射关系。

```c
typedef struct _zend_guard {
    zend_bool in_get;
    zend_bool in_set;
    zend_bool in_unset;
    zend_bool in_isset;
    zend_bool dummy; /* sizeof(zend_guard) must not be equal to sizeof(void*) */
} zend_guard;
```

此结构用于在操作属性时，防止递归调用。例如给对象一个新属性赋值时，\_\_set()函数理论上会递归调用自己，所以此结构用于判断该属性是否已在\_\_set()中。

## 属性的存储结构

在zend_object的存储结构里，哈希表properties存储类的非预定义属性的名称和值。

对于预定义的属性，由于PHP的哈希表的存储开销很大，所以把属性信息（即下面的zend_property_info结构体）存储在zend_class_entry里，对象里用C的数组存储所有预定义属性的zval的指针，并把偏移量记录在属性信息里，这就是properties_table。

```c
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
```

### 属性名的编码

在类的继承关系中，同名不同类型（public，private等）的属性各自单独存储，所以属性名在底层是经过编码的，规则如下：

>class Foo { private $prop;   } => "\0Foo\0prop"  
>class Bar { private $prop;   } => "\0Bar\0prop"  
>class Rab { protected $prop; } => "\0*\0prop"  
>class Oof { public $prop;    } => "prop"  

大部分情况下，对属性操作的API自动处理属性名的编码。只有当需要直接访问property_info->name或zobj->properties时才需要自行处理，此时使用zend_(un)mangle_property_name()函数。

## Objects Store的存储结构

对象仓库是一个可变数组，存储多个zend_object_store_bucket结构。

```c
typedef struct _zend_objects_store {
    zend_object_store_bucket *object_buckets;
    zend_uint top;
    zend_uint size;
    int free_list_head;
} zend_objects_store;
```

size是对象仓库的容量。top是下一个可用的对象句柄，对象句柄从1开始，以保证所有句柄都为真。对象仓库通过每个Bucket的free_list结构维护一个可用的Bucket链表，free_list_head记录链表的头部。

### zend_object_store_bucket的存储结构

每个对象的信息存储在一个bucket里。

```c
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
```

桶被占用的时候，valid为1，否则为0。

对象被销毁时，dtor被调用后，destructor_called被置为1，防止在被free时重复调用dtor，具体见**Object的二阶销毁逻辑**。

_store_object里存储对象的主要信息。zend_objects_store_put()传入的zend_object结构体存储在object里。dtor和free_storage见**Object的二阶销毁逻辑**。clone是对象的克隆函数。handlers存储对象的一系列操作函数，缺省为std_object_handlers。refcount是对象的引用计数。buffered是垃圾回收需要用到的数据。

free_list记录对象仓库中可用的Bucket链表中下一个可用的Bucket。

### Object Store的操作

  - zend_objects_store_put()：注册对象到仓库
  - zend_object_store_get_object_by_handle()：通过对象句柄取对象
  - zend_object_store_get_object()：通过zval取对象，返回void*
  - zend_objects_get_address()：和zend_object_store_get_object()一样，但返回zend_object*

## Object的二阶销毁逻辑

对象的销毁分两个步骤，一是对象的析构，一是内存的释放。前者调用对象的dtor，后者调用free_storage。一般先析构，再释放内存，但两者可各自分开执行。

dtor中可以执行用户空间的PHP代码，主要是PHP类的__destruct()。PHP脚本执行完成后销毁对象并结束进程（executor shutdown），在这个过程进行到一半的时候执行用户空间代码可能会出问题，所以这么区别主要是为了在进程结束过程中不会调用用户空间代码。

此外，dtor并不是必须执行的，如果一个对象的dtor调用的用户空间代码里执行了die()，后续对象的dtor不会被执行。所以大部分情况下，开发者可以自定义free_storage函数，而使用缺省的zend_objects_destroy_object作为dtor。
