---
layout: post
title: "使用存取控制表在Linux用户间共享文件"
date: 2014-03-19 22:38:00
comments: true
categories:
- 计算机
tags:
- Linux
- CLI
---

我用Dropbox在两台电脑间同步个人维基数据，将数据目录从Web Server下软连接到Dropbox里，而对Dropbox目录的备份实际上只包含那个软连接，没有内容，结果当把备份拷贝到另一台电脑上并打开Dropbox后，维基数据被清空了！我积累多年的笔记差一点儿完蛋，幸亏单独备份过维基。然后改将维基数据放到Dropbox里，然后软连接到Web Server下，新问题出现了，Web Server是以http身份运行的，对用户主目录没有权限，当然也不能访问主目录下的Dropbox目录。

最简单的办法是将主目录、Dropbox、维基目录的权限全部设成777，显然，这样做太不安全。另一种方法是把Dropbox用NFS输出，然后挂载到Web Server下，这么做太蛋疼。最合适的解决方案是Access Control List（存取控制表），它可以为文件和目录设置具体到单个用户或用户组的存取权限，实现像Windows下的文件（目录）共享权限设置那样的效果，而且比后者更强大、灵活。

使用ACL首先需要目录的挂载选项中包含acl，不过一般缺省都包含这一项。

ACL包含两个命令：getfacl和setfacl，前者用来查看目录或文件的存取控制表，后者用来操作它。

首先，把维基目录的所有者改成http，并设置目录权限为770：

```bash
chown -R http:http ~/Dropbox/wiki
chmod -R 770 ~/Dropbox/wiki
```

这时Web Server还是不能访问维基目录，使用getfacl查看用户主目录的ACL：

```bash
getfacl ~
```

显示结果如下：

>getfacl: Removing leading '/' from absolute path names  
\# file: home/taoqi  
\# owner: taoqi  
\# group: users  
user::rwx  
user:root:--x  
group::---  
mask::--x  
other::---  

显然，要给http用户访问该目录的权限：

```bash
setfacl -m u:http:x ~
```

再查看ACL，发现增加了一条：

>user:http:--x

同理，给Dropbox目录也加上这一条规则之后，Web Server就可以访问维基数据目录了。
