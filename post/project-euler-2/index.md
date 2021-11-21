# 欧拉工程第二解

<p>第二解：</p>

<blockquote><p>Each new term in the Fibonacci sequence is generated by adding the previous two terms. By starting with 1 and 2, the first 10 terms will be:
1, 2, 3, 5, 8, 13, 21, 34, 55, 89, &hellip;
Find the sum of all the even-valued terms in the sequence which do not exceed four million.</p></blockquote>

<p>解：</p>

<p>
```python
i = 1
j = 2
sum = 0
while j < 4000000 :
    if j % 2 == 0 :
        sum = sum + j
    t = i
    i = j
    j = t + j
print sum
```
</p>


<p><a href="http://picasaweb.google.com/lh/photo/hFwGEgy5zPFOgWFUSsv22w"><img src="http://lh5.ggpht.com/lenin.lee/SPtY3kR_AxI/AAAAAAAAAig/Zq7x_Ht99vQ/s400/project_euler_problem_2.png" /></a></p>
