# 欧拉工程第九解

<blockquote>
<p>A Pythagorean triplet is a set of three natural numbers, <b><em>a</em></b> < <b><em>b</em></b> < <b><em>c</em></b>, for which,</p>

<p><b><em>a</em></b><sup>2</sup> + <b><em>b</em></b><sup>2</sup> = <b><em>c</em></b><sup>2</sup></p>

<p>For example, 3<sup>2</sup> + 4<sup>2</sup> = 9 + 16 = 25 = 5<sup>2</sup>.</p>

<p>There exists exactly one Pythagorean triplet for which <b><em>a</em></b> + <b><em>b</em></b> + <b><em>c</em></b> = 1000.
Find the product <b><em>abc</em></b>.
</blockquote>


<p>解：</p>

<p>
```python
flag = False
for a in range(1,1000):
    for b in range(1,1000):
        if a ** 2 + b ** 2 == (1000 - a - b) ** 2:
            print a,b,(1000 - a - b)
            print a * b * (1000 - a - b)
            flag = True
            break
    if flag:
        break
```
</p>


<p><a href="http://picasaweb.google.com/lh/photo/klDX8mgZYS4EiKJ3qCQBKA"><img src="http://lh5.ggpht.com/_ceUJ_lBTHzc/SRhQhDVQ9aI/AAAAAAAAAmM/gTI91RFIk4M/s400/project_euler_problem_009.png" /></a></p>

