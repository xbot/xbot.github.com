---
layout: post
title: Project Euler Problem 17 Solved
slug: project euler 17
date: 2014-04-09 22:29:00
comments: true
tags:
- golang
- python
- 欧拉工程
- 编程
- 计算机
---

Number letter counts
--------------------

If the numbers 1 to 5 are written out in words: one, two, three, four, five, then there are 3 + 3 + 5 + 4 + 4 = 19 letters used in total.

If all the numbers from 1 to 1000 (one thousand) inclusive were written out in words, how many letters would be used?

**NOTE**: Do not count spaces or hyphens. For example, 342 (three hundred and forty-two) contains 23 letters and 115 (one hundred and fifteen) contains 20 letters. The use of "and" when writing out numbers is in compliance with British usage.

Solution
--------

```python
#!/usr/bin/python
# -*- coding: utf-8 -*-


def translate_number(num):
    words = {
        0: '',
        1: 'one',
        2: 'two',
        3: 'three',
        4: 'four',
        5: 'five',
        6: 'six',
        7: 'seven',
        8: 'eight',
        9: 'nine',
        10: 'ten',
        11: 'eleven',
        12: 'twelve',
        13: 'thirteen',
        14: 'fourteen',
        15: 'fifteen',
        16: 'sixteen',
        17: 'seventeen',
        18: 'eighteen',
        19: 'nineteen',
        20: 'twenty',
        30: 'thirty',
        40: 'forty',
        50: 'fifty',
        60: 'sixty',
        70: 'seventy',
        80: 'eighty',
        90: 'ninety',
        }

    english = ''

    if num / 1000 > 0:
        english += translate_number(num / 1000) + ' thousand'
        tmp = translate_number(num % 1000)
        english += ((tmp.strip() == '' or tmp.find('hundred') > -1)
                    and ' ' or ' and ') + tmp
    elif num / 100 > 0:
        english += translate_number(num / 100) + ' hundred'
        tmp = translate_number(num % 100)
        if tmp.strip() != '':
            english += ' and ' + tmp
    else:
        if words.has_key(num):
            english += words[num]
        else:
            english += words[num / 10 * 10] + '-' + words[num % 10]

    return english


if __name__ == '__main__':
    count = 0
    for i in range(1, 1001):
        count += len(translate_number(i).replace(' ', '').replace('-',
                     ''))
    print count
```

I'm the 71762nd person to have solved this problem.
