# 用 SSH 命令调试 Git 的连接问题


昨天执行`git pull`的时候发现会一直卡在那儿，排除了各种可能，而且 Github 上的项目也是可以正常拉取的，说明跟网络、代理等关系应该不大，git 命令本身也工作正常。

问题集中在公司的项目上，所有项目都无法拉取代码。起先怀疑是公司自建的 Gitlab 出了问题，但是其他人那里又是正常的，甚至跟我同一局域网的电脑上也是正常的。

也就是说问题应该出在我自己的电脑上。考虑到前两天还能正常使用，之后唯一一件相关的事就是升级 macOS 到 10.15.4 了。

然而用 Homebrew 重新安装 git 等相关的几个包也没能解决问题，于是用`ssh -T -v git@gitlab.xxx.com -p 8848`调试，输出如下：

```
donie@Donies  ~  ssh -T -v git@gitlab.xxx.com -p 8848
OpenSSH_8.1p1, LibreSSL 2.7.3
debug1: Reading configuration data /Users/donie/.ssh/config
debug1: /Users/donie/.ssh/config line 9: Applying options for gitlab.xxx.com
debug1: Reading configuration data /etc/ssh/ssh_config
debug1: /etc/ssh/ssh_config line 47: Applying options for *
debug1: Connecting to gitlab.xxx.com port 8848.
```

卡在了“Connecting to gitlab.xxx.com port 8848”这一步，但是`telnet gitlab.xxx.com 8848`却是通的。

问题陷入僵局。

Google的过程中偶然注意到别人正常的调试信息中这一步的输出是有 IP 地址的，如下所示：

```
debug1: Connecting to gitlab.xxx.com [xxx.xxx.xxx.xxx] port 8848.
```

首先怀疑应该是主机名映射出了问题，但是无论是修改 Hosts 还是在 ~/.ssh/config 中指定都还是不行，ping 主机名也能正常解析出 IP 地址。

问题陷入绝境。

绝望之际，突然想到会不会是 SSH 自身出了问题呢？`which ssh`发现是系统自带的，所以装上 Homebrew 的版本试了一下，竟然好了！

bugOS !!!
