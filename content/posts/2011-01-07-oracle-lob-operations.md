---
layout: post
title: Oracle中对LOB字段的操作方法
slug: oracle lob operations
date: 2011-01-07 00:00:00
tags:
- oracle
- SQL
- 编程
status: publish
comments: true
meta:
  _edit_last: '1'
  views: '613'
  _wp_old_slug: ''
---
在Oracle中插入或更新LOB字段时，可以将字符串以如下方式写入SQL语句：

```sql
insert tbl_lob (fld_lob) values (utl_raw.cast_to_raw('hello world'));
```

这样做的限制是：

<ol>
	<li>Oracle中单条SQL语句有长度限制</li>
	<li>cast_to_raw()对字符串有长度限制</li>
</ol>

因此如果要插入或更新的字符串过长，会导致执行失败。此时应该使用dbms_lob处理LOB相关字段。下面是利用dbms_lob更新一个BLOB字段的存储过程，对CLOB的操作同理：

```sql
create or replace procedure updateblob(
    ctbl in varchar2,
    cfld in varchar2,
    cstr in varchar2,
    ccond in varchar2
)
is
    vqry varchar2(1000);
    vblob blob;
    vbatch varchar2(2000);
    vstrlen number;
    voffset number :=1;
    vamt number :=2000;
begin
    vstrlen := length(cstr);

    vqry := 'update '||ctbl||' set '||cfld||'=empty_blob() where '||ccond;
    execute immediate vqry;

    vqry := 'select '||cfld||' from '||ctbl||' where '||ccond||' for update';
    execute immediate vqry into vblob;

    if vstrlen>vamt then
        while vstrlen>voffset loop
            vbatch := substr(cstr, voffset, vamt);
            voffset := voffset+vamt;
            dbms_lob.writeappend(vblob, length(vbatch), utl_raw.cast_to_raw(vbatch));
        end loop;
    else
        dbms_lob.writeappend(vblob, length(cstr), utl_raw.cast_to_raw(cstr));
    end if;
    commit;
end;
```

示例：

```sql
-- 将tbl_lob表的fld_lob字段的值改为“hello lob !”，要求被更改的行满足条件：
-- 1. fld_code字段的值以“2011”开头
-- 2. fld_name字段的值等于“Hell”

declare
    vcond varchar2(1000);
begin
    vcond := 'fld_code like ''2011%'' and fld_name=''Hell''';
    updateblob('tbl_lob', 'fld_lob', 'hello lob !', vcond);
end;
```
