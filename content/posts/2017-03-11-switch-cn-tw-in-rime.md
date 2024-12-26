---
title: 鼠须管实现简繁转换的方法
slug: switch cn tw in rime
date: 2017-03-11 23:42:11
tags:
- mac
- 输入法
- 计算机
---

鼠须管默认输出繁体，可以通过自带的过滤器转换成简体。这样做是科学的，因为繁体的异体字较多，从简转繁容易转错。

对于只有简体的码表，可以转换成繁体，再通过过滤器转简体，但是会导致在简体状态下无法自造词。所以最好的办法是给繁体单独配一个码表。

步骤为：

1. 复制简体码表的schema.yaml和dict.yaml文件
1. 修改两个yaml文件的名称
1. 文件内容中和文件名对应的内容也都改为和新文件名一致
1. 修改schema.yaml中输入法的名称，使之和简体码表区分开
1. 转换dict.yaml中的内容到繁体
1. 重新部署

如果需要给繁体码表增加临时输出简体的功能，方法如下：

```yaml
# 在码表的schema.yaml中

# switches下增加
  - name: simplification
    states: [ 汉字, 汉字 ]

# engine下增加
  filters:
    - simplifier
    - uniquifier

#key_binder下增加
    - { when: always, accept: Control+Shift+4, toggle: simplification }
    - { when: always, accept: Control+Shift+dollar, toggle: simplification }
```
