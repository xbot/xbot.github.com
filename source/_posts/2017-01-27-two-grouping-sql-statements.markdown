---
layout: post
title: "兩條分組的SQL語句"
date: 2017-01-27 17:51
comments: true
categories: 計算機
tags:
- sql
- 編程
---

最近做訪問統計解決的兩個問題：

## 根據日期分組

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

## 選擇每組數據的第一條

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
