---
layout: post
title: "怎樣為紅帽系Linux發行版寫系統服務腳本"
date: 2012-06-19 00:14
comments: true
categories: 計算機
tags:
- Linux
- Redhat
- CentOS
- Bash
- 編程
---
##閱讀說明

閱讀本文要求有基本的Linux系統使用經驗和Bash腳本編程能力。

本文所述的內容適用於RHEL、CentOS等紅帽系Linux發行版。

##系統服務簡介

###常用命令

{% codeblock lang:bash %}
# 啟動MySQL
service mysqld start

# 關閉MySQL
service mysqld stop

# 重啟MySQL
service mysqld restart

# 查看MySQL服務運行狀態
service mysqld status

# 查看MySQL服務是否開機自動運行
chkconfig --list mysqld

# 設置MySQL服務開機自動運行
chkconfig --level 345 mysqld on

# 取消MySQL服務開機自動運行
chkconfig --level 345 mysqld off
{% endcodeblock %}

###運行級別

泛UNIX派系操作系統使用運行級別標識使用何種模式初始化。理論上，不同的發行版各級別代表的意義不同。紅帽系發行版使用以下七個運行級別：

  - 0：關機
  - 1：單用戶模式
  - 2：多用戶模式，無網絡服務
  - 3：多用戶模式，有網絡服務
  - 4：未使用/用戶自定義
  - 5：帶圖形界面的多用戶模式
  - 6：重啟

最常用的是0、3、5、6四種模式。其中，0和6表示關閉和重啟操作系統，所以，執行命令**init 0**和**shutdown**是一個效果，同樣，執行命令**init 6**和**reboot**是一個效果。模式3即普通的命令行用戶界面，模式5即普通的圖形界面用戶界面。

模式1在個別情景下會用到，例如忘記root用戶的密碼時，需要進入模式1修改密碼。

##系統服務腳本格式

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

###解釋

假設本服務腳本文件的文件名為**sample**。

整個服務腳本是一個普通的Bash腳本。主體是一個case語句，定義了四個命令start、stop、restart或reload、status，顧名思義，四個命令分別用來啟動、停止、重啟該服務，以及獲取服務的運行狀態。

紅帽系發行版使用chkconfig命令設置服務自動在哪個運行級別被自動執行。要使服務腳本支持chkconfig命令，需要加入註釋行：

{% codeblock lang:bash %}
# chkconfig: 345 99 12
{% endcodeblock %}

其中，**345**表示缺省的運行級別，使用如下命令添加服務時：

{% codeblock lang:bash %}
chkconfig --add sample
{% endcodeblock %}

將和使用如下命令一個效果：

{% codeblock lang:bash %}
chkconfig --level 345 sample on
{% endcodeblock %}

**99**表示啟動序號，例如若希望服務B在服務A啟動後啟動，且服務A的啟動序號是98，則服務B的啟動序號應設為大於98的一個整數。

**12**表示停止序號，作用與啟動序號相同。

此外，若希望在操作系統關閉前先自動執行服務的關閉命令，需要在**/var/lock/subsys/**目錄中存在與服務名同名的空文件，因此，應在**start**命令中創建一個這樣的文件，並在**stop**命令中刪除之。
