---
layout: post
title: "小確幸，用shift切換輸入法"
date: 2016-09-17 15:36
comments: true
categories: 計算機
tags:
- mac
---

把Alfred升級到3，就想著把切換中英輸入狀態的問題一塊解決掉。

Alfred可以設置默認使用英文輸入狀態，然後用Karabiner把Shift_L映射成Ctrl+Space。現在切換輸入狀態和用Alfred的效率都高多了。

```xml
<item>
<name>Shift_L to Shift_L</name>
<appendix> + When you type Shift_L only,change to previous system input method</appendix>
<appendix>Shift_L == (Ctrl-Space)</appendix>
<identifier>private.change_input_source_to_squirrel.Rime</identifier>
<autogen>__KeyOverlaidModifier__ KeyCode::SHIFT_L, ModifierFlag::SHIFT_L | ModifierFlag::NONE, KeyCode::SHIFT_L, KeyCode::SPACE,ModifierFlag::CONTROL_L</autogen>
</item>
```

