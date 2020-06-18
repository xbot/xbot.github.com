# Swoole与PHP-FPM性能对比


测试环境：

  - CPU: Intel(R) Core(TM) i5-3470 CPU @ 3.20GHz
  - MEM: 4G
  - OS:  Archlinux 64bit

测试命令：

```bash
ab -c 200 -n 200000 -k http://127.0.0.1/test
```

## NGINX + PHP-FPM

Requests per second:    16240.50 [#/sec] (mean)  
Time per request:       12.315 [ms] (mean)

## NGINX + Swoole

Requests per second:    31284.57 [#/sec] (mean)  
Time per request:       6.393 [ms] (mean)

## Swoole

Requests per second:    99926.55 [#/sec] (mean)  
Time per request:       2.001 [ms] (mean)

## 结论

对一个最简单的PHP脚本做测试，排除业务逻辑的消耗的影响。Swoole威武。

