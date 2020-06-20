# 迁移到 Octopress


## 关于迁移

前段时间，用了五年的虚拟主机突然限制了PHP内存上限，导致Wordpress只能启用有限的几个插件，根本不能满足需要。

所以毅然决定迁移到JeckyII+GitHub，在摸索的过程中发现octopress比JeckyII易用，于是导出所有文章，开始迁移。因为我这几年博客写得比较乱，无论是内容上，还是发表方式上，结果用了几个导出脚本，效果都不甚理想，还是有很多地方需要手工修改。六百篇文章，正好趁这个机会挑拣一下，较早的文章质量低的比较多。断断续续地迁移了一些，剩下的工作量还是很大的，留著以后慢慢来吧。另外这次把URL也改成永久格式了，这一来真的伤筋动骨了，连永久重定向都没的做。

至于Feed输出，以前订阅Feedburner烧录的两个地址的读者不受影响，直接订阅Wordpress输出的地址的就丢了，这里再公布一下：

- 本博客聚合输出：http://feeds.feedburner.com/sinolog
- 我的全部资讯聚合：http://feeds.feedburner.com/leninlee

## 迁移那点事

### Python 版本的问题

Archlinux很激进，早已把Python的缺省版本进化到了3.x，octopress会用到2.x，结果在生成全站的时候，会报如下错误：

```
  File "<string>", line 1
    import sys; print sys.executable
                        ^
SyntaxError: invalid syntax
```

在[这里](http://blog.dayanjia.com/2012/04/fix-rubypython-bug-in-arch-linux/)找到解决方法。在octopress的plugins目录里创建文件：

```ruby
require 'pygments'

if !!RUBY_PLATFORM['linux']
    RubyPython.configure :python_exe => '/usr/bin/python2'
end
```

### 不足

用了这段时间octopress，感觉缺点也不小，每次修改后都要重新生成全站，占用系统资源不小，而且理论上以后会越来越慢。

