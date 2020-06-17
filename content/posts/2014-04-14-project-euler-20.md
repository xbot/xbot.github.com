---
layout: post
title: "Project Euler Problem 20 Solved"
date: 2014-04-14 22:19:00
comments: true
categories:
- 计算机
tags:
- golang
- python
- 欧拉工程
- 编程
---

Factorial digit sum
-------------------

n! means n × (n − 1) × ... × 3 × 2 × 1

For example, 10! = 10 × 9 × ... × 3 × 2 × 1 = 3628800,  
and the sum of the digits in the number 10! is 3 + 6 + 2 + 8 + 8 + 0 + 0 = 27.

Find the sum of the digits in the number 100!

Solution
--------

{% codeblock lang:python p20.py %}
#!/usr/bin/python
# -*- coding: utf-8 -*-

if __name__ == '__main__':
    product = 1
    for i in range(2, 101):
        product *= i
    print sum([int(x) for x in str(product)])
{% endcodeblock %}

{% codeblock lang:go p20.go %}
package main

import (
    "fmt"
    "math/big"
    "strconv"
)

func main() {
    product := big.NewInt(1)
    for i := 1; i < 101; i++ {
        product.Mul(product, big.NewInt(int64(i)))
    }
    sum := 0
    for idx := range product.String() {
        tmp, _ := strconv.Atoi(product.String()[idx : idx+1])
        sum += tmp
    }
    fmt.Println(sum)
}
{% endcodeblock %}

I'm the 100089th person to have solved this problem.
