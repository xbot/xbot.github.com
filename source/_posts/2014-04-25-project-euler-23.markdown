---
layout: post
title: "Project Euler Problem 23 Solved"
date: 2014-04-25 23:56
comments: true
categories: 计算机
tags:
- golang
- python
- 欧拉工程
- 编程
---

Non-abundant sums
-----------------

A perfect number is a number for which the sum of its proper divisors is exactly equal to the number. For example, the sum of the proper divisors of 28 would be 1 + 2 + 4 + 7 + 14 = 28, which means that 28 is a perfect number.

A number n is called deficient if the sum of its proper divisors is less than n and it is called abundant if this sum exceeds n.

As 12 is the smallest abundant number, 1 + 2 + 3 + 4 + 6 = 16, the smallest number that can be written as the sum of two abundant numbers is 24. By mathematical analysis, it can be shown that all integers greater than 28123 can be written as the sum of two abundant numbers. However, this upper limit cannot be reduced any further by analysis even though it is known that the greatest number that cannot be expressed as the sum of two abundant numbers is less than this limit.

Find the sum of all the positive integers which cannot be written as the sum of two abundant numbers.

Solution
--------

Using sets instead of lists greatly improves performance in Python:

{% codeblock lang:python p23.py %}
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
    (result, limit, abundants) = (0, 28124, set())

    for n in range(1, limit):
        if sum_proper_factors(n) > n:
            abundants.add(n)
        if not any(n - a in abundants for a in abundants):
            result += n

    print result


if __name__ == '__main__':
    import time
    startTime = time.time()
    main()
    print time.time() - startTime
{% endcodeblock %}

Simulate sets using maps in Go:

{% codeblock lang:go p23.go %}
package main

import (
	"fmt"
	"math"
	"time"
)

func sum_proper_factors(n int) int {
	result, sqrt := 1, math.Sqrt(float64(n))

	start, step := 2, 1
	if n%2 == 1 {
		start, step = 3, 2
	}
	for i := start; i <= int(sqrt); i += step {
		if n%i == 0 {
			result += i + n/i
		}
	}

	if sqrt == float64(int(sqrt)) {
		result -= int(sqrt)
	}

	return result
}

func main() {
	result, limit, abundants, startTime := 0, 28124, make(map[int]bool), time.Now()

	for n := 1; n < limit; n++ {
		if sum_proper_factors(n) > n {
			abundants[n] = true
		}
		isSumOfTwoAbundants := false
		for k := range abundants {
			if abundants[n-k] == true {
				isSumOfTwoAbundants = true
				break
			}
		}
		if !isSumOfTwoAbundants {
			result += n
		}
	}

	fmt.Println(result, time.Now().Sub(startTime))
}
{% endcodeblock %}

I'm the 49489th person to have solved this problem.
