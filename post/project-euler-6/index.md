# 欧拉工程第六解

<p>第六解：</p>

<blockquote><p>The sum of the squares of the first ten natural numbers is,
12 + 22 + &hellip; + 102 = 385</p>

<p>The square of the sum of the first ten natural numbers is,
(1 + 2 + &hellip; + 10)2 = 552 = 3025</p>

<p>Hence the difference between the sum of the squares of the first ten natural numbers and the square of the sum is 3025  385 = 2640.
Find the difference between the sum of the squares of the first one hundred natural numbers and the square of the sum.</p></blockquote>

<p>解：</p>

<p>
```python
sumSquare = 0
sum = 0
for i in range(1,101):
    sumSquare += i**2
    sum += i
print sum**2 - sumSquare
```
</p>


<p><a href="http://picasaweb.google.com/lh/photo/z7HPhLCtbrXymzF80GLxwQ"><img src="http://lh5.ggpht.com/lenin.lee/SQawOId6V3I/AAAAAAAAAkA/kHr7dd3P6Y8/s400/project_euler_problem_006.png" /></a></p>

