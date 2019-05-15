---
layout: post
title: "Project Euler Problem 15 Solved"
date: 2014-04-02 21:44
comments: true
categories: 计算机
tags:
- golang
- python
- 欧拉工程
- 编程
---

Lattice paths
-------------

Starting in the top left corner of a 2×2 grid, and only being able to move to the right and down, there are exactly 6 routes to the bottom right corner.

{% img http://pic.yupoo.com/leninlee/DEwoQ7qc/medish.jpg %}

How many such routes are there through a 20×20 grid?

Solution
--------

{% codeblock lang:python p15.py %}
#!/usr/bin/python
# -*- coding: utf-8 -*-

if __name__ == '__main__':
    (steps, a, b) = (20, 1, 1)

    i = steps * 2
    while i > steps:
        a *= i
        i -= 1
    while steps > 1:
        b *= steps
        steps -= 1

    print a / b
{% endcodeblock %}

I'm the 86747th person to have solved this problem.
