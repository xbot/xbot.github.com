---
layout: post
title: "遷移到octopress"
date: 2012-06-23 23:13
comments: true
categories: 青梅煮酒
tags:
- 生活
- 牧碼志
- wordpress
- octopress
- github
- git
---
##關於遷移

前段時間，用了五年的虛擬主機突然限制了PHP內存上限，導致Wordpress只能啟用有限的幾個插件，根本不能滿足需要。

所以毅然決定遷移到JeckyII+GitHub，在摸索的過程中發現octopress比JeckyII易用，於是導出所有文章，開始遷移。因為我這幾年博客寫得比較亂，無論是內容上，還是發表方式上，結果用了幾個導出腳本，效果都不甚理想，還是有很多地方需要手工修改。六百篇文章，正好趁這個機會挑揀一下，較早的文章質量低的比較多。斷斷續續地遷移了一些，剩下的工作量還是很大的，留著以後慢慢來吧。另外這次把URL也改成永久格式了，這一來真的傷筋動骨了，連永久重定向都沒的做。

至於Feed輸出，以前訂閱Feedburner燒錄的兩個地址的讀者不受影響，直接訂閱Wordpress輸出的地址的就丟了，這裡再公佈一下：

- 本博客聚合輸出：http://feeds.feedburner.com/sinolog
- 我的全部資訊聚合：http://feeds.feedburner.com/leninlee

##遷移那點事

###Python版本的問題

Archlinux很激進，早已把Python的缺省版本進化到了3.x，octopress會用到2.x，結果在生成全站的時候，會報如下錯誤：

{% codeblock %}
  File "<string>", line 1
    import sys; print sys.executable
                        ^
SyntaxError: invalid syntax
{% endcodeblock %}

在[這裡](http://blog.dayanjia.com/2012/04/fix-rubypython-bug-in-arch-linux/)找到解決方法。在octopress的plugins目錄裡創建文件：

{% codeblock lang:ruby %}
require 'pygments'

if !!RUBY_PLATFORM['linux']
    RubyPython.configure :python_exe => '/usr/bin/python2'
end
{% endcodeblock %}

###不足

用了這段時間octopress，感覺缺點也不小，每次修改後都要重新生成全站，佔用系統資源不小，而且理論上以後會越來越慢。
