# 欧拉工程第七解

<p>第七解：</p>

<blockquote><p>By listing the first six prime numbers: 2, 3, 5, 7, 11, and 13, we can see that the 6th prime is 13.</p>

<p>What is the 10001st prime number?</p></blockquote>

<p>穷举，并加以最大程度的优化：对大于2的素数，只判断奇数；判断一个奇数是否素数时，只拿已经找到的素数中小于第这个数平方根的数来相除，如果均不能整除，就是素数。Python的实现：</p>

<p>
```python
def IsPrimeNum(num,feed):
    from math import sqrt
    tmp = feed[:]
    while tmp[-1] > int(sqrt(num)):
        tmp.pop()
    for i in tmp:
        if num % i == 0:
            return False
    return True

limit = 10001
feed = [2,3,5,7]
temp = 7
counter = 4
while counter < limit :
    temp += 2
    if IsPrimeNum(temp,feed):
        feed.append(temp)
        counter += 1
print temp
```
</p>


<p>执行了一下，在我这里居然用了五百秒才出结果，神啊，差不多十分钟啊。想到PHP号称速度很快，于是用PHP重新实现了一下：</p>

<p>
```php
function IsPrimeNum($num,$feed){
    $base = floor(sqrt($num));
    foreach($feed as $i=>$v){
        if($v > $base){
            return true;
        }
        if($num % $v == 0){
            return false;
        }
    }
}

$limit = 10001;
$feed = array(2,3,5,7);
$counter = 4;
$tmp = 7;
while($counter < $limit){
    $tmp += 2;
    if(IsPrimeNum($tmp,$feed)){
        $counter++;
        $feed[] = $tmp;
    }
}

echo $tmp;
```
</p>


<p>还好，七十四秒出结果，看来PHP的牛皮不是吹的。当然，Lua会更快：</p>

<p>
```lua
function IsPrimeNum(num,feed)
    require('math')
    local limit = math.floor(math.sqrt(num))
    for i,v in ipairs(feed) do
        if v > limit then
            return true
        end
        if num % v == 0 then
            return false
        end
    end
end

local limit = 10001
local feed = {2,3,5,7}
local counter = 4
local tmp = 7
while counter < limit do
    tmp = tmp + 2
    if IsPrimeNum(tmp,feed) then
        counter = counter + 1
        table.insert(feed,tmp)
    end
end

print(tmp)
```
</p>


<p>执行完后还是吓了一跳，0.3秒，同样是语言，效率的差别咋就那么大呢？！我在想用Java会不会算到2009去。</p>

<p>我不相信这道题用Python就那么难解，下面是用递归实现的程序：</p>

<p>
```python
from math import sqrt

def GuessPrime(feed,limit):
    if feed == 2 :
        return [2]
    elif feed == 3 :
        return [2,3]
    
    tmp = int(sqrt(feed))
    primes = GuessPrime(tmp,limit)
    
    base = 0
    if tmp % 2 == 0:
        base = tmp + 1
    else:
        base = tmp
    for i in range(base,feed,2):
        flag = 0
        for j in primes:
            if i % j == 0:
                flag = 1
                break
        if flag == 0:
            primes.append(i)
            if len(primes) == limit:
                return primes
    
    return primes

limit = 10001
feed = 1000000
primes = GuessPrime(feed,limit)
print primes[limit-1]
```
</p>


<p>11秒就出了结果，说到底，算法的改进才是硬道理！</p>

<p><a href="http://picasaweb.google.com/lh/photo/Uqg0Cu8ZWv5tlQ84nbVGZw"><img src="http://lh4.ggpht.com/_ceUJ_lBTHzc/SQsdErNSeSI/AAAAAAAAAlA/D7Wia09MD6o/s400/project_euler_problem_007.png" /></a></p>

