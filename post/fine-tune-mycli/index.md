# 调校mycli


做以下配置，使mycli按需使用pager，并在数据过多时不破坏表格格式：

```ini
# ~/.my.cnf

[client]
pager = less -FSXR

```

