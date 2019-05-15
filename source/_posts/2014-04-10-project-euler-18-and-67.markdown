---
layout: post
title: "Project Euler Problem 18&67 Solved"
date: 2014-04-10 21:28
comments: true
categories: 计算机
tags:
- golang
- python
- 欧拉工程
- 编程
---

Maximum path sum I
------------------

By starting at the top of the triangle below and moving to adjacent numbers on the row below, the maximum total from top to bottom is 23.

3  
7 4  
2 4 6  
8 5 9 3

That is, 3 + 7 + 4 + 9 = 23.

Find the maximum total from top to bottom of the triangle below:

75  
95 64  
17 47 82  
18 35 87 10  
20 04 82 47 65  
19 01 23 75 03 34  
88 02 77 73 07 63 67  
99 65 04 28 06 16 70 92  
41 41 26 56 83 40 80 70 33  
41 48 72 33 47 32 37 16 94 29  
53 71 44 65 25 43 91 52 97 51 14  
70 11 33 28 77 73 17 78 39 68 17 57  
91 71 52 38 17 14 91 43 58 50 27 29 48  
63 66 04 68 89 53 67 30 73 16 69 87 40 31  
04 62 98 27 23 09 70 98 73 93 38 53 60 04 23

**NOTE**: As there are only 16384 routes, it is possible to solve this problem by trying every route. However, Problem 67, is the same challenge with a triangle containing one-hundred rows; it cannot be solved by brute force, and requires a clever method! ;o)

Solution
--------

{% codeblock lang:python p18.py %}
#!/usr/bin/python
# -*- coding: utf-8 -*-


def main():
    matrix = []
    file = open('data_p18.txt')
    for line in file.readlines():
        matrix.append(map(int, line.replace('\n', '').split(' ')))

    (x, y) = (0, 0)
    for y in range(len(matrix)):
        for x in range(len(matrix[y])):
            if y > 0:
                greaterParentPathValue = 0
                if x > 0:
                    greaterParentPathValue = matrix[y - 1][x - 1]
                if x < len(matrix[y - 1]) and matrix[y - 1][x] \
                    > greaterParentPathValue:
                    greaterParentPathValue = matrix[y - 1][x]
                matrix[y][x] += greaterParentPathValue

    print max(matrix[-1])


if __name__ == '__main__':
    main()
{% endcodeblock %}

I'm the 70471st person to have solved this problem.

Maximum path sum II
-------------------

By starting at the top of the triangle below and moving to adjacent numbers on the row below, the maximum total from top to bottom is 23.

**3**  
**7** 4  
2 **4** 6  
8 5 **9** 3

That is, 3 + 7 + 4 + 9 = 23.

Find the maximum total from top to bottom in [triangle.txt](http://projecteuler.net/project/triangle.txt) (right click and 'Save Link/Target As...'), a 15K text file containing a triangle with one-hundred rows.

**NOTE**: This is a much more difficult version of Problem 18. It is not possible to try every route to solve this problem, as there are 2^99 altogether! If you could check one trillion (10^12) routes every second it would take over twenty billion years to check them all. There is an efficient algorithm to solve it. ;o)

Solution
--------

{% codeblock lang:go p67.go %}
package main

import (
    "bufio"
    "fmt"
    "io"
    "os"
    "sort"
    "strconv"
    "strings"
)

func main() {
    f, err := os.Open("data_p67.txt")
    if nil != err {
        fmt.Println(err)
        os.Exit(1)
    }
    defer f.Close()

    matrix := make([][]int, 0)
    reader := bufio.NewReader(f)
    for {
        line, err := reader.ReadString('\n')
        if nil != err || io.EOF == err {
            break
        }
        row := make([]int, 0)
        numbers := strings.Split(strings.Replace(line, "\n", "", -1), " ")
        for i := range numbers {
            number, _ := strconv.Atoi(numbers[i])
            row = append(row, number)
        }
        matrix = append(matrix, row)
    }

    for y := 0; y < len(matrix); y++ {
        for x := 0; x < len(matrix[y]); x++ {
            if y > 0 {
                greaterParentPathValue := 0
                if x > 0 {
                    greaterParentPathValue = matrix[y-1][x-1]
                }
                if x < len(matrix[y-1]) && matrix[y-1][x] > greaterParentPathValue {
                    greaterParentPathValue = matrix[y-1][x]
                }
                matrix[y][x] += greaterParentPathValue
            }
        }
    }

    sort.Sort(sort.Reverse(sort.IntSlice(matrix[len(matrix)-1])))
    fmt.Println(matrix[len(matrix)-1][0])
}
{% endcodeblock %}

I'm the 50650th person to have solved this problem.
