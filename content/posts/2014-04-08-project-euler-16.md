---
layout: post
title: "Project Euler Problem 16 Solved"
date: 2014-04-08 21:57:00
comments: true
categories:
- 计算机
tags:
- golang
- python
- 欧拉工程
- 编程
---

Power digit sum
---------------

2^15 = 32768 and the sum of its digits is 3 + 2 + 7 + 6 + 8 = 26.

What is the sum of the digits of the number 2^1000?

Solution
--------

```python
#!/usr/bin/python
# -*- coding: utf-8 -*-

import math

if __name__ == '__main__':
    str = format(math.pow(2, 1000), 'f')
    sum = 0
    for c in str[:str.index('.')]:
        sum += int(c)
    print sum
```

```go
package main

import (
    "fmt"
    "math/big"
    "strconv"
)

func main() {
    num, base := big.NewInt(1), big.NewInt(2)
    for power := 0; power < 1000; power++ {
        num.Mul(num, base)
    }
    result := 0
    for i := 0; i < len(num.String()); i++ {
        bit, _ := strconv.Atoi(num.String()[i : i+1])
        result += bit
    }
    fmt.Println(num.String(), result)
}
```

I'm the 109044th person to have solved this problem.
