---
layout: post
title: "PHP流的上下文和過濾器的實現"
date: 2015-06-15 19:15
comments: true
categories: 計算機
tags:
- PHP
- 源碼
---

## 上下文的實現和應用

上下文包含流的選項和流的參數兩部分內容。

{% codeblock lang:c %}
php_stream_context *php_stream_context_alloc(void);
{% endcodeblock %}

流的選項是一系列鍵值對。

{% codeblock lang:c %}
int php_stream_context_set_option(php_stream_context *context, const char *wrappername, const char *optionname, zval *optionvalue);

int php_stream_context_get_option(php_stream_context *context, const char *wrappername, const char *optionname, zval ***optionvalue);
{% endcodeblock %}

流的參數目前只實現對流的事件的回調，php_stream_context->notifier存儲如下結構：

{% codeblock lang:c %}
typedef struct {
    php_stream_notification_func func;
    void (*dtor)(php_stream_notifier *notifier);
    void *ptr;
    int mask;
    size_t progress, progress_max;
} php_stream_notifier;
{% endcodeblock %}

回調函數的原型為：

{% codeblock lang:c %}
typedef void (*php_stream_notification_func)(php_stream_context *context,
		int notifycode, int severity,
		char *xmsg, int xcode,
		size_t bytes_sofar, size_t bytes_max,
		void * ptr TSRMLS_DC);
{% endcodeblock %}

notifycode包含如下：

  - PHP_STREAM_NOTIFY_RESOLVE：主機名解析完成
  - PHP_STREAM_NOTIFY_CONNECT：socket連接建立
  - PHP_STREAM_NOTIFY_AUTH_REQUIRED：需要驗證
  - PHP_STREAM_NOTIFY_MIME_TYPE_IS：遠程資源的MIME-Type可用
  - PHP_STREAM_NOTIFY_FILE_SIZE_IS：遠程資源的大小可用
  - PHP_STREAM_NOTIFY_REDIRECTED：原始地址被跳轉
  - PHP_STREAM_NOTIFY_PROGRESS：php_stream_notifier->progress和progress_max（可能的）已更新
  - PHP_STREAM_NOTIFY_COMPLETED：流中已無可用數據
  - PHP_STREAM_NOTIFY_FAILURE：請求失敗
  - PHP_STREAM_NOTIFY_AUTH_RESULT：遠程驗證已完成，並且可能是成功的

severity包含如下：

  - PHP_STREAM_NOTIFY_SEVERITY_INFO
  - PHP_STREAM_NOTIFY_SEVERITY_WARN
  - PHP_STREAM_NOTIFY_SEVERITY_ERR

php_stream_notifier->ptr用於存儲附加數據，如果該數據需要手工回收，需指定php_stream_notifier->dtor。

php_stream_notifier->mask如果被賦值severity，其它severity的事件將不會觸發回調函數。


## 過濾器的實現和應用

{% codeblock lang:c %}
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
{% endcodeblock %}

### 註冊和註銷

分別在MINIT和MSHUTDOWN函數中調用php_stream_filter_register_factory()和php_stream_filter_unregister_factory()註冊和註銷過濾器。

### 過濾器的執行過程

當過濾器被調用時，調用方將使用php_donie_stream_filter_create()函數創建過濾器的實例。該函數在被執行時初始化過濾器的相關數據，並指定過濾器的相關操作。

調用方然後將過濾器實例添加到對應的流的讀過濾器鏈或寫過濾器鏈中，當流中發生讀或寫的操作時，過濾器鏈將數據放到一或多個php_stream_bucket結構中，並傳遞給過濾器處理。

### 業務邏輯

結構體php_donie_stream_filter_ops指定了過濾器的名稱和相關業務邏輯。php_donie_stream_filter_ops->php_donie_stream_filter_dtor是過濾器的析構函數。php_donie_stream_filter_ops->php_donie_stream_filter是過濾器的主要業務邏輯。

在php_donie_stream_filter()中，函數接收一個php_stream_bucket鏈表buckets_in，並將處理後的php_stream_bucket追加到鏈表buckets_out中輸出。

php_stream_bucket_make_writeable()將一個bucket從鏈表中移除，如果必要，複製其內部緩衝數據，以獲得對內容的寫權限。此外，對bucket的相關操作還有：

{% codeblock lang:c %}
php_stream_bucket *php_stream_bucket_new(php_stream *stream, char *buf, size_t buflen, int own_buf, int buf_persistent TSRMLS_DC);

int php_stream_bucket_split(php_stream_bucket *in, php_stream_bucket **left, php_stream_bucket **right, size_t length TSRMLS_DC);

void php_stream_bucket_delref(php_stream_bucket *bucket TSRMLS_DC);
void php_stream_bucket_addref(php_stream_bucket *bucket);

void php_stream_bucket_prepend(php_stream_bucket_brigade *brigade, php_stream_bucket *bucket TSRMLS_DC);
void php_stream_bucket_append(php_stream_bucket_brigade *brigade, php_stream_bucket *bucket TSRMLS_DC);

void php_stream_bucket_unlink(php_stream_bucket *bucket TSRMLS_DC);
{% endcodeblock %}

若過濾器已準備好輸出的數據，返回PSFS_PASS_ON；若還需要更多數據才能結束過濾操作，返回PSFS_FEED_ME；若遇到嚴重問題，返回PSFS_ERR_FATAL。
