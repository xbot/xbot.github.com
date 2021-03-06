# 用Tiny Tiny RSS搭建私人阅读器的步骤

## 优势
1. 自定义过滤器
2. 全功能，无限制
3. 利用已有VPS，无需额外费用

## 安装
### 安装并启动docker

```bash
curl https://get.docker.com/ | sh

// centos7
systemctl start docker
```

### 安装postgre

```bash
docker run -d --name ttrssdb nornagon/postgres
```

### 安装tiny tiny rss

```bash
docker run -d --link ttrssdb:db -p 80:80 -e SELF_URL_PATH=http://example.org/ttrss fischerman/docker-ttrss
```

`example.org`替换成VPS的IP或者对应的域名。

## 配置
### 配置主程序
访问`http://example.org/ttrss`，用户名`admin`，密码`password`。

### 伪装成fever
如果RSS阅读器不支持ttrss，但支持fever，例如reeder，可以通过安装插件伪装成fever：

```bash
git clone https://github.com/rannen/tinytinyrss-fever-plugin.git

docker cp fever [[CONTAINER ID]]:/var/www/plugins
```

然后去设置见面启用fever插件，并在fever插件的配置栏设置单独的密码，该栏目中会显示在RSS阅读器中使用的接口地址，用户名就是`admin`。

## 备份
每天凌晨3点备份数据库到dropbox。

### 下载dropbox上传脚本
在VPS的`/root`下执行：

```bash
wget https://raw.github.com/andreafabrizi/Dropbox-Uploader/master/dropbox_uploader.sh
```

执行命令并按提示操作：

```bash
./dropbox_uploader.sh info
```

### 创建备份脚本
创建`/root/backup.sh`：

```bash
#!/bin/bash

SCRIPT_DIR="/root"
NOW=$(date +"%Y%m%d")
TMP_PATH='/tmp'
DOCKER_ID_TTRSS='39cec6a7xcb7'
TTRSS_DB="$TMP_PATH/ttrss.sql"
BAK_FILE_NAME="vps-$NOW.tar.gz"
BAK_FILE="$TMP_PATH/$BAK_FILE_NAME"
DROPBOX_DIR=""

docker exec "$DOCKER_ID_TTRSS" /usr/bin/pg_dump ttrss > "$TTRSS_DB"
echo "数据库备份完成，打包网站数据中..."
tar cfzP "$BAK_FILE" "$TTRSS_DB"
echo "所有数据打包完成，准备上传..."
# 用脚本上传到dropbox
"$SCRIPT_DIR"/dropbox_uploader.sh upload "$BAK_FILE" "$DROPBOX_DIR/$BAK_FILE_NAME"
if [ $? -eq 0 ];then
     echo "上传完成"
else
     echo "上传失败，重新尝试"
fi

# 删除本地的临时文件
rm -f "$TTRSS_DB" "$BAK_FILE"
```

`39cec6a7xcb7`替换成实际的postgresql容器的ID。

### 创建定时任务
在`crontab -e `里添加：

```
0 3 * * * /bin/bash /root/backup.sh > /dev/null
```


