# Project Euler Problem 24 Solved


Lexicographic permutations
--------------------------

A permutation is an ordered arrangement of objects. For example, 3124 is one possible permutation of the digits 1, 2, 3 and 4. If all of the permutations are listed numerically or alphabetically, we call it lexicographic order. The lexicographic permutations of 0, 1 and 2 are:

012   021   102   120   201   210

What is the millionth lexicographic permutation of the digits 0, 1, 2, 3, 4, 5, 6, 7, 8 and 9?

Solution
--------

```python
#!/usr/bin/python2
# -*- coding: utf-8 -*-

from math import factorial


def get_perm(digits, number):
    if len(digits) == 0:
        return ''
    (perm, counter) = ('', factorial(len(digits) - 1))
    for digit in digits:
        if counter >= number:
            digits.remove(digit)
            perm += str(digit) + get_perm(digits, number)
            break
        number -= counter
    return perm


if __name__ == '__main__':
    import time
    startTime = time.time()
    perm = get_perm([d for d in range(10)], 1000000)
    print perm, '%sms' % ((time.time() - startTime) * 1000)
```

```php
<?php
function factorial($n) {
    $result = 1;
    for ($i = 2;$i <= $n;$i++) $result*= $i;
    return $result;
}

function get_perm($digits, $number) {
    $perm = "";
    $length = count($digits);
    $counter = factorial($length - 1);
    for ($i = 0;$i < $length;$i++) {
        if ($counter >= $number) {
            $segment = array_splice($digits, $i, 1);
            $perm = $segment[0] . get_perm($digits, $number);
            break;
        }
        $number-= $counter;
    }
    return $perm;
}

$startTime = microtime(true);
$digits = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
$perm = get_perm($digits, 1000000);
$costs = (microtime(true) - $startTime) * 1000;
echo "$perm ${costs}ms\n";
?>
```

```go
package main

import (
	"fmt"
	"strconv"
	"time"
)

func factorial(n int) int {
	result := 1
	for i := 2; i <= n; i++ {
		result *= i
	}
	return result
}

func get_perm(digits []int, number int) string {
	perm, length := "", len(digits)
	counter := factorial(length - 1)
	for i := 0; i < length; i++ {
		if counter >= number {
			digit := digits[i]
			digits = append(digits[:i], digits[i+1:]...)
			perm += strconv.Itoa(digit) + get_perm(digits, number)
			break
		}
		number -= counter
	}
	return perm
}

func main() {
	startTime := time.Now()
	digits := []int{0, 1, 2, 3, 4, 5, 6, 7, 8, 9}
	perm := get_perm(digits, 1000000)
	fmt.Println(perm, time.Now().Sub(startTime))
}
```

I'm the 57181st person to have solved this problem.

I've just advanced to Level 1. 61264 members (15.9%) have made it this far.

I have earned 1 new award:

The Journey Begins: Progress to Level 1 by solving twenty-five problems

