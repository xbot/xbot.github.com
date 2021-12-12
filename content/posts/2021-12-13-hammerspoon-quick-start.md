---
title: "Hammerspoon 快速入门"
slug: Hammerspoon Quick Start
date: 2021-12-13T00:38:06+08:00
categories:
- 计算机
tags:
- Lua
- Hammerspoon
- 编程
---

Hammerspoon（*以下简称 HS*）是个很好用的效率工具脚手架，我一直用它实现 APP 启动器、窗口控制、桌面常亮等功能。

HS 可以通过写脚本可以实现很多个性化的需求。最快的上手方式是直接安装别人写好的[脚本](https://github.com/sugood/hammerspoon)，然后根据自己的需要稍加修改即可。如果需要深度定制，可以自行实现新的模块。

HS 使用 Lua 语言。我在 08 年左右用过一年的 [SciTE](https://www.scintilla.org/SciTE.html) 编辑器，它也通过内嵌的 Lua 配置和扩展功能，所以学了这个语言。当时 NGINX 也才刚起步， Lua 主要还是被用在游戏脚本的开发，并没有现在使用得如此广泛。当时对这个语言的印象是两个极端，一是速度非常快，远远超过 PHP 和 Python ，比 C 也只慢一点点；二是开发库非常简陋，其它脚本语言一行就能实现的常见功能，它往往要从头开始写。

总之 Lua 是个很精简的语言，可以通过 [Y 分钟速成 X](https://learnxinyminutes.com/docs/zh-cn/lua-cn/) 的教程快速熟悉它的语法，然后浏览一遍[官方入门指南](https://www.hammerspoon.org/go/)。HS 提供了很多方便的模块，通过 [API 文档](https://www.hammerspoon.org/docs/index.html)查看。

由于经常需要切换键盘映射方案，所以我写了一个简单的模块来实现这个功能：

```lua
local hotkey = require "hs.hotkey"

hotkey.bind(hyper, "K", function()
    local configFile = os.getenv('HOME') .. '/.config/karabiner/karabiner.json'

    if hs.json.read(configFile) == nil then
        hs.alert.show('Failed to read config file!')
        return
    end

    local configs = hs.json.read(configFile)
    local profiles = configs['profiles']
    local selectedIndex = nil

    for i = 1, #profiles do
        if profiles[i]['selected'] == true then
            selectedIndex = i
            break
        end
    end

    local switchToIndex = selectedIndex + 1

    if switchToIndex > #profiles then switchToIndex = 1 end

    profiles[switchToIndex]['selected'] = true
    profiles[selectedIndex]['selected'] = false

    hs.json.write(configs, configFile, true, true)

    hs.alert.show(profiles[switchToIndex]['name'] .. ' activated!')
end)
```

源码见[这里](https://github.com/xbot/hammerspoon/blob/master/modules/karabiner.lua)。

代码非常简单，通过 HS 的 API `hotkey.bind()` 绑定快捷键到一个包含所有功能逻辑的匿名函数上。最后在 `init.lua` 里引入这个模块即可。

除此之外， HS 还有个 Spoon 的功能，一个 Spoon 其实就是它的一个插件。官方维护了一份 spoon 的[列表](https://www.hammerspoon.org/Spoons/) 。Spoon 的详细用法可以参考 Github 上官方仓库的[说明](https://github.com/Hammerspoon/hammerspoon/blob/master/SPOONS.md)。

以 `SendToOmniFocus.spoon` 为例，下载 zip 文件并解压到 `~/.hammerspoon/Spoons` 目录，新增如下模块：

```lua
hs.loadSpoon('SendToOmniFocus')

spoon.SendToOmniFocus:bindHotkeys({send_to_omnifocus = {{'ctrl', 'alt', 'cmd'}, 'O'}})

spoon.SendToOmniFocus:registerApplication('Brave Browser', { apptype = "chromeapp", itemname = "tab" })
```

源码见[这里](https://github.com/xbot/hammerspoon/blob/master/modules/omnifocus.lua)。
