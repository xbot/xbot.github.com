# 小确幸，用shift切换输入法


把Alfred升级到3，就想著把切换中英输入状态的问题一块解决掉。

Alfred可以设置默认使用英文输入状态，然后用Karabiner把Shift_L映射成Ctrl+Space。现在切换输入状态和用Alfred的效率都高多了。

```xml
<item>
<name>Shift_L to Shift_L</name>
<appendix> + When you type Shift_L only,change to previous system input method</appendix>
<appendix>Shift_L == (Ctrl-Space)</appendix>
<identifier>private.change_input_source_to_squirrel.Rime</identifier>
<autogen>__KeyOverlaidModifier__ KeyCode::SHIFT_L, ModifierFlag::SHIFT_L | ModifierFlag::NONE, KeyCode::SHIFT_L, KeyCode::SPACE,ModifierFlag::CONTROL_L</autogen>
</item>
```


