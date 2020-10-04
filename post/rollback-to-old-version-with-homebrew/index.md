# 使用 Homebrew 安装旧版程序


前几天发现因为 Hugo 的不兼容更新导致我在用的主题在部署时报错，所以打算装回旧版。但是网上找到的 Homebrew 回滚方法已过时，新的方法更简单，但找到具体说明也费了点功夫。

以回滚 Hugo 到 0.74.3 为例：

```shell
brew uninstall hugo
brew tap-new donie/hugo-0-74-3
brew extract --version 0.74.3 hugo donie/hugo-0-74-3
brew install hugo@0.74.3
```

即：先创建一个名为“donie/hugo-0-74-3”的 tap ，然后通过 extract 命令把相应版本的数据放到里面，就可以安装了。
