---
layout: post
title: Project Euler Problem 22 Solved
slug: project euler 22
date: 2014-04-20 08:03:00
comments: true
tags:
- golang
- python
- php
- 欧拉工程
- 编程
- 计算机
---

Names scores
------------

Using [names.txt](http://projecteuler.net/project/names.txt) (right click and 'Save Link/Target As...'), a 46K text file containing over five-thousand first names, begin by sorting it into alphabetical order. Then working out the alphabetical value for each name, multiply this value by its alphabetical position in the list to obtain a name score.

For example, when the list is sorted into alphabetical order, COLIN, which is worth 3 + 15 + 12 + 9 + 14 = 53, is the 938th name in the list. So, COLIN would obtain a score of 938 × 53 = 49714.

What is the total of all the name scores in the file?

Solution
--------

Pretty code snippets are easily to be implemented in Python as always:

```python
#!/usr/bin/python
# -*- coding: utf-8 -*-

if __name__ == '__main__':
    f = open('names.txt')
    names = sorted(f.readline().replace('"', '').split(','))
    f.close()

    print sum(map(lambda name: sum([ord(c) - 64 for c in name]) \
              * (names.index(name) + 1), names))
```

Easy as Python, ugly as shit, here is the PHP implementation:

```php
<?php
$names = explode(',', str_replace('"', '', file_get_contents('names.txt')));
sort($names, SORT_STRING);
$cal_alpha_value = function($name, $i) {
    $cal_alpha_index = function($char){return ord($char) - 64;};
    return array_sum(array_map($cal_alpha_index, str_split($name, 1))) * $i;
};
echo array_sum(array_map($cal_alpha_value, $names, range(1, count($names))));
?>
```

```go
package main

import (
	"bufio"
	"fmt"
	"os"
	"sort"
	"strings"
)

func main() {
	f, err := os.Open("names.txt")
	if err != nil {
		fmt.Println(err)
		os.Exit(1)
	}
	defer f.Close()

	reader := bufio.NewReader(f)
	line, _ := reader.ReadString('\n')
	names := strings.Split(strings.Replace(line, "\"", "", -1), ",")
	sort.Sort(sort.StringSlice(names))

	val := 0
	for i := 0; i < len(names); i++ {
		tmp := 0
		for j := 0; j < len(names[i]); j++ {
			tmp += int(names[i][j]) - 64
		}
		val += tmp * (i + 1)
	}

	fmt.Println(val)
}
```

I'm the 65646th person to have solved this problem.
