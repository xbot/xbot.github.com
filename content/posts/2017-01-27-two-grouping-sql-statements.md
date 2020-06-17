---
layout: post
title: "两条分组的SQL语句"
slug: two grouping sql statements
date: 2017-01-27 17:51:00
comments: true
categories:
- 计算机
tags:
- sql
- 编程
---

最近做访问统计解决的两个问题：

## 根据日期分组

```sql
select (
           case
           when visit_at >= 1485014400 then from_unixtime(visit_at, '%Y-%m-%d')
           when visit_at >= 1484928000
       and visit_at < 1485014400 then from_unixtime(visit_at, '%Y-%m-%d')
           else 0
       end
       ) col, count(*) as pv, count(distinct session_id) as uv
  from `statistics`
 where `visit_at`    >= 1484496000
 group by (
           case
           when visit_at >= 1485014400 then from_unixtime(visit_at, '%Y-%m-%d')
           when visit_at >= 1484928000
       and visit_at      <  1485014400 then from_unixtime(visit_at, '%Y-%m-%d')
           else 0
       end
       )
```

## 选择每组数据的第一条

```sql
select a.session_id,a.created_at,a.province,a.referer,a.uri,a.ipv4,
       a.user_id,b.duration,b.num
  from statistics as a
 inner join (
        select session_id,
               max(created_at) as latest_time,
               count(*) as num,sum(duration) as duration
          from statistics
 group by session_id
       ) as b
    on a.session_id=b.session_id
   and a.created_at=b.latest_time
 ORDER BY a.created_at desc
```
