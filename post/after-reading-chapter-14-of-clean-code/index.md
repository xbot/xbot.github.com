# 《Clean Code》第十四章读后


蹩脚的译文看着实在费解，干脆去看《Clean Code》的原版。第14章“Successive Refinement（逐步改进）”讲的是对一个用Java实现的解析命令行参数的类Args重构的过程。

很多人都应该好好看看这章。

<!--more-->

如果你觉得用了类就是面向对象编程，就去看看Args的第一版代码，一定程度上它是面向过程的。这种代码在我们项目中比比皆是。

如果你觉得重构代码容易出问题，去看看作者是怎么利用单元测试保证重构的质量的。

如果你觉得只要能实现业务逻辑就万事大吉，去看看这章结尾的总结部分：

> Programmers who satisfy themselves with merely working code are behaving unprofessionally. They may fear that they don’t have time to improve the structure and design of their code, but I disagree. Nothing has a more profound and long-term degrading effect upon a development project than bad code.

第一句的意思就是——抱有这种看法的人是不专业的。

接下来作者说：

> But bad code rots and ferments, becoming an inexorable weight that drags the team down. Time and time again I have seen teams grind to a crawl because, in their haste, they created a malignant morass of code that forever thereafter dominated their destiny.

很多人总是抱怨老项目的代码写得不好，而我审核他们代码的时候听到最多的话是“这回先这样吧”……亲，你正在成为自己厌恶的人，而你终将被别人厌恶！

本章开头的部分有一段话：

> Most freshman programmers (like most grade-schoolers) don’t follow this advice particularly well. They believe that the primary goal is to get the program working. Once it’s “working,” they move on to the next task, leaving the “working” program in whatever state they finally got it to “work.” Most seasoned programmers know that this is professional suicide.

当你还是个“freshman”的时候，你觉得精益求精无关紧要是可以被短暂地容忍的，而当你不再fresh的时候还表现得像个freshman，你的职业生涯几乎就走到头了（professional suicide）。

