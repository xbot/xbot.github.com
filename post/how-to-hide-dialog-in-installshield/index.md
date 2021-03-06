# 隐藏InstallShield对话框控件的方法

<p>SQLServerSelectLogin对话框中如果选择Windows身份验证，在附加数据库时会因权限不够而失败，所以应该隐藏这个选项，从而要求用户只执行SQL身份验证。</p>

<p>但是如果在定制对话框布局时直接删除两个单选框，会导致用户输入的用户名和密码不能被安装程序获取，并且InstallShield也没有提供隐藏这些控件的属性。解决方法是修改控件的定位属性，将其定位到对话框的大小范围之外：</p>

<p>要达到的目标：</p>

<p><a href="https://picasaweb.google.com/lh/photo/pDxRP_-E5wL7hAoCGYhHXQ?feat=embedwebsite"><img src="https://lh5.googleusercontent.com/-069F7ZsOWOo/TgHdXDlb7iI/AAAAAAAABv0/zd6y0KAlYWM/s400/SQLServerSelectLogin_Customization_01.png" height="299" width="400" /></a></p>

<p>修改Top属性，将指定的控件定位在对话框之外：</p>

<p><a href="https://picasaweb.google.com/lh/photo/5NUaJ2Re61aWiUcJwDOHZg?feat=embedwebsite"><img src="https://lh6.googleusercontent.com/-9xthNFn9b2U/TgHdXnU-TmI/AAAAAAAABv4/-pQdJWF3nM8/s400/SQLServerSelectLogin_Customization_02.png" height="302" width="400" /></a></p>

<p>最终效果：</p>

<p><a href="https://picasaweb.google.com/lh/photo/PImOz5YLlQKHAS2KUfggyw?feat=embedwebsite"><img src="https://lh5.googleusercontent.com/-o2S0V7IzHKg/TgHdWxwEWMI/AAAAAAAABvw/2q4M26A_O2s/s400/SQLServerSelectLogin_Customization_03.png" height="299" width="400" /></a></p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

