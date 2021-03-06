# Double Commander: 免费、跨平台的Total Commander

<p>由于Total Commander很贵，在Windows下又是必不可少的，我一直希望能找到一个TC的免费替代品。试用过很多免费的文件管理器，但都不理想。偶然看到<a href="http://doublecmd.sourceforge.net/">Double Commander</a>，才发现这几乎完全就是我想要的。</p>

<p><a href="https://picasaweb.google.com/lh/photo/ioR_w9MmX81q_5a8xjUPsA?feat=embedwebsite"><img src="https://lh4.googleusercontent.com/-gkI4K4m5SSM/Tl31NceNNhI/AAAAAAAABzs/3AigwSN7whI/s640/doublecmd_screenshot.png" height="360" width="640" /></a></p>

<h2>特性</h2>

<ul>
<li>几乎完全模仿TC，甚至连插件的API都一致</li>
<li>界面布局、样式高度可定制</li>
<li>热键高度可定制</li>
<li>开源、免费、跨平台</li>
</ul>

<h2>局限</h2>

<ul>
<li>功能相对TC少很多</li>
<li>稳定性不是非常高</li>
<li>运行速度稍嫌慢</li>
<li>开发进度慢</li>
</ul>

<h2>小技巧</h2>

<h3>热键设置</h3>

<p>每个内置命令可以设置多个热键，每个热键可以指定参数，参数中可以使用环境变量，如下图所示：</p>

<p><a href="https://picasaweb.google.com/lh/photo/Z3jLMCGkoAbs1XC6yOhElg?feat=embedwebsite"><img src="https://lh5.googleusercontent.com/-lFMDaSny_gw/Tl31OKrfhMI/AAAAAAAABzs/2XX44Go-3zc/s640/doublecmd_hotkey.png" height="417" width="640" /></a></p>

<p>针对命令<code>cm_ChangeDir</code>设置了两个热键，其中，给<strong>Ctrl+Home</strong>指定的参数是<strong>$HOME</strong>，给<strong>Ctrl+Shift+Home</strong>指定的参数是<strong>$HOME/Desktop</strong>，即分别切换到当前用户的主目录和桌面目录。</p>

<h3>自定义文件类型命令</h3>

<p>可以为每种文件类型定义多个命令，这些自定义命令将显示为上下文菜单的“动作”菜单的子菜单项。如下图所示：</p>

<p><a href="https://picasaweb.google.com/lh/photo/m7mjk4g77XsMZw58ayJnEw?feat=embedwebsite"><img src="https://lh5.googleusercontent.com/-4Z6LcQg9YIc/Tl31OqcRqKI/AAAAAAAABzs/wJjK8noUWM8/s400/doublecmd_filetype_command_01.png" height="400" width="376" /></a></p>

<p>定义了名称为“Archive”的文件类型，关联了一系列的文件后缀名。然后添加了一个名为“解压缩到当前目录”的动作，并指定所执行的命令为<code>urxvt -e aunpack %f</code>。其中，<strong>%f</strong>是一个占位符，代表当前文件的全名。DC预置了几个占位符，可以点击命令输入框右侧图标是加号的按钮选择。</p>

<p>最终的效果如下：</p>

<p><a href="https://picasaweb.google.com/lh/photo/mXYVL8uX1TNw0ENFur1PUQ?feat=embedwebsite"><img src="https://lh6.googleusercontent.com/-smFkdYyLMHE/Tl31QAWMNXI/AAAAAAAABzs/hIBW-yVpBuQ/s400/doublecmd_filetype_command_02.png" height="236" width="400" /></a></p>

<h3>解决日期时间乱码的问题</h3>

<p>截至版本0.5.0 beta，如果设置了使用日期时间字符串的格式为“yyyy-mm-dd hh:mm:ss”，在<strong>zh_CN.UTF-8</strong>的区域设置下，字符串中将出现乱码：</p>

<p><a href="https://picasaweb.google.com/lh/photo/gMOlqF5DOQQ5otUEBon7lA?feat=embedwebsite"><img src="https://lh6.googleusercontent.com/-LfvO5yBH_F0/Tl3_d13FCNI/AAAAAAAAB0A/MHApuvTvkSg/s400/doublecmd_datetime_illegle_char.png" height="400" width="168" /></a></p>

<p>临时解决办法是以英文区域设置启动doublecmd：</p>

```bash
#!/bin/bash

export LC_ALL=en_US.UTF-8
doublecmd &
```

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

