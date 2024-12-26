---
layout: post
title: PHP流的操作
slug: stream operations of php
date: 2015-06-01 15:33:00
comments: true
tags:
- PHP
- 源码
- 计算机
---

## 实现

```c
/* reimplement fopen using stream */
ZEND_FUNCTION(donie_stream_fopen)
{
	php_stream *stream;
	char *path, *mode;
	int path_len, mode_len;
	int options = ENFORCE_SAFE_MODE|REPORT_ERRORS;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ss", &path, &path_len, &mode, &mode_len) == FAILURE)
	{
		return;
	}

	stream = php_stream_open_wrapper(path, mode, options, NULL);
	if (!stream)
	{
		RETURN_FALSE;
	}

	php_stream_to_zval(stream, return_value);
}
```

php_stream_open_wrapper()是对文件类型资源创建流的方法，此外还有基于socket的流、目录流和特殊流三种。php_stream_to_zval()用于把流实例转换成zval结构。

## 创建文件类型的流

```c
#define php_stream_open_wrapper(path, mode, options, opened)	_php_stream_open_wrapper_ex((path), (mode), (options), (opened), NULL STREAMS_CC TSRMLS_CC)
#define php_stream_open_wrapper_ex(path, mode, options, opened, context)	_php_stream_open_wrapper_ex((path), (mode), (options), (opened), (context) STREAMS_CC TSRMLS_CC)
```

参数path是文件名或URL，mode是模式字符串，options是选项组合。php_stream_open_wrapper_ex()允许指定一个流的上下文。

options包含以下选项：

  - USE_PATH：应用ini中的include_path到相对路径。内建的fopen()的第三个参数置True时使用此选项。
  - STREAM_USE_URL：只有远程URL才允许打开，%%file://, php://, compress.zlib://%%这样的本地URL会报错。
  - ENFORCE_SAFE_MODE：只有设置了此选项且ini中的safe_mode开启时，才会使safe_mode生效，不设置此选项，则不论ini中是否开启都不会生效。
  - REPORT_ERRORS：若开启流出错，生成错误信息。
  - STREAM_MUST_SEEK：不是所有流都允许seek，若置此选项，且流不允许seek，则包装器不会开启流。
  - STREAM_WILL_CAST：置此参数将要求流可被转换成posix或stdio类型的文件描述符，若流不可转换，可在IO开始前失败。
  - STREAM_ONLY_GET_HEADERS：http包装器使用此参数，只获取资源的元数据，不获取内容。
  - STREAM_DISABLE_OPEN_BASEDIR：当ini中的open_basedir开启时，置此参数跳过open_basedir检查。
  - STREAM_OPEN_PERSISTENT：要求流和相关资源都创建为持久数据。
  - IGNORE_PATH：不从include_path中搜索。
  - IGNORE_URL：只有本地文件才可以被打开。

## 创建传输类型的流

```c
php_stream *_php_stream_xport_create(const char *name, size_t namelen, int options, int flags, const char *persistent_id, struct timeval *timeout, php_stream_context *context, char **error_string, int *error_code)
```

参数：

  - name：URL。
  - options：参数，与php_stream_open_wrapper()的相同。
  - flags：STREAM_XPORT_CLIENT或STREAM_XPORT_SERVER与其它STREAM_XPORT_*常量的组合。
  - persistent_id：键值，置此参数将使流在多次请求间持久存在。
  - timeout：置NULL将使用ini中设置的值。
  - errstr：用于向外传递错误信息，初始应置为NULL，若有错误信息传出，调用方有责任释放错误信息占用的内存。
  - errcode：错误码。

flags：

  - STREAM_XPORT_CLIENT：工作为客户端，向远程发起连接。
  - STREAM_XPORT_SERVER：工作为服务器，接受连接。
  - STREAM_XPORT_CONNECT：传输建立的同时发起对远程的连接，否则，需手动调用php_stream_xport_connect()。
  - STREAM_XPORT_CONNECT_ASYNC：发起异步远程连接。
  - STREAM_XPORT_BIND：将传输流绑定到本地资源. 用在服务端传输流时,这将使得accept连接的传输流准备端口, 路径或特定的端点标识符等信息。
  - STREAM_XPORT_LISTEN：%%Listen for inbound connections on the bound transport endpoint. This is typically used with stream-based transports such as tcp://, ssl://, and unix://%%.

## 创建目录类型的流

```c
php_stream php_stream_opendir(const char *path, int options, php_stream_context *context)
```


## 创建特殊类型的流

```c
php_stream *php_stream_fopen_tmpfile(void);
php_stream *php_stream_fopen_temporary_file(const char *dir, const char *pfx, char **opened_path);
php_stream *php_stream_fopen_from_fd(int fd, const char *mode, const char *persistent_id);
php_stream *php_stream_fopen_from_file(FILE *file, const char *mode);
php_stream *php_stream_fopen_from_pipe(FILE *file, const char *mode);
```

## 读流

```c
// 读一个字符
int php_stream_getc(php_stream *stream);

// 读取指定数量的字符
size_t php_stream_read(php_stream *stream, char *buf, size_t count);

// 读取直到行末、或流末、或最多maxlen个字符
char *php_stream_get_line(php_stream *stream, char *buf, size_t maxlen, size_t *returned_len);
char *php_stream_gets(php_stream *stream, char *buf, size_t maxlen);

// 与php_stream_get_line相同，可指定截止标记
char *php_stream_get_record(php_stream *stream, size_t maxlen, size_t *returned_len, char *delim, size_t delim_len TSRMLS_DC);

// 读取一个目录项
php_stream_dirent *php_stream_readdir(php_stream *dirstream, php_stream_dirent *entry);
```

## 写流

```c
// 写非阻塞流可能写入的数据比传入的短；_string要求传入的字符串以NULL结尾
size_t php_stream_write(php_stream *stream, char *buf, size_t count);
size_t php_stream_write_string(php_stream *stream, char *stf);

int php_stream_putc(php_stream *stream, int c);
// 与_string不同的是会自动追加一个换行符到字符串末尾
int php_stream_puts(php_string *stream, char *buf);

size_t php_stream_printf(php_stream *stream TSRMLS_DC, const char *format, ...);
```

```c
int php_stream_flush(php_stream *stream);
```

在关闭流的时候，flush会被自动调用，并且大部分无过滤的流因无内部缓冲而不需flush，所以单独flush一般是不需要的。

## 寻址

```c
int php_stream_seek(php_stream *stream, off_t offset, int whence);
int php_stream_rewind(php_stream *stream);
int php_stream_rewinddir(php_stream *dirstream);
off_t php_stream_tell(php_stream *stream);
```

offset是相对于whence的位移量，whence包含：

  - SEEK_SET：文件开头。置offet为负值被认为是个错误并导致不可预料的行为。offset超出文件范围会导致一个错误，或文件被增大。
  - SEEK_CUR：当前位置。
  - SEEK_END：文件末尾。offset一般为负，正值的行为因流的实现而异。

## 获取额外信息

```c
int php_stream_stat(php_stream *stream, php_stream_statbuf *ssb);
```

## 关闭流

```c
#define php_stream_close(stream) php_stream_free((stream), PHP_STREAM_FREE_CLOSE)
#define php_stream_pclose(stream) php_stream_free((stream), PHP_STREAM_FREE_CLOSE_PERSISTENT)
```

包含以下选项：

  - PHP_STREAM_FREE_CALL_DTOR：销毁流时调用php_stream->ops->close
  - PHP_STREAM_FREE_RELEASE_STREAM：销毁流时调用php_stream_wrapper->ops->stream_close
  - PHP_STREAM_FREE_PRESERVE_HANDLE：php_stream->ops->close不销毁句柄
  - PHP_STREAM_FREE_RSRC_DTOR：用于流内部资源列表垃圾回收
  - PHP_STREAM_FREE_PERSISTENT：用于持久流，所有操作的结果在多次请求间持久有效
  - PHP_STREAM_FREE_CLOSE：CALL_DTOR和RELEASE_STREAM的组合，用于非持久流的常规选项
  - PHP_STREAM_FREE_CLOSE_CASTED：CLOSE和PRESERVE_HANDLE的组合
  - PHP_STREAM_FREE_CLOSE_PERSISTENT：CLOSE和PERSISTENT的组合，用于持久流的常规选项
