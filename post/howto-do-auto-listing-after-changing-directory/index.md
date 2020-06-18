# cd到目录下后自动ls的方法

每cd到一个目录下就ls，这成了我的习惯。以下Bash函数和别名可以实现cd到一个目录下就自动执行ls命令：

```bash
cdl() {
    cd "${1}";
    ls;
}
alias cd=cdl
```

将上述内容添加到用户主目录中的.bashrc中即可。

