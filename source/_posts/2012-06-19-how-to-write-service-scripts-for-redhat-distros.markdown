---
layout: post
title: "怎样为红帽系Linux发行版写系统服务脚本"
date: 2012-06-19 00:14
comments: true
categories: 计算机
tags:
- Linux
- Redhat
- CentOS
- Bash
- 编程
---
##阅读说明

阅读本文要求有基本的Linux系统使用经验和Bash脚本编程能力。

本文所述的内容适用于RHEL、CentOS等红帽系Linux发行版。

##系统服务简介

###常用命令

{% codeblock lang:bash %}
# 启动MySQL
service mysqld start

# 关闭MySQL
service mysqld stop

# 重启MySQL
service mysqld restart

# 查看MySQL服务运行状态
service mysqld status

# 查看MySQL服务是否开机自动运行
chkconfig --list mysqld

# 设置MySQL服务开机自动运行
chkconfig --level 345 mysqld on

# 取消MySQL服务开机自动运行
chkconfig --level 345 mysqld off
{% endcodeblock %}

###运行级别

泛UNIX派系操作系统使用运行级别标识使用何种模式初始化。理论上，不同的发行版各级别代表的意义不同。红帽系发行版使用以下七个运行级别：

  - 0：关机
  - 1：单用户模式
  - 2：多用户模式，无网络服务
  - 3：多用户模式，有网络服务
  - 4：未使用/用户自定义
  - 5：带图形界面的多用户模式
  - 6：重启

最常用的是0、3、5、6四种模式。其中，0和6表示关闭和重启操作系统，所以，执行命令**init 0**和**shutdown**是一个效果，同样，执行命令**init 6**和**reboot**是一个效果。模式3即普通的命令行用户界面，模式5即普通的图形界面用户界面。

模式1在个别情景下会用到，例如忘记root用户的密码时，需要进入模式1修改密码。

##系统服务脚本格式

###示例

{% codeblock lang:bash %}
#!/bin/bash

# chkconfig: 345 99 12
# description: This is a sample service script

case "$1" in
    start)
        # Start something.
        touch /var/lock/subsys/sample
        ;;
    stop)
        # Stop something.
        /bin/rm -f /var/lock/subsys/sample
        ;;
    restart|reload)
        # Restart something.
        ;;
    status)
        # Report the running status of something.
        ;;
    *)
        # Invalid command, complain an error.
        ;;
esac

exit 0
{% endcodeblock %}

###解释

假设本服务脚本文件的文件名为**sample**。

整个服务脚本是一个普通的Bash脚本。主体是一个case语句，定义了四个命令start、stop、restart或reload、status，顾名思义，四个命令分别用来启动、停止、重启该服务，以及获取服务的运行状态。

红帽系发行版使用chkconfig命令设置服务自动在哪个运行级别被自动执行。要使服务脚本支持chkconfig命令，需要加入注释行：

{% codeblock lang:bash %}
# chkconfig: 345 99 12
{% endcodeblock %}

其中，**345**表示缺省的运行级别，使用如下命令添加服务时：

{% codeblock lang:bash %}
chkconfig --add sample
{% endcodeblock %}

将和使用如下命令一个效果：

{% codeblock lang:bash %}
chkconfig --level 345 sample on
{% endcodeblock %}

**99**表示启动序号，例如若希望服务B在服务A启动后启动，且服务A的启动序号是98，则服务B的启动序号应设为大于98的一个整数。

**12**表示停止序号，作用与启动序号相同。

此外，若希望在操作系统关闭前先自动执行服务的关闭命令，需要在**/var/lock/subsys/**目录中存在与服务名同名的空文件，因此，应在**start**命令中创建一个这样的文件，并在**stop**命令中删除之。
