---
layout: post
title: "使用存取控制表在Linux用戶間共享文件"
date: 2014-03-19 22:38
comments: true
categories: 計算機
tags:
- Linux
- CLI
---

我用Dropbox在兩台電腦間同步個人維基數據，將數據目錄從Web Server下軟連接到Dropbox裡，而對Dropbox目錄的備份實際上只包含那個軟連接，沒有內容，結果當把備份拷貝到另一台電腦上並打開Dropbox後，維基數據被清空了！我積累多年的筆記差一點兒完蛋，幸虧單獨備份過維基。然後改將維基數據放到Dropbox裡，然後軟連接到Web Server下，新問題出現了，Web Server是以http身份運行的，對用戶主目錄沒有權限，當然也不能訪問主目錄下的Dropbox目錄。

最簡單的辦法是將主目錄、Dropbox、維基目錄的權限全部設成777，顯然，這樣做太不安全。另一種方法是把Dropbox用NFS輸出，然後掛載到Web Server下，這麼做太蛋疼。最合適的解決方案是Access Control List（存取控制表），它可以為文件和目錄設置具體到單個用戶或用戶組的存取權限，實現像Windows下的文件（目錄）共享權限設置那樣的效果，而且比後者更強大、靈活。

使用ACL首先需要目錄的掛載選項中包含acl，不過一般缺省都包含這一項。

ACL包含兩個命令：getfacl和setfacl，前者用來查看目錄或文件的存取控制表，後者用來操作它。

首先，把維基目錄的所有者改成http，並設置目錄權限為770：

{% codeblock lang:bash %}
chown -R http:http ~/Dropbox/wiki
chmod -R 770 ~/Dropbox/wiki
{% endcodeblock %}

這時Web Server還是不能訪問維基目錄，使用getfacl查看用戶主目錄的ACL：

{% codeblock lang:bash %}
getfacl ~
{% endcodeblock %}

顯示結果如下：

>getfacl: Removing leading '/' from absolute path names  
\# file: home/taoqi  
\# owner: taoqi  
\# group: users  
user::rwx  
user:root:--x  
group::---  
mask::--x  
other::---  

顯然，要給http用戶訪問該目錄的權限：

{% codeblock lang:bash %}
setfacl -m u:http:x ~
{% endcodeblock %}

再查看ACL，發現增加了一條：

>user:http:--x

同理，給Dropbox目錄也加上這一條規則之後，Web Server就可以訪問維基數據目錄了。
