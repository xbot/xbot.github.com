---
title: 轻松管理 Docker 下的 MariaDB 错误日志：重定向到日志控制台和日志轮转
slug: steps to implement redirection and rotation for error logs of docker deployed mariadb
date: 2024-01-15T16:48:09+08:00
categories:
  - 计算机
tags:
  - mariadb
  - MySQL
  - docker
  - logrotate
  - 日志轮转
toc: false
draft: false
---

本文旨在通过把 MariaDB 的错误日志重定向到 Docker 的错误控制台以实现通过 Loki 收集日志，并使用日志轮转控制日志文件大小。

# 指定固定的日志文件名

```ini
[mariadb]
log_error=/config/databases/mariadb.err
```

# 把容器中的错误日志重定向到 Docker 日志控制台

在容器的环境变量中添加以下内容：
```ini
DOCKER_MODS=lscr.io/linuxserver/mods:universal-stdout-logs
LOGS_TO_STDOUT=/config/databases/mariadb.err
```

# 设置 MariaDB 的 root@localhost 用户的鉴权方式

创建 root@localhost 用户：

```sql
CREATE USER 'root'@'localhost' IDENTIFIED VIA 'unix_socket';
```

或更改已存在的 root@localhost 用户的鉴权方式：

```sql
ALTER USER 'root'@'localhost' IDENTIFIED VIA 'unix_socket';
```

# 受权 RELOAD 给 root@localhost 用户

```sql
GRANT RELOAD ON *.* TO 'root'@'localhost';`
```

# 配置 logrotate

在宿主机创建 `/etc/logrotate.d/mariadb`：

```bash
/mnt/user/appdata/mariadb/databases/mariadb.err {
        su nobody users
        missingok
        create 660 nobody users
        notifempty
        daily
        minsize 1M
        maxsize 100M
        rotate 30
        dateext
        dateformat .%Y-%m-%d-%H-%M-%S
        compress
        delaycompress
        sharedscripts 
        olddir ../archive/
        createolddir 770 nobody users
    postrotate
        # just if mysqld is really running
        if docker exec mariadb test -x /usr/bin/mysqladmin && \
           docker exec mariadb /usr/bin/mysqladmin ping &>/dev/null
        then
           docker exec mariadb /usr/bin/mysqladmin --local flush-error-log \
              flush-engine-log flush-general-log flush-slow-log
           docker exec mariadb sh -c '/bin/kill -HUP $(cat /var/run/mysqld/mysqld.pid)'
        fi
    endscript
}
```

可通过以下命令测试效果：

```bash
logrotate --force /etc/logrotate.d/mariadb`
```