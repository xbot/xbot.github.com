# 欧拉工程第四解

<p>第四解：</p>

<blockquote><p>A palindromic number reads the same both ways. The largest palindrome made from the product of two 2-digit numbers is 9009 = 91 × 99.
Find the largest palindrome made from the product of two 3-digit numbers.</p></blockquote>

<p>解：</p>

<p>
```python
largestPalindrome = 0
for i in range(100,1000):
    for j in range(100,1000):
        product = i * j
        if int(str(product)[::-1]) == product and product > largestPalindrome:
            largestPalindrome = product
print largestPalindrome
```
</p>


<p>穷举，有没有效率高的办法？</p>

<p><a href="http://picasaweb.google.com/lh/photo/L1O4_C06B1wqUYys6WdzjQ"><img src="http://lh5.ggpht.com/lenin.lee/SP8j5zDSfaI/AAAAAAAAAjI/oq1cJ0N19q0/s400/project_euler_problem_004.png" /></a></p>

