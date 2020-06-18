# 在非GNOME环境中使用TweetDeck

<p>在非GNOME桌面环境中，运行TweetDeck会报如下错误：</p>

<blockquote>
  <p>Ooops, TweetDeck can't find your data
  TweetDeck is having trouble using some of your passwords that are stored securely on your machine. Clicking submit will clear this data so that you continue to use TweetDeck.</p>
</blockquote>

<p>原因是Adobe Air将密码保存在GNOME的Keyring里，而此时Air找不到GNOME的相关守护进程<strong>gnome-keyring-daemon</strong>。</p>

<p>使用如下脚本启动之：</p>

<p>
```bash
#!/bin/bash
GNOME_KEYRING=`pidof gnome-keyring-daemon`
GNOME_DESKTOP_SESSION_ID=$GNOME_KEYRING /usr/bin/tweetdeck &
```
</p>

<p>Posted via <a href="http://0x3f.org/?p=1894">UltraBlog.vim</a>.</p>

