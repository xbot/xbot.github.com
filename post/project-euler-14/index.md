# Project Euler Problem 14 Solved


Longest Collatz sequence
------------------------

The following iterative sequence is defined for the set of positive integers:

n → n/2 (n is even)  
n → 3n + 1 (n is odd)

Using the rule above and starting with 13, we generate the following sequence:

13 → 40 → 20 → 10 → 5 → 16 → 8 → 4 → 2 → 1

It can be seen that this sequence (starting at 13 and finishing at 1) contains 10 terms. Although it has not been proved yet (Collatz Problem), it is thought that all starting numbers finish at 1.

Which starting number, under one million, produces the longest chain?

NOTE: Once the chain starts the terms are allowed to go above one million.

Solution
--------

```go
package main

import (
    "fmt"
    "math"
    "time"
)

func collatz(n int) int {
    if n == 1 {
        return 1
    }
    if math.Mod(float64(n), 2) == 0 {
        return collatz(n/2) + 1
    } else {
        return collatz(n*3+1) + 1
    }
}

func main() {
    start := time.Now()

    i, iLen, count, max := 1000000, 0, 0, 0
    for i > 1 {
        iLen = collatz(i)
        if iLen > count {
            count = iLen
            max = i
        }
        i--
    }

    end := time.Now()
    fmt.Println(end.Sub(start), max, count)
}
```

I'm the 104188th person to have solved this problem.

