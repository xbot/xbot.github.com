---
layout: post
title: "PHP流的操作"
date: 2015-06-01 15:33
comments: true
categories: 計算機
tags:
- PHP
- 源碼
---

## 實現

{% codeblock lang:c %}
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
{% endcodeblock %}

php_stream_open_wrapper()是對文件類型資源創建流的方法，此外還有基於socket的流、目錄流和特殊流三種。php_stream_to_zval()用於把流實例轉換成zval結構。

## 創建文件類型的流

{% codeblock lang:c %}
#define php_stream_open_wrapper(path, mode, options, opened)	_php_stream_open_wrapper_ex((path), (mode), (options), (opened), NULL STREAMS_CC TSRMLS_CC)
#define php_stream_open_wrapper_ex(path, mode, options, opened, context)	_php_stream_open_wrapper_ex((path), (mode), (options), (opened), (context) STREAMS_CC TSRMLS_CC)
{% endcodeblock %}

參數path是文件名或URL，mode是模式字符串，options是選項組合。php_stream_open_wrapper_ex()允許指定一個流的上下文。

options包含以下選項：

  - USE_PATH：應用ini中的include_path到相對路徑。內建的fopen()的第三個參數置True時使用此選項。
  - STREAM_USE_URL：只有遠程URL才允許打開，%%file://, php://, compress.zlib://%%這樣的本地URL會報錯。
  - ENFORCE_SAFE_MODE：只有設置了此選項且ini中的safe_mode開啟時，才會使safe_mode生效，不設置此選項，則不論ini中是否開啟都不會生效。
  - REPORT_ERRORS：若開啟流出錯，生成錯誤信息。
  - STREAM_MUST_SEEK：不是所有流都允許seek，若置此選項，且流不允許seek，則包裝器不會開啟流。
  - STREAM_WILL_CAST：置此參數將要求流可被轉換成posix或stdio類型的文件描述符，若流不可轉換，可在IO開始前失敗。
  - STREAM_ONLY_GET_HEADERS：http包裝器使用此參數，只獲取資源的元數據，不獲取內容。
  - STREAM_DISABLE_OPEN_BASEDIR：當ini中的open_basedir開啟時，置此參數跳過open_basedir檢查。
  - STREAM_OPEN_PERSISTENT：要求流和相關資源都創建為持久數據。
  - IGNORE_PATH：不從include_path中搜索。
  - IGNORE_URL：只有本地文件才可以被打開。

## 創建傳輸類型的流

{% codeblock lang:c %}
php_stream *_php_stream_xport_create(const char *name, size_t namelen, int options, int flags, const char *persistent_id, struct timeval *timeout, php_stream_context *context, char **error_string, int *error_code)
{% endcodeblock %}

參數：

  - name：URL。
  - options：參數，與php_stream_open_wrapper()的相同。
  - flags：STREAM_XPORT_CLIENT或STREAM_XPORT_SERVER與其它STREAM_XPORT_*常量的組合。
  - persistent_id：鍵值，置此參數將使流在多次請求間持久存在。
  - timeout：置NULL將使用ini中設置的值。
  - errstr：用於向外傳遞錯誤信息，初始應置為NULL，若有錯誤信息傳出，調用方有責任釋放錯誤信息佔用的內存。
  - errcode：錯誤碼。

flags：

  - STREAM_XPORT_CLIENT：工作為客戶端，向遠程發起連接。
  - STREAM_XPORT_SERVER：工作為服務器，接受連接。
  - STREAM_XPORT_CONNECT：傳輸建立的同時發起對遠程的連接，否則，需手動調用php_stream_xport_connect()。
  - STREAM_XPORT_CONNECT_ASYNC：發起異步遠程連接。
  - STREAM_XPORT_BIND：将传输流绑定到本地资源. 用在服务端传输流时,这将使得accept连接的传输流准备端口, 路径或特定的端点标识符等信息。
  - STREAM_XPORT_LISTEN：%%Listen for inbound connections on the bound transport endpoint. This is typically used with stream-based transports such as tcp://, ssl://, and unix://%%.

## 創建目錄類型的流

{% codeblock lang:c %}
php_stream php_stream_opendir(const char *path, int options, php_stream_context *context)
{% endcodeblock %}


## 創建特殊類型的流

{% codeblock lang:c %}
php_stream *php_stream_fopen_tmpfile(void);
php_stream *php_stream_fopen_temporary_file(const char *dir, const char *pfx, char **opened_path);
php_stream *php_stream_fopen_from_fd(int fd, const char *mode, const char *persistent_id);
php_stream *php_stream_fopen_from_file(FILE *file, const char *mode);
php_stream *php_stream_fopen_from_pipe(FILE *file, const char *mode);
{% endcodeblock %}

## 讀流

{% codeblock lang:c %}
// 讀一個字符
int php_stream_getc(php_stream *stream);

// 讀取指定數量的字符
size_t php_stream_read(php_stream *stream, char *buf, size_t count);

// 讀取直到行末、或流末、或最多maxlen個字符
char *php_stream_get_line(php_stream *stream, char *buf, size_t maxlen, size_t *returned_len);
char *php_stream_gets(php_stream *stream, char *buf, size_t maxlen);

// 與php_stream_get_line相同，可指定截止標記
char *php_stream_get_record(php_stream *stream, size_t maxlen, size_t *returned_len, char *delim, size_t delim_len TSRMLS_DC);

// 讀取一個目錄項
php_stream_dirent *php_stream_readdir(php_stream *dirstream, php_stream_dirent *entry);
{% endcodeblock %}

## 寫流

{% codeblock lang:c %}
// 寫非阻塞流可能寫入的數據比傳入的短；_string要求傳入的字符串以NULL結尾
size_t php_stream_write(php_stream *stream, char *buf, size_t count);
size_t php_stream_write_string(php_stream *stream, char *stf);

int php_stream_putc(php_stream *stream, int c);
// 與_string不同的是會自動追加一個換行符到字符串末尾
int php_stream_puts(php_string *stream, char *buf);

size_t php_stream_printf(php_stream *stream TSRMLS_DC, const char *format, ...);
{% endcodeblock %}

{% codeblock lang:c %}
int php_stream_flush(php_stream *stream);
{% endcodeblock %}

在關閉流的時候，flush會被自動調用，並且大部分無過濾的流因無內部緩沖而不需flush，所以單獨flush一般是不需要的。

## 尋址

{% codeblock lang:c %}
int php_stream_seek(php_stream *stream, off_t offset, int whence);
int php_stream_rewind(php_stream *stream);
int php_stream_rewinddir(php_stream *dirstream);
off_t php_stream_tell(php_stream *stream);
{% endcodeblock %}

offset是相對於whence的位移量，whence包含：

  - SEEK_SET：文件開頭。置offet為負值被認為是個錯誤並導致不可預料的行為。offset超出文件範圍會導致一個錯誤，或文件被增大。
  - SEEK_CUR：當前位置。
  - SEEK_END：文件末尾。offset一般為負，正值的行為因流的實現而異。

## 獲取額外信息

{% codeblock lang:c %}
int php_stream_stat(php_stream *stream, php_stream_statbuf *ssb);
{% endcodeblock %}

## 關閉流

{% codeblock lang:c %}
#define php_stream_close(stream) php_stream_free((stream), PHP_STREAM_FREE_CLOSE)
#define php_stream_pclose(stream) php_stream_free((stream), PHP_STREAM_FREE_CLOSE_PERSISTENT)
{% endcodeblock %}

包含以下選項：

  - PHP_STREAM_FREE_CALL_DTOR：銷毀流時調用php_stream->ops->close
  - PHP_STREAM_FREE_RELEASE_STREAM：銷毀流時調用php_stream_wrapper->ops->stream_close
  - PHP_STREAM_FREE_PRESERVE_HANDLE：php_stream->ops->close不銷毀句柄
  - PHP_STREAM_FREE_RSRC_DTOR：用於流內部資源列表垃圾回收
  - PHP_STREAM_FREE_PERSISTENT：用於持久流，所有操作的結果在多次請求間持久有效
  - PHP_STREAM_FREE_CLOSE：CALL_DTOR和RELEASE_STREAM的組合，用於非持久流的常規選項
  - PHP_STREAM_FREE_CLOSE_CASTED：CLOSE和PRESERVE_HANDLE的組合
  - PHP_STREAM_FREE_CLOSE_PERSISTENT：CLOSE和PERSISTENT的組合，用於持久流的常規選項
