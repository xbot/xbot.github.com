---
layout: post
title: "Project Euler Problem 19 Solved"
slug: project euler 19
date: 2014-04-12 21:21:00
comments: true
categories:
- 计算机
tags:
- golang
- python
- 欧拉工程
- 编程
---

Counting Sundays
----------------

You are given the following information, but you may prefer to do some research for yourself.

* 1 Jan 1900 was a Monday.
* Thirty days has September,
April, June and November.  
All the rest have thirty-one,  
Saving February alone,  
Which has twenty-eight, rain or shine.  
And on leap years, twenty-nine.  
* A leap year occurs on any year evenly divisible by 4, but not on a century unless it is divisible by 400.

How many Sundays fell on the first of the month during the twentieth century (1 Jan 1901 to 31 Dec 2000)?

Solution
--------

```python
#!/usr/bin/python
# -*- coding: utf-8 -*-


def is_leap(year):
    return year % 400 == 0 or year % 100 != 0 and year % 4 == 0


def count_days(year, month, day):
    days = 0
    for y in range(1900, year):
        days += is_leap(y) and 366 or 365
    thirties = [4, 6, 9, 11]
    for m in range(1, month):
        if m == 2:
            days += is_leap(year) and 29 or 28
        else:
            days += m in thirties and 30 or 31
    return days + day


def main():
    count = 0
    for year in range(1901, 2001):
        for month in range(1, 13):
            if count_days(year, month, 1) % 7 == 0:
                count += 1
    print count


if __name__ == '__main__':
    main()
```

```go
package main

import (
    "fmt"
    "math"
)

func is_leap(year int) bool {
    return math.Mod(float64(year), 400) == 0 || math.Mod(float64(year), 100) != 0 && math.Mod(float64(year), 4) == 0
}

func count_days(year, month, day int) int {
    days := 0
    for y := 1900; y < year; y++ {
        if is_leap(y) {
            days += 366
        } else {
            days += 365
        }
    }
    for m := 1; m < month; m++ {
        if m == 2 {
            if is_leap(year) {
                days += 29
            } else {
                days += 28
            }
        } else {
            if m == 4 || m == 6 || m == 9 || m == 11 {
                days += 30
            } else {
                days += 31
            }
        }
    }
    return days + day
}

func main() {
    count := 0
    for year := 1901; year < 2001; year++ {
        for month := 1; month < 13; month++ {
            if math.Mod(float64(count_days(year, month, 1)), 7) == 0 {
                count++
            }
        }
    }
    fmt.Println(count)
}
```

I'm the 65183rd person to have solved this problem.
