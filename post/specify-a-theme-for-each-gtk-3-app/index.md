# 为每个GTK3应用指定单独的主题


指定GVim 8使用Numix主题并最小程度影响既有的脚本、快捷键。

我这里gvim安装在`/usr/bin/gvim`，$PATH中`/usr/local/bin`在`/usr/bin`前面，所以在`/usr/local/bin`下创建gvim：

```bash
#!/bin/sh

GTK_THEME=Numix /usr/bin/gvim "$@"
```

同理，对gvimdiff也做同样处理。

