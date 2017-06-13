---
layout: post
title: "Project Euler Problem 21 Solved"
date: 2014-04-17 22:18
comments: true
categories: 計算機
tags:
- golang
- python
- 歐拉工程
- 編程
---

Amicable numbers
----------------

Let d(n) be defined as the sum of proper divisors of n (numbers less than n which divide evenly into n).  
If d(a) = b and d(b) = a, where a ≠ b, then a and b are an amicable pair and each of a and b are called amicable numbers.

For example, the proper divisors of 220 are 1, 2, 4, 5, 10, 11, 20, 22, 44, 55 and 110; therefore d(220) = 284. The proper divisors of 284 are 1, 2, 4, 71 and 142; so d(284) = 220.

Evaluate the sum of all the amicable numbers under 10000.

Solution
--------

{% codeblock lang:python p21.py %}
#!/usr/bin/python
# -*- coding: utf-8 -*-


def sum_proper_factors(n):
    (result, sqrt) = (1, n ** 0.5)

    (start, step) = n % 2 == 1 and (3, 2) or (2, 1)
    for i in range(start, int(sqrt) + 1, step):
        if n % i == 0:
            result += i + n / i

    if sqrt == int(sqrt):
        result -= sqrt

    return result


def main():
    result = 0
    for i in range(1, 10000):
        sum1 = sum_proper_factors(i)
        if sum1 > i:
            if i == sum_proper_factors(sum1):
                result += i + sum1
    print result


if __name__ == '__main__':
    import time
    startTime = time.time()
    main()
    print time.time() - startTime
{% endcodeblock %}

{% codeblock lang:go p21.go %}
package main

import (
	"fmt"
	"math"
	"time"
)

func sum_proper_factors(n int) int {
	sum, sqrt := 1, math.Sqrt(float64(n))

	start, step := 2, 1
	if n%2 == 1 {
		start, step = 3, 2
	}
	for i := start; i <= int(sqrt); i += step {
		if n%i == 0 {
			sum += i + n/i
		}
	}

	if sqrt == float64(int(sqrt)) {
		sum -= int(sqrt)
	}

	return sum
}

func main() {
	result, startTime := 0, time.Now()

	for i := 1; i < 10000; i++ {
		iSum := sum_proper_factors(i)
		if iSum > i {
			if i == sum_proper_factors(iSum) {
				result += i + iSum
			}
		}
	}

	fmt.Println(result, time.Now().Sub(startTime))
}
{% endcodeblock %}

I'm the 70186th person to have solved this problem.
