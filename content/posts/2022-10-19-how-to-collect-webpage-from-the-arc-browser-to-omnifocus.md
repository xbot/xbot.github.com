---
title: 用 Hammerspoon 收集 Arc 浏览器网页到 OmniFocus 的方法
slug: How to collect webpage from the Arc browser to OmniFocus
date: 2022-10-19 23:02:50+08:00
tags:
- hammerspoon
- ArcBrowser
- omnifocus
- 最佳实践
- 计算机
toc: false
---

我用 [Hammerspoon](https://www.hammerspoon.org/) 收集网页到 [OmniFocus](https://www.omnigroup.com/omnifocus/) ，它的 [SendToOmniFocus](https://www.hammerspoon.org/Spoons/SendToOmniFocus.html) 对 Chrome 家族的浏览器有原生的支持，但并不适用于 [Arc 浏览器](https://thebrowser.company/)。

看 SendToOmniFocus 的源码是用 Apple Script 获取 Chrome 家族浏览器中网页的信息的，但最初我没有解决怎样用同样的办法拿到 Arc 浏览器网页信息的问题。所以采取通过剪贴板中转的方式实现。

首先配置 [Surfingkeys](https://github.com/brookhong/Surfingkeys) 实现把网页信息组装成特定格式复制到剪贴板：

```javascript
mapkey('yO', 'Copy OmniFocus sensible info.',  yankOmniFocusSensibleInfo);

function yankOmniFocusSensibleInfo() {
    var info_arr = [];
    
    info_arr.push("#omnifocus_sensible");
    info_arr.push(document.title);
    info_arr.push(window.location.href);
    
    Clipboard.write(info_arr.join("\n"));
}
```

然后在 Hammerspoon 中监听剪贴板，一旦发现特定格式的文本，就调用 Apple Script 填充解析到的网页信息到 OmniFocus 的对话框：

```lua
-- Interpolate table values into a string
-- From http://lua-users.org/wiki/StringInterpolation
local function interp(s, tab)
   return (s:gsub('($%b{})', function(w) return tab[w:sub(3, -2)] or w end))
end

-- Read a whole file into a string
local function slurp(path)
   local f = assert(io.open(path))
   local s = f:read("*a")
   f:close()
   return s
end

local pasteboard = require('hs.pasteboard')

local function open_omnifocus_edit_dialog(lines)
    local module_dir = debug.getinfo(1, "S").source:sub(2):match("(.*/)")
    local template_file = module_dir .. '../templates/add_webpage_to_omnifocus.tpl'
    local text=slurp(template_file)
    local data = {
        title = lines[2],
        url = lines[3],
    }
    local as_script = interp(text, data)
    hs.osascript.applescript(as_script)
end

if GetOption('watch_omnifocus_sensible_data', 'off') == 'on' then
    OmniFocusPasteboardWatcher = pasteboard.watcher.new(function(pasteboard_content)
        local lines = {}
        for line in string.gmatch(pasteboard_content, "[^\r\n]+") do
            table.insert(lines, line)
        end

        if #lines == 3 and lines[1] == '#omnifocus_sensible' then
            open_omnifocus_edit_dialog(lines)
        end
    end)

    OmniFocusPasteboardWatcher:start()
end
```

后来解决了前面提到的问题，可以直接获取网页信息的 Apple Script 如下：

```applescript
tell application "Arc"
    set tabURL to URL of active tab of window 1
    set tabTitle to title of active tab of window 1
    tell front document of application "OmniFocus"
        tell quick entry
            make new inbox task with properties {name:("Review: " & tabTitle), note:tabURL as text}
            open
        end tell
    end tell
    display notification "Successfully exported tab '" & tabTitle & "' to OmniFocus" with title "Send tab to OmniFocus"
end tell
```

Hammerspoon 里这样配置：

```lua
spoon.SendToOmniFocus:registerApplication('Arc', {
    as_scriptfile = os.getenv('HOME') .. '/.hammerspoon/templates/add_arc_webpage_to_omnifocus.applescript',
    itemname = 'tab'
})
```

以上提到的代码片断的完整版本见以下链接：

- [Surfingkeys 的配置](https://gist.github.com/xbot/241b432193bde2df779339963c51076b)
- [Hammerspoon 的配置](https://github.com/xbot/hammerspoon)