# 欧拉工程第三解

<p>第三解：</p>

<blockquote><p>The prime factors of 13195 are 5, 7, 13 and 29.
What is the largest prime factor of the number 600851475143 ?</p></blockquote>

<p>解：</p>

<p>
```python
feed = 600851475143

def GetFactor(feed,footmark):
    while footmark < feed:
        footmark += 2
        if feed % footmark == 0:
            print footmark
            GetFactor(feed / footmark,footmark)
            break

GetFactor(feed,1)
```
</p>

<p><a href="http://picasaweb.google.com/lh/photo/CQ25Wrocadk-dyd7sOiRUA"><img src="http://lh6.ggpht.com/lenin.lee/SP3inG0AczI/AAAAAAAAAjA/DBMygQUR9HI/s400/project_euler_problem_003.png" /></a></p>

