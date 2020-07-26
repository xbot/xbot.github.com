---
title: "MySQL 事务的一致性、隔离级别与幻读问题"
slug: Isolation Levels And Phantom Reads In MySQL
date: 2020-07-21T16:25:09+08:00
categories:
- 计算机
tags:
- MySQL
- 数据库
- 编程

---

## 数据库事务的一致性

数据库事务有四个特性：原子性（Atomicity）、一致性（Consistency）、隔离性（Isolation）、持久性（Durability），简称“ACID”。

其中，“一致性”指的是数据库层面的一致性，与应用层面的一致性不同。

数据库层面的一致性是说，事务具备把数据库从一个正确的状态迁移到另一个正确的状态的特性。所谓正确的状态，是指任何写入数据库的数据都满足数据库既定的规则，这些规则包括各种约束、级联回滚、触发器以及任何它们的组合。

与数据库层面的一致性不同，应用层面的一致性要求保证结果的正确性。例如，当执行如下 SQL 语句时：

```sql
update accounts set balance = balance + 1 where id = 3
```

我希望最终的结果是在事务开始时 id = 3 的这行数据 balance 列的值的基础上加一，否则，结果就是不正确的。

因此，只要写入的数据满足数据库的规则，就保证了数据库层面的一致性。**数据库层面的一致性并不保证数据正确**[^1]。

## MySQL 事务的隔离级别与幻读问题

### 隔离级别

隔离性是指：多个事务并发执行时，每个事务对数据库所做的更改必须与其它事务隔离。

隔离性是并发控制的主要目标[^2]，并发控制用来实现在保证应用层面一致性的前提下，尽可能快地对并发请求做出响应。由此可见，隔离性和并发能力是一对此消彼长的关系。极端情况下，如果并发的请求按顺序执行，隔离性是最好的，但是响应最慢，反之亦然。

在此基础上，产生了四个隔离级别的划分。由低到高依次为：读未提交、读已提交、可重复读、序列化。据前所述，它们的并发能力是依次下降的。

因此，**隔离级别的本质是通过适度地破坏隔离性来提高并发能力。**

### 隔离级别的问题

隔离级别不同，存在的问题也不一样。

“读未提交”的级别最低、并发能力最高，存在的问题也最多，包括：脏读、不可重复读、幻读。其中，脏读是这个级别独有的，即事务 A 读取了并发事务 B 未提交的数据。

“读已提交”顾名思义，事务 A 不会读取并发事务 B 未提交的数据，但可以读取其已提交的。因此，这个级别存在不可重复读和幻读的问题。

在“可重复读”级别下，同一事务中两次同样的查询结果一致，不会受并发事务的影响，它因此也解决了不可重复读的问题。但还会存在幻读问题。

“序列化”级别要求对查询的对象加范围锁并保持到事务结束，因此，它避免了幻读的问题。

### 幻读问题（Phantom Reads）

幻读问题是个很 tricky 的问题，以至于网上找到的很多文章对它的理解都是不全面甚至是错误的。

幻读即同一事务中两次相同查询的结果集不一致。乍一看，好像和不可重复读是一样的。那它们的区别是什么呢？

事实上，幻读是不可重复读的一种特殊情况。换句话说，**只要存在幻行（Phantom Rows），就是幻读问题**。

所谓幻行，即同一事务中两次相同的查询结果集的非空差集里的元素，或者说第二次查询后发现不存在于前一次结果集中的行。

幻行产生的原因主要是除“序列化”之外的隔离级别不要求对查询结果加范围锁，导致并发事务在查询范围内插入新的行后被当前事务查到。

#### MVCC

在此基础上，MySQL 的 InnoDB 引擎实现了“多版本并发控制”（MVCC: Multiversion Concurrency Control）。

简单地说，InnoDB 的表存在两个隐藏列，用于记录每行数据的版本信息。当插入一行数据时，InnoDB 将当前事务的版本信息一并写入。当修改一行数据时，InnoDB 先将该行做一次复制，并把当前版本信息写入进去。查询时，只返回版本小于等于当前事务版本的数据。通过这样保证一个事务中查询到的是事务开始前已经存在的数据或当前事务写入的数据。其它事务写入的数据则不会被读取。

从这个意义上讲，InnoDB 通过 MVCC 解决了这种幻读问题。

#### Next-Key Lock

上述普通的 SELECT 查询属于 InnoDB 读操作的一种：快照读。此外，还存在一种“当前读”。快照读顾名思义读取的是快照中的内容，而当前读读取的是当前最新的数据。INSERT、DELETE、UPDATE、SELECT ... FOR UPDATE 都是当前读。

因此，还存在一种特殊情况：

![并发事务插入同一主键的数据导致的幻读问题](https://www.plantuml.com/plantuml/svg/dP91Inj15CVlyoaUUbWZQzaau26WQ9iA7Wf5Ul4uoRx4mTsPC3jRzIP5gTQqKL4KyQ8WtX8F8j7aspIp9gT-XRx482nuybtUpEFFx__Ucrc83DSc3WF6edKXQrpp4FoQWBpdCMQ42H3iuuDj7FMU3kolgwHnR7Tlx66zrzxkjJk3a-RpnKvoUfVyRjfMj__pdgxsiTDlTGPdpS7rEGkmYj9dGF52W6-RMUYr_r3EZT79_ct_glwlMnS-sBspv7BVdhJ8uFd-sdPF_sx_O8I0COdnib5wI64-Dxp48uJYnzTgZWInKJ8EIwWP6vhDpCoJeW0bh0h9XhRfIaeKOO1b0zDGqIg4E48TmSOQQWHgRWxoJ2g3e4LrpO2gZ8QPzBnFdulP0dYWrKO4GXB5i9IJa4Gob8mQGwU4xmAleWkIXvY1xpo8aSfvbtRlWkChAT39f2Xb5OR2Z9QPQlhzz3UcMLfQf2Z0LDQRpRamKOnhWIXpqg4qUWkSl0CLslKwqddazk7holANnPLLPoB3s08z4lsd_m00 "并发事务插入同一主键的数据导致的幻读问题")

以及另一种特殊情况：

![更新并发事务插入的数据导致的幻读问题](https://www.plantuml.com/plantuml/svg/pPBFJjj04CRl-nGZzP2c2fAJ2Acg14A5la5VO1DFaXNsRcIleOSUeWh4Vo8G0LIWKY9Lt0Y7g8AIjsdM9AU-GcTj2_2XTtpvTdTcV_jz8o-5cWSwyZt6mbKXsppWFhXj0LN7OKmBxI745wFuzDRS_pBTeydlFRFRZxl7PlFdu_UDk7SR7zoOuNYwDQAjkN-O3XzcPpUpmJdpi8dIPK1VUWi-Vod3vEwGj08fBjwvdlRt_uxtUHF1R9_5bpkpgn_cP3YvwpoE1cRS-zFvnWW36OtnkbQ19J6NQrxZ8SBAnwVJ2ZL4Mid8hs70c7rmRcwHEZv03Pj2Cljq_YJN5AA7TGtle14e7w8GWn3MMnWW29VwzJgYXB83d1ozTvXK6Y4GpPO6rKZYlN6SvKyhvIJrLtl9YWrdLN8_MMJLIYhpJggkaW9jrSQrP9PpAoIvqIHQPSRU2hTasQK4N8GrxaL8nzKICKjGI825OevILxulT68W5pngqpWn0uUeKwyBKD3ABsGZi34M2Frg9aRUu4Mr-f_gypoNQ4d_-Zy0 "更新并发事务插入的数据导致的幻读问题")

以上两种情况都是在有当前读操作时可能发生的幻读问题。这是 MVCC 无法解决的。

InnoDB 用 Next-Key Lock 解决这种幻读问题。

Next-Key Lock 实际上是记录锁（Record Lock）和间隙锁（Gap Lock）的结合。加锁规则如下：

1. Next-Key Lock 的格式是左开右闭。例如：( 5, 9 ] 表示间隙 ( 5, 9 ) 的间隙锁和 9 的行锁。
2. 对查询条件范围的“行和间隙”（如果存在）加锁。
3. 对于等值查询：
   1. 对于唯一索引，Next-Key Lock 退化为行锁。
   2. 向右遍历至查询条件范围内最后一个值右侧节点时，若该节点不满足等值条件，Next-Key Lock 退化为间隙锁。

例如，users 表结构如下：

| 列名 | 类型     | 索引   |
| ---- | -------- | ------ |
| id   | bigint   | 唯一   |
| age  | smallint | 不唯一 |

表中的数据：

| id   | age  |
| ---- | ---- |
| 1    | 10   |
| 5    | 50   |
| 11   | 30   |

对于上述第一种问题，事务 A 中通过任何一种当前读操作 id = 3 的行（例如 INSERT），都会自动给这个索引值加记录锁。此时，事务 B 中再插入 id = 3 的数据时就会被阻塞。从而避免了这种幻读问题。

对于第二种问题，事务 A 中对 age = 30 的数据加排他锁：

```sql
select * from users where age = 30 for update
```

数据库会加 ( (10, 1), (30, 11) ] 和 ( (30, 11), (50, 5) ] 两个 Next-Key Lock 。注意，由于 InnoDB 使用 B+ 树存储索引，且对于辅助索引， B+ 树叶子节点存储的是索引值和主键值，所以这里间隙用 `( ( 头节点索引值, 头节点主键值 ), ( 尾节点索引值, 尾节点主键值 ) )` 表示。而且，由于 (30, 11) 右侧间隙的尾结点 (50, 5) 不满足等值条件，所以这个 Next-Key Lock 退化为间隙锁 ( (30, 11), (50, 5) ) 。

此时，事务 B 更新 age = 30 的数据时将会被阻塞。也就避免了这种幻读问题。

#### 总结

InnoDB 通过 MVCC 解决了普通查询中的幻读问题，通过 Next-Key Lock 解决了当前读导致的幻读问题。

[^1]: https://en.wikipedia.org/wiki/Consistency_(database_systems)
[^2]: https://en.wikipedia.org/wiki/ACID#Isolation

