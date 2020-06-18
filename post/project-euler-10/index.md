# 欧拉工程第十解

<blockquote><p>The sum of the primes below 10 is 2 + 3 + 5 + 7 = 17.</p>

<p>Find the sum of all the primes below two million.</p></blockquote>

<p>题目越来越变态，开始好玩儿了。</p>

<p><a href="http://0x3f.org/?p=753">第七解</a>里的算法在这里算是废了，一万个素数都算得那么费劲，两百万以下的素数有十几万个，不得不用筛选法了。</p>

<p>普通的筛选效率也不行，当初就是因为这个原因才没用它。不过优化过的筛选法就很奇妙了，下面是Lua的实现：</p>

<p>
```lua
require('math')

local limit = 2000000

local primes = {}
for i=1,limit do
    table.insert(primes,true)
end
primes[0] = false
primes[1] = false

for i=0,math.floor(math.sqrt(limit)) do
    if primes[i] then
        for j=math.pow(i,2),limit,i do
            primes[j] = false
        end
    end
end

local sumVal = 0
for i,j in ipairs(primes) do
    if j then
        sumVal = sumVal + i
    end
end

print(sumVal)
```
</p>


<p>在我这里两秒半就出结果了，Python的表现也不错，四秒半出结果：</p>

<p>
```python
from math import sqrt

limit = 2000000
primes = [True for i in range(0,limit)]
primes[0] = False
primes[1] = False

for i in range(1,int(sqrt(limit))+1):
    if primes[i]:
        for j in range(i**2,limit,i):
            primes[j] = False

sumVal = 0
for i in range(len(primes)):
    if primes[i]:
        sumVal += i

print sumVal
```
</p>


<p><a href="http://picasaweb.google.com/lh/photo/va8CeXPxRtCojlNw4qpmBQ"><img src="http://lh5.ggpht.com/_ceUJ_lBTHzc/SSIt7sQQ5RI/AAAAAAAAAms/5sZPp1KBi_4/s400/project_euler_problem_010.png" /></a></p>

