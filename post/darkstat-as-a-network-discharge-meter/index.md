# darkstat: 网络流量监测工具

<a href="http://dmr.ath.cx/net/darkstat/">darkstat</a>是一个跨平台的网络流量监测工具，可显示最大一个月内操作系统的各项网络流量指标。

具有以下特性：

<ol>
	<li>以守护进程方式运行，提供Web用户界面，零配置，易于使用</li>
	<li>可显示较为详细的网络流量指标</li>
	<li>性能表现好，占用资源少</li>
	<li>跨平台，支持多个UNIX、类UNIX操作系统，并有非官方的Windows移植版</li>
</ol>

具有以下不足：

<ol>
	<li>功能较为简单，以易用性换灵活性</li>
	<li>日志记录功能比较单薄，需要编写辅助脚本并设置定时任务</li>
</ol>

darkstat启动时需要手工指定监视的网络接口，因此需要用根权限启动：

```bash
sudo darkstat -i eth0
```

darkstat默认使用<strong>667</strong>端口提供Web访问，可以用<strong>-p</strong>参数指定端口：

```bash
sudo darkstat -i eth0 -p 8080
```

darkstat的Web用户界面分为Graph、Hosts和Host三部分。Graph是缺省主页，用于显示最近一分钟、一小时、一天和一个月的流量统计直方图，将光标置于任何一个直方条上可查看对应时刻的流量指标：

<a href="http://picasaweb.google.com/lh/photo/NAB3_2TtnezdM45HoNd5pQ?feat=embedwebsite"><img src="http://lh4.ggpht.com/_ceUJ_lBTHzc/TRcCUWqnF0I/AAAAAAAABhs/wLRQb3rvHW8/s400/darkstat-graphs.png" height="400" width="395" /></a>

Hosts界面按主机显示各自的流量统计指标的值：

<a href="http://picasaweb.google.com/lh/photo/kG1J2j2eecaLUnhDL-fArA?feat=embedwebsite"><img src="http://lh4.ggpht.com/_ceUJ_lBTHzc/TRcCUXxnsMI/AAAAAAAABhw/ri5OsbqPdmc/s400/darkstat-hosts.png" height="322" width="400" /></a>

单击Hosts界面上的IP进入对应主机的Host界面，此界面按TCP/UDP类型显示对应的主机的各端口的网络流量指标的值：

<a href="http://picasaweb.google.com/lh/photo/XBNQWty4lp63OWwyAFm_BA?feat=embedwebsite"><img src="http://lh3.ggpht.com/_ceUJ_lBTHzc/TRcCUn6lg_I/AAAAAAAABh0/6Aj0hArzIYk/s800/darkstat-host.png" height="800" width="190" /></a>

可通过如下命令查看占用指定端口的进程：

<blockquote>
[lenin@archer ~]$ lsof -i tcp:58062 -n
COMMAND   PID  USER   FD   TYPE DEVICE SIZE/OFF NODE NAME
mlnet   18660 lenin   74u  IPv4 251788      0t0  TCP 192.168.1.3:58062->122.116.212.89:http (ESTABLISHED)
</blockquote>

darkstat区别于其它流量监测工具的最大的优点就是简单易用，非常适合对流量监测应用不复杂的场景。前段时间公司的一台RHEL服务器因为流量过大多次被网管拔线，后来用darkstat监测到大部分流量被几个UDP端口占去，进而发现原来是服务器被入侵并被安装了一个扫描器<a href="http://code.google.com/p/sipvicious/">SIPVicious</a>。

