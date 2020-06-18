# MySQL升级后执行mysql_upgrade

<p>最近把MySQL从5.1升级到5.5，之后创建存储过程时报如下错误：</p><blockquote><p>ERROR 1548 (HY000) at line 5: Cannot load from mysql.proc. The table is probably corrupted</p></blockquote><p>解决办法是执行如下命令：

```bash
mysql_upgrade -p
```

<a href="http://dev.mysql.com/doc/refman/5.0/en/mysql-upgrade.html" target="_blank">mysql_upgrade</a>是MySQL提供的一个指令，作用是检查MySQL中的各个表与当前版本的数据库是否匹配并尝试修复所有发现的问题。MySQL官方文档中推荐在每次升级后执行一下这个指令。</p>

