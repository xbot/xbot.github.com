# 欧拉工程第五解

<p>第五解：</p>

<blockquote><p>2520 is the smallest number that can be divided by each of the numbers from 1 to 10 without any remainder.
What is the smallest number that is evenly divisible by all of the numbers from 1 to 20?</p></blockquote>

<p>解：</p>

<p>
```python
#为简洁明了，此处不作校验
def GetGreatestCommonDivisor(min,max):
    '''辗转相除法求最大公约数'''
    while min > 0:
        tmp = min
        min = max % min
        max = tmp
    return max

def GetLeastCommonMultiple(a,b):
    if a > b:
        max = a
        min = b
    else:
        max = b
        min = a
    div = GetGreatestCommonDivisor(min,max)
    return min * max / div

temp = 1
for i in range(1,21):
    temp = GetLeastCommonMultiple(i,temp)
print temp
```
</p>


<p>本题旨在求最小公倍数。此算法有意思的是，它的精华在于如何求解两个正整数的最大公约数，有点<a href="http://zh.wikipedia.org/wiki/桂陵之战">围魏救赵</a>的意思。</p>

<p>在<a href="http://w5qy.blog.sohu.com/70238826.html">这里</a>可以找到另外一些求解最小公倍数的方法。</p>

<p><a href="http://picasaweb.google.com/lh/photo/3qs_t5dw-36_1G8wU1jAsQ"><img src="http://lh3.ggpht.com/lenin.lee/SQXXSSxErdI/AAAAAAAAAjg/HwT1UKkWKns/s400/project_euler_problem_005.png" /></a></p>

