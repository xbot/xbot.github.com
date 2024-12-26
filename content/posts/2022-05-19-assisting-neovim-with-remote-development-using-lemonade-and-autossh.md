---
title: 使用 lemonade 和 autossh 辅助 Neovim 远程开发
slug: Assisting Neovim With Remote Development Using Lemonade and Autossh
date: 2022-05-19 16:02:34+08:00
tags:
- vim
- 编程
- 计算机
---

我的开发环境部署在 Linux VM 里，普通的远程复制用 [vim-oscyank](https://github.com/ojroques/vim-oscyank) 插件是可以实现的。但是当我想用 [fugitive](https://github.com/tpope/vim-fugitive) 的 `:GBrowse` 命令打开 Gitlab 链接的时候遇到了困难，甚至我想退而求其次、通过 `:GBrowse!` 复制链接都不可得，因为 fugitive 的代码里通过 `has('clipboard')` 判断 Vim 是否可以使用系统剪贴板，且我的 Linux VM 不满足该特性的条件（见 `:help clipboard`）。所以用 [lemonade](https://github.com/lemonade-command/lemonade) 解决这个问题。

# 实现方式

lemonade 虽然支持客户端和服务端直接通信，但是并不提供安全保障，所以我采取 SSH 端口远程转发的方式并用 [autossh](https://www.harding.motd.ca/autossh/) 保持连接。

首先分别在服务端（MacOS）和客户端（Linux VM）创建 lemonade 的配置文件（`~/.config/lemonade.toml`）：

服务端：

```ini
port = 2489
allow = '127.0.0.1'
line-ending = 'lf'
```

客户端：

```ini
port = 2489
host = 'localhost'
trans-loopback = true
trans-localfile = true
line-ending = 'lf'
```

然后创建 MacOS 的服务配置文件：

`~/Library/LaunchAgents/org.0x3f.lemonade.plist`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>KeepAlive</key>
	<true/>
	<key>Label</key>
	<string>org.0x3f.lemonade</string>
	<key>ProgramArguments</key>
	<array>
        <string>/Users/donie/.go/bin/lemonade</string>
		<string>server</string>
	</array>
	<key>RunAtLoad</key>
	<true/>
	<key>StandardErrorPath</key>
	<string>/opt/homebrew/var/log/lemonade-error.log</string>
    <key>StandardOutPath</key>
	<string>/opt/homebrew/var/log/lemonade-output.log</string>
	<key>WorkingDirectory</key>
    <string>/Users/donie</string>
</dict>
</plist>
```

`~/Library/LaunchAgents/org.0x3f.autossh.plist`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
	<key>KeepAlive</key>
	<true/>
	<key>Label</key>
	<string>org.0x3f.autossh</string>
	<key>ProgramArguments</key>
	<array>
        <string>/opt/homebrew/bin/autossh</string>
        <string>-f</string>
        <string>-N</string>
		<string>-M</string>
		<string>4444</string>
		<string>-R</string>
		<string>2489:127.0.0.1:2489</string>
		<string>arch</string>
	</array>
	<key>RunAtLoad</key>
	<true/>
	<key>StandardErrorPath</key>
	<string>/opt/homebrew/var/log/autossh-error.log</string>
    <key>StandardOutPath</key>
	<string>/opt/homebrew/var/log/autossh-output.log</string>
	<key>WorkingDirectory</key>
    <string>/Users/donie</string>
</dict>
</plist>
```

**注意**：上述配置在我的 MacBook Air M1 里工作良好，但是在另一台 Intel iMac 里，需要去掉 `-f` 参数，否则无法启动 autossh 。

启动服务：

```bash
launchctl load -w ~/Library/LaunchAgents/org.0x3f.lemonade.plist

launchctl load -w ~/Library/LaunchAgents/org.0x3f.autossh.plist
```

在 Linux VM 里给 lemonade 创建别名，使 fugitive 调用 `xdg-open` 打开链接时实际通过 lemonade 执行。由于真实的 `xdg-open` 在 `/usr/bin` 下，且在我的 `PATH` 环境变量里 `/usr/local/bin` 位于 `/usr/bin` 之前，所以我把别名建在这个目录下面：

```bash
sudo ln -s /usr/bin/lemonade /usr/local/bin/xdg-open
```

# 存在的问题

lemonade 的客户端不支持多服务端的配置，在通过 SSH 端口远程转发的前提下，只能借助 `--port` 参数指定不同的端口，可以在 `~/.zshenv` （或同类文件）里定义一个保存端口的环境变量，通过 `$SSH_CLIENT` 选择对应的端口。