# 批量下载视频的BASH脚本

两周前为了批量下载某视频网站中的电视剧，写了个BASH脚本。将电视剧列表页面的URL地址作为唯一参数传给脚本，然后就会把所有视频下载到当前目录下，并自动重命名，同时生成一个M3U格式的播放列表。

由于在线视频不支持断点续传，所以对于单个视频来说无法实现。但对于整个批量下载任务来说，实现了宏观上的断点续传，已经下载的视频不会被重复下载。由于有些视频网站会在午夜更改视频地址，所以这一点很有用。

主流视频网站应该是都支持的，我只测试了我下载电视剧的网站，<strong>不支持的都是非主流的！</strong>

唯一可能需要注意的依赖是PHP，必须安装后才能使用。

```bash
#!/bin/bash

print_help_msg () {
    echo "You see, I'm nothing ."
    exit 0
}

check_param () {
    if [ $# -ne 1 ]; then
        print_help_msg
    fi
}

check_m3u () {
    if ! [ -a p.m3u ] || [ `wc -l p.m3u|awk '{ print $1 }'` -eq 0 ]; then
        echo '#EXTM3U' > p.m3u
    fi
}

check_param $*
check_m3u

export LC_ALL=en_US.UTF-8

ue=$(php -r "echo urlencode('$1');")
parser="http://www.flvcd.com/parse.php?flag=&format=&kw=$ue&sbt=%BF%AA%CA%BCGO%21"
if ! wget $parser -U mozilla -O meta.html ; then
    echo "Unable to touch the parser, check network status for the cause ."
    exit 0
fi

grep "<N>" meta.html > title.lst
grep "<U>" meta.html > url.lst
iconv -f gbk -t utf-8 title.lst -o title.lst
sed -i 's/<N>//g' title.lst
sed -i 's/ //g' title.lst
sed -i 's/<U>//g' url.lst

l1=`wc -l title.lst|awk '{ print $1 }'`
l2=`wc -l url.lst|awk '{ print $1 }'`
if [ "$l1" != "$l2" ]; then
    echo "Title.lst has $l1 lines, but url.lst got $l2."
    exit 0
fi
if [ $l1 -eq 0 ]; then
    echo "Nothing got from the parser, check meta.html for detail info."
    exit 0
fi

arrTitle=(`cat title.lst`)
arrURL=(`cat url.lst`)

idx=$((`wc -l p.m3u|awk '{ print $1 }'`-1))
while [ $idx -lt $l1 ]; do
    title=${arrTitle[$idx]}
    url=${arrURL[$idx]}
    idx=$((idx+1))
    if ! wget $url -U mozilla -O "${title}.flv" ; then
        echo "Failed fetching ${title}.flv, maybe its URL has been changed !"
        exit 0
    fi
    cmd="sed -i '\$a\\${title}.flv' p.m3u"
    eval $cmd
done

echo 'done !'
exit 0
```

另外，使用VLC执行播放列表效果灰常不错，视频之间衔接平滑流畅。

