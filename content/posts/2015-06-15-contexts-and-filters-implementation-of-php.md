---
layout: post
title: "PHP流的上下文和过滤器的实现"
slug: contexts and filters implementation of php
date: 2015-06-15 19:15:00
comments: true
categories:
- 计算机
tags:
- PHP
- 源码
---

## 上下文的实现和应用

上下文包含流的选项和流的参数两部分内容。

```c
php_stream_context *php_stream_context_alloc(void);
```

流的选项是一系列键值对。

```c
int php_stream_context_set_option(php_stream_context *context, const char *wrappername, const char *optionname, zval *optionvalue);

int php_stream_context_get_option(php_stream_context *context, const char *wrappername, const char *optionname, zval ***optionvalue);
```

流的参数目前只实现对流的事件的回调，php_stream_context->notifier存储如下结构：

```c
typedef struct {
    php_stream_notification_func func;
    void (*dtor)(php_stream_notifier *notifier);
    void *ptr;
    int mask;
    size_t progress, progress_max;
} php_stream_notifier;
```

回调函数的原型为：

```c
typedef void (*php_stream_notification_func)(php_stream_context *context,
		int notifycode, int severity,
		char *xmsg, int xcode,
		size_t bytes_sofar, size_t bytes_max,
		void * ptr TSRMLS_DC);
```

notifycode包含如下：

  - PHP_STREAM_NOTIFY_RESOLVE：主机名解析完成
  - PHP_STREAM_NOTIFY_CONNECT：socket连接建立
  - PHP_STREAM_NOTIFY_AUTH_REQUIRED：需要验证
  - PHP_STREAM_NOTIFY_MIME_TYPE_IS：远程资源的MIME-Type可用
  - PHP_STREAM_NOTIFY_FILE_SIZE_IS：远程资源的大小可用
  - PHP_STREAM_NOTIFY_REDIRECTED：原始地址被跳转
  - PHP_STREAM_NOTIFY_PROGRESS：php_stream_notifier->progress和progress_max（可能的）已更新
  - PHP_STREAM_NOTIFY_COMPLETED：流中已无可用数据
  - PHP_STREAM_NOTIFY_FAILURE：请求失败
  - PHP_STREAM_NOTIFY_AUTH_RESULT：远程验证已完成，并且可能是成功的

severity包含如下：

  - PHP_STREAM_NOTIFY_SEVERITY_INFO
  - PHP_STREAM_NOTIFY_SEVERITY_WARN
  - PHP_STREAM_NOTIFY_SEVERITY_ERR

php_stream_notifier->ptr用于存储附加数据，如果该数据需要手工回收，需指定php_stream_notifier->dtor。

php_stream_notifier->mask如果被赋值severity，其它severity的事件将不会触发回调函数。


## 过滤器的实现和应用

```c
#include "ext/standard/php_string.h"

typedef struct {
	char is_persistent;
	char *from_chars;
	char *to_chars;
	int tr_len;
} php_donie_filter_data;

static php_stream_filter_status_t php_donie_stream_filter(
	php_stream *stream, php_stream_filter *thisfilter,
	php_stream_bucket_brigade *buckets_in,
	php_stream_bucket_brigade *buckets_out,
	size_t *bytes_consumed, int flags TSRMLS_DC
) {
	php_donie_filter_data *data = thisfilter->abstract;
	php_stream_bucket *bucket;
	size_t consumed = 0;

	while(buckets_in->head) {
		bucket = php_stream_bucket_make_writeable(buckets_in->head TSRMLS_CC);
		php_strtr(bucket->buf, bucket->buflen, data->from_chars, data->to_chars, data->tr_len);
		consumed += bucket->buflen;
		php_stream_bucket_append(buckets_out, bucket TSRMLS_CC);
	}

	if (bytes_consumed) {
		*bytes_consumed = consumed;
	}

	return PSFS_PASS_ON;
}

static void php_donie_stream_filter_dtor(
	php_stream_filter *thisfilter TSRMLS_DC
) {
	php_donie_filter_data *data = thisfilter->abstract;
	pefree(data, data->is_persistent);
}

static php_stream_filter_ops php_donie_stream_filter_ops = {
	php_donie_stream_filter,
	php_donie_stream_filter_dtor,
	"donie.to_upper_case"
};

static php_stream_filter *php_donie_stream_filter_create(
	const char *name, zval *param, int persistent TSRMLS_DC
) {
	php_donie_filter_data *data;

	data = pemalloc(sizeof(php_donie_filter_data), persistent);
	if (!data) {
		return NULL;
	}
	data->is_persistent = persistent;
	data->from_chars = "abcdefghijklmnopqrstuvwxyz";
	data->to_chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	data->tr_len = strlen(data->from_chars);

	return php_stream_filter_alloc(&php_donie_stream_filter_ops, data, persistent);
}

static php_stream_filter_factory php_donie_stream_uppercase_factory = {
	php_donie_stream_filter_create
};

PHP_MINIT_FUNCTION(donie)
{
	/* register a filter */
	php_stream_filter_register_factory("donie.to_upper_case", &php_donie_stream_uppercase_factory TSRMLS_CC);

	return SUCCESS;
}

PHP_MSHUTDOWN_FUNCTION(donie)
{
	/* unregister the filter */
	php_stream_filter_unregister_factory("donie.to_upper_case" TSRMLS_CC);

	return SUCCESS;
}
```

### 注册和注销

分别在MINIT和MSHUTDOWN函数中调用php_stream_filter_register_factory()和php_stream_filter_unregister_factory()注册和注销过滤器。

### 过滤器的执行过程

当过滤器被调用时，调用方将使用php_donie_stream_filter_create()函数创建过滤器的实例。该函数在被执行时初始化过滤器的相关数据，并指定过滤器的相关操作。

调用方然后将过滤器实例添加到对应的流的读过滤器链或写过滤器链中，当流中发生读或写的操作时，过滤器链将数据放到一或多个php_stream_bucket结构中，并传递给过滤器处理。

### 业务逻辑

结构体php_donie_stream_filter_ops指定了过滤器的名称和相关业务逻辑。php_donie_stream_filter_ops->php_donie_stream_filter_dtor是过滤器的析构函数。php_donie_stream_filter_ops->php_donie_stream_filter是过滤器的主要业务逻辑。

在php_donie_stream_filter()中，函数接收一个php_stream_bucket链表buckets_in，并将处理后的php_stream_bucket追加到链表buckets_out中输出。

php_stream_bucket_make_writeable()将一个bucket从链表中移除，如果必要，复制其内部缓冲数据，以获得对内容的写权限。此外，对bucket的相关操作还有：

```c
php_stream_bucket *php_stream_bucket_new(php_stream *stream, char *buf, size_t buflen, int own_buf, int buf_persistent TSRMLS_DC);

int php_stream_bucket_split(php_stream_bucket *in, php_stream_bucket **left, php_stream_bucket **right, size_t length TSRMLS_DC);

void php_stream_bucket_delref(php_stream_bucket *bucket TSRMLS_DC);
void php_stream_bucket_addref(php_stream_bucket *bucket);

void php_stream_bucket_prepend(php_stream_bucket_brigade *brigade, php_stream_bucket *bucket TSRMLS_DC);
void php_stream_bucket_append(php_stream_bucket_brigade *brigade, php_stream_bucket *bucket TSRMLS_DC);

void php_stream_bucket_unlink(php_stream_bucket *bucket TSRMLS_DC);
```

若过滤器已准备好输出的数据，返回PSFS_PASS_ON；若还需要更多数据才能结束过滤操作，返回PSFS_FEED_ME；若遇到严重问题，返回PSFS_ERR_FATAL。
