# MacOS中切换输入法的最佳实践


原则：

- 一键切换
- 可以对特定应用设定初始输入状态
- 可以方便地确认当前输入状态
- 兼容外接键盘

思路：

- 用Karabiner Elements把右Shift修改成F19，在系统里设置用F19切换输入法
- 用isHUD显示输入法状态
- [用Keyboard Maestro自动切换键盘布局](/post/auto-switch-keyboard-layouts-in-macos/)

Karabiner Elements会使系统的键盘布局失效，且不能对不同的键盘使用不同的布局，所以只有用Keyboard Maestro自动切换。

isHUD只在切换输入法时有效，理想情况是用Caps Lock键灯表示输入状态，不过目前没找到可用的解决办法。

鼠须管可以对不同的应用设置初始输入状态，但只是在切换输入法后，例如对于Alfred，如果上次鼠须管处于中文输入状态，再次打开输入框的时候不会变成英文状态，因此需要在Alfred中设置初始输入法为英文。

