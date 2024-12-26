---
title: 用鼠须管输入日期时间的方法
slug: input date and time with rime
date: 2020-01-08 13:52:49
tags:
- Mac
- 最佳实践
- 计算机
---

原来在Linux下用小小输入法，输入日期时间很方便。换到Mac后，很早就在关注这个问题，直到后来鼠须管实现了嵌入Lua脚本的功能才得以解决。

<!--more-->

在“用户设定”目录下创建文件`rime.lua`，内容如下：

```lua
function date_translator(input, seg)
    if (input == "date") then
       --- Candidate(type, start, end, text, comment)
       yield(Candidate("date", seg.start, seg._end, os.date("%Y-%m-%d"), "日期"))
       yield(Candidate("date", seg.start, seg._end, os.date("%Y年%m月%d日"), "日期"))
    end
    if (input == "time") then
       --- Candidate(type, start, end, text, comment)
       yield(Candidate("date", seg.start, seg._end, os.date("%H:%M"), "时间"))
       yield(Candidate("date", seg.start, seg._end, os.date("%H:%M:%S"), "时间"))
    end
    if (input == "datetime") then
       --- Candidate(type, start, end, text, comment)
       yield(Candidate("date", seg.start, seg._end, os.date("%Y-%m-%d %H:%M:%S"), "日期时间"))
    end
 end
 
 --- 过滤器：单字在先
 function single_char_first_filter(input)
    local l = {}
    for cand in input:iter() do
       if (utf8.len(cand.text) == 1) then
          yield(cand)
       else
          table.insert(l, cand)
       end
    end
    for i, cand in ipairs(l) do
       yield(cand)
    end
 end
```

然后在码表配置文件的“engine/translators”下追加`lua_translator@date_translator`。

最后，重新部署即可。
