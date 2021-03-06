# 欧拉工程第一解

<div style="float:right;margin-left:10px;margin-bottom:10px;">
	<a href="http://picasaweb.google.com/lh/photo/AAMfMJges97jrmxxYmmsXw"><img src="http://lh5.ggpht.com/lenin.lee/SOjhkJQQ7HI/AAAAAAAAAbk/HT6_ZIxVCJE/s144/euler.jpg" /></a>
</div>
<p>
“<a href="http://projecteuler.net/">欧拉工程</a>”是一个很有意思的网站，它每周会提供一道数学题，要求访问者使用任一种编程语言设计一个计算机程序求解。到现在为止已经出了二百一十一道题，当然，题的难度是依次递增的。几十个国家的程序员已参与了这个工程，截至目前，中国有四百多人参与，但是解决所有的二百多道题的只有一个人。
</p>
<p>
我觉得没事儿的时候做一道很有意思，下面是第一道，很简单：
</p>
	<blockquote>
	If we list all the natural numbers below 10 that are multiples of 3 or 5, we get 3, 5, 6 and 9. The sum of these multiples is 23.
	Find the sum of all the multiples of 3 or 5 below 1000.
	</blockquote>
<p>
最容易想到的一算法就是依次取出一到一千的整数，只要是三或五的倍数，就累加起来，最终的和就是结果：
</p>
```python
sum = 0

for num in range(1,1000):
	if not (num % 3 != 0 and num % 5 != 0):
		sum += num

print sum
```
<p>
但是我觉得这个算法太普通了，从一到一千要做一千次循环，时间复杂度会比较高。所以我设想只取出三和五的倍数，然后相加就行了，所需要考虑的只是怎么处理三和五的公倍数的问题。下面是我的算法，只有三百多次循环：
</p>
```python
def SumMultiple(feed,limit):
    threeMultiple = 3 * feed
    fiveMultiple = 5 * feed
    
    if threeMultiple >= limit:
        return None
    if fiveMultiple >= limit:
        return threeMultiple
    if fiveMultiple % 3 == 0:
        return threeMultiple
    
    return threeMultiple + fiveMultiple

sum = 0

for feed in range(1,500):
    if SumMultiple(feed,1000) == None:
        break;
    sum += SumMultiple(feed,1000)

print sum
```
<p>
不过事与愿违，通过测试，这个算法的效率要比上一种低，我想应该是SumMultiple()函数中运算和比较次数较多导致的。
</p>
<p>
不管怎样，第一个问题已经顺利解决了：
</p>
<div>
	<a href="http://picasaweb.google.com/lh/photo/pqzQqPwZre4daUJCjDfoyA"><img src="http://lh6.ggpht.com/lenin.lee/SOjhkRV8w6I/AAAAAAAAAbs/ZL7QKztQm4E/s400/project_euler_problem_1.png" /></a>
</div>

