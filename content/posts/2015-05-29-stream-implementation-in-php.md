---
layout: post
title: "PHP流的实现"
date: 2015-05-29 17:33:00
comments: true
categories:
- 计算机
tags:
- PHP
- 源码
---

## 流的概念

流是一系列概念的集合，包括流包装器、流资源、流操作、上下文等内容。流是对不同资源进行操作的抽象，允许线性地从指定位置读取或写入数据，通过一套统一的API简化对资源操作的实现。

流由scheme://target指代，scheme是包装器（Wrapper）的名字，target是流的目标地址。

PHP的流的实现较Java简单，后者可以通过嵌套实现更灵活的应用。

## 流的实现

### 存储结构

```c
struct _php_stream  {
	php_stream_ops *ops;
	void *abstract;			/* convenience pointer for abstraction */
	php_stream_filter_chain readfilters, writefilters;
	php_stream_wrapper *wrapper; /* which wrapper was used to open the stream */
	void *wrapperthis;		/* convenience pointer for a instance of a wrapper */
	zval *wrapperdata;		/* fgetwrapperdata retrieves this */
	int fgetss_state;		/* for fgetss to handle multiline tags */
	int is_persistent;
	char mode[16];			/* "rwb" etc. ala stdio */
	int rsrc_id;			/* used for auto-cleanup */
	int in_free;			/* to prevent recursion during free */
	/* so we know how to clean it up correctly.  This should be set to
	 * PHP_STREAM_FCLOSE_XXX as appropriate */
	int fclose_stdiocast;
	FILE *stdiocast;    /* cache this, otherwise we might leak! */
#if ZEND_DEBUG
	int __exposed;	/* non-zero if exposed as a zval somewhere */
#endif
	char *orig_path;
	php_stream_context *context;
	int flags;	/* PHP_STREAM_FLAG_XXX */
	/* buffer */
	off_t position; /* of underlying stream */
	unsigned char *readbuf;
	size_t readbuflen;
	off_t readpos;
	off_t writepos;
	/* how much data to read when filling buffer */
	size_t chunk_size;
	int eof;
#if ZEND_DEBUG
	const char *open_filename;
	uint open_lineno;
#endif
	struct _php_stream *enclosing_stream; /* this is a private stream owned by enclosing_stream */
}; /* php_stream */

typedef struct _php_stream_ops  {
	/* stdio like functions - these are mandatory! */
	size_t (*write)(php_stream *stream, const char *buf, size_t count TSRMLS_DC);
	size_t (*read)(php_stream *stream, char *buf, size_t count TSRMLS_DC);
	int    (*close)(php_stream *stream, int close_handle TSRMLS_DC);
	int    (*flush)(php_stream *stream TSRMLS_DC);
	const char *label; /* label for this ops structure */
	/* these are optional */
	int (*seek)(php_stream *stream, off_t offset, int whence, off_t *newoffset TSRMLS_DC);
	int (*cast)(php_stream *stream, int castas, void **ret TSRMLS_DC);
	int (*stat)(php_stream *stream, php_stream_statbuf *ssb TSRMLS_DC);
	int (*set_option)(php_stream *stream, int option, int value, void *ptrparam TSRMLS_DC);
} php_stream_ops;
```

php_stream结构体最重要的成员是ops和abstract。ops包含了流实例的所有操作逻辑，特别地，php_stream_ops->close在php_stream结构被回收前提供了回收与该流实例相关的资源的机会。abstract用来存储一个自定义结构的数据，在流的操作逻辑里可以方便的访问。

### 实现

```c
#define PHP_DONIESTREAM_STREAMTYPE "doniestream"

static size_t php_doniestream_write(php_stream *stream, const char *buf, size_t count TSRMLS_DC)
{
	donie_stream_data *data = stream->abstract;

	php_printf("Write to stream: %s\n", buf);

	return count;
}
static size_t php_doniestream_read(php_stream *stream, char *buf, size_t count TSRMLS_DC)
{
	donie_stream_data *data = stream->abstract;
	zval **val;
	size_t read_size = count;

	php_printf("Read from stream: %s\n", data->key);

	return read_size;
}
static int php_doniestream_close(php_stream *stream, int close_handle TSRMLS_DC)
{
	donie_stream_data *data = stream->abstract;
	efree(data->key);
	efree(data);
	return 0;
}
static php_stream_ops php_doniestream_ops = {
	php_doniestream_write,
	php_doniestream_read,
	php_doniestream_close,
	NULL, /* flush */
	PHP_DONIESTREAM_STREAMTYPE,
	NULL, /* seek */
	NULL, /* cast */
	NULL, /* stat */
	NULL, /* set_option */
};
```

主要是流的操作逻辑的实现，最后构建的php_stream_ops结构用于后面流包装器中初始化流实例时赋给php_stream->ops。

## 包装器的实现

Wrapper是对某一协议的封装，主要包含对该类型的流的一系列操作逻辑的实现。

### 存储结构

```c
struct _php_stream_wrapper	{
	php_stream_wrapper_ops *wops;	/* operations the wrapper can perform */
	void *abstract;			/* context for the wrapper */
	int is_url;			/* so that PG(allow_url_fopen) can be respected */
};

typedef struct _php_stream_wrapper_ops {
	/* open/create a wrapped stream */
	php_stream *(*stream_opener)(php_stream_wrapper *wrapper, const char *filename, const char *mode, int options, char **opened_path, php_stream_context *context STREAMS_DC TSRMLS_DC);
	/* close/destroy a wrapped stream */
	int (*stream_closer)(php_stream_wrapper *wrapper, php_stream *stream TSRMLS_DC);
	/* stat a wrapped stream */
	int (*stream_stat)(php_stream_wrapper *wrapper, php_stream *stream, php_stream_statbuf *ssb TSRMLS_DC);
	/* stat a URL */
	int (*url_stat)(php_stream_wrapper *wrapper, const char *url, int flags, php_stream_statbuf *ssb, php_stream_context *context TSRMLS_DC);
	/* open a "directory" stream */
	php_stream *(*dir_opener)(php_stream_wrapper *wrapper, const char *filename, const char *mode, int options, char **opened_path, php_stream_context *context STREAMS_DC TSRMLS_DC);
	const char *label;
	/* delete a file */
	int (*unlink)(php_stream_wrapper *wrapper, const char *url, int options, php_stream_context *context TSRMLS_DC);
	/* rename a file */
	int (*rename)(php_stream_wrapper *wrapper, const char *url_from, const char *url_to, int options, php_stream_context *context TSRMLS_DC);
	/* Create/Remove directory */
	int (*stream_mkdir)(php_stream_wrapper *wrapper, const char *url, int mode, int options, php_stream_context *context TSRMLS_DC);
	int (*stream_rmdir)(php_stream_wrapper *wrapper, const char *url, int options, php_stream_context *context TSRMLS_DC);
	/* Metadata handling */
	int (*stream_metadata)(php_stream_wrapper *wrapper, const char *url, int options, void *value, php_stream_context *context TSRMLS_DC);
} php_stream_wrapper_ops;
```

php_stream_wrapper中最重要的是ops成员，它包含了所有该类型的流的操作逻辑的实现。其中最重要的是stream_opener和stream_closer，前者是流的实例化逻辑，后者是流的析构逻辑。特别的，stream_closer主要是用来销毁php_stream结构，而php_stream_ops->close是用来回收所有和该流实例相关的资源。

### 实现

```c
#define PHP_DONIESTREAM_WRAPPER "donie"
typedef struct _donie_stream_data {
	off_t position;
	char *key;
	int key_len;
} donie_stream_data;

static php_stream *php_doniestream_wrapper_open(
		php_stream_wrapper *wrapper,
		const char *filename, const char *mode, int options,
		char **opened_path, php_stream_context *context
		STREAMS_DC TSRMLS_DC)
{
	donie_stream_data *data;
	php_url *url;

	if (options & STREAM_OPEN_PERSISTENT)
	{
		php_stream_wrapper_log_error(wrapper, options TSRMLS_CC, "Unable to open %s persistently.", filename);
		return NULL;
	}

	url = php_url_parse(filename);
	if (!url)
	{
		php_stream_wrapper_log_error(wrapper, options TSRMLS_CC, "Unable to parse url %s.", filename);
		return NULL;
	}

	data = emalloc(sizeof(donie_stream_data));
	data->position = 0;
	data->key_len = strlen(url->host);
	data->key = estrndup(url->host, data->key_len+1);
	php_url_free(url);

	return php_stream_alloc(&php_doniestream_ops, data, 0, mode);
}
static php_stream_wrapper_ops php_doniestream_wrapper_ops = {
	php_doniestream_wrapper_open,
	NULL, /* stream_closer */
	NULL, /* stream_stat */
	NULL, /* url_stat */
	NULL, /* dir_opener */
	PHP_DONIESTREAM_WRAPPER,
	NULL, /* unlink */
	NULL, /* rename */
	NULL, /* mkdir */
	NULL, /* rmdir */
	NULL  /* stream_metadata */
};
static php_stream_wrapper php_doniestream_wrapper = {
	&php_doniestream_wrapper_ops,
	NULL, /* abstract */
	0, /* is_url */
};

PHP_MINIT_FUNCTION(donie)
{
	/* register stream wrapper */
	if (php_register_url_stream_wrapper(PHP_DONIESTREAM_WRAPPER, &php_doniestream_wrapper TSRMLS_CC) == FAILURE)
	{
		return FAILURE;
	}

	return SUCCESS;
}

PHP_MSHUTDOWN_FUNCTION(donie)
{
	/* unregister stream wrapper */
	if (php_unregister_url_stream_wrapper(PHP_DONIESTREAM_WRAPPER TSRMLS_CC) == FAILURE)
	{
		return FAILURE;
	}

	return SUCCESS;
}
```

PHP_DONIESTREAM_WRAPPER定义了协议名“donie”，所有对格式为“donie://XXX”地址的操作将由这个流实现。

donie_stream_data是一个自定义的结构体，在创建流实例的时候初始化并赋给php_stream->abstract，为以后对流的操作提供方便。

这里只实现了最关键的stream_opener函数，其中，用php_stream_alloc()创建新的流实例。

最后在模块的MINIT中用php_register_url_stream_wrapper()注册包装器，并在MSHUTDOWN中用php_unregister_url_stream_wrapper()注销。
