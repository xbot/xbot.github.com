# SQL Server的自定义函数：统计两日期之间工作日的数量

<p>前段时间工作中遇到的一个需求，要求计算两个日期之间工作日的数量，即排除期间所有的周六和周日之后的天数。</p>

<p>在网上找到一个自定义函数，原函数有些小问题，例如如果传入的截止日期如果有时间且足够大，则计算结果可能出错，修正后代码如下：</p>

<p>
```sql
--函数：类似datediff，不统计期间所有的周六和周日
if exists (select * from dbo.sysobjects where id=object_id(N'[dbo].[f_WorkDay]') and xtype in (N'FN',N'IF',N'TF')) 
drop function [dbo].[f_WorkDay] 
GO

CREATE FUNCTION f_WorkDay(
@dt_begin   datetime,
@dt_end     datetime 
)RETURNS int 
AS 
BEGIN 
    DECLARE @workday int,@i int,@bz bit,@dt datetime 

    set @dt_begin = convert(datetime, convert(nvarchar(32), @dt_begin, 23))
    set @dt_end = convert(datetime, convert(nvarchar(32), @dt_end, 23))    

    IF @dt_begin>@dt_end 
        SELECT @bz=1,@dt=@dt_begin,@dt_begin=@dt_end,@dt_end=@dt 
    ELSE 
        SET @bz=0 

    SELECT @i=DATEDIFF(Day,@dt_begin,@dt_end),@workday=@i/7*5,@dt_begin=DATEADD(Day,@i/7*7,@dt_begin) 

    WHILE @dt_begin<@dt_end 
    BEGIN 
        SELECT @workday=CASE WHEN (@@DATEFIRST+DATEPART(Weekday,@dt_begin)-1)%7 BETWEEN 1 AND 5 THEN @workday+1 ELSE @workday END,@dt_begin=@dt_begin+1 
    END 

    RETURN(CASE WHEN @bz=1 THEN -@workday ELSE @workday END) 
END 
GO
```
</p>

<p>此函数的原理是，使用datediff计算两日期之间的差值A，然后取A与7的商，即计算期间内有几个整周。然后使用这个商与5相乘，得到所有整周内的工作日天数B。接着，使用A除以7再乘以7得到C，这就约去了A中最后不到一个整周的天数。再在起始日期的基础上加上C，得到一个新的起始日期，然后从这个新的起始日期开始遍历至截止日期的每一天，每增加一天，判断若此日期是工作日，则在C的基础上累加一。判断一个日期（假设使用@dt_begin表示）是否是工作日的方法是：<strong>(@@datefirst+datepart(Weekday, @dt_begin)-1)%7</strong>的值在1和5之间。</p>

<p>此外还有一个需求是计算两个日期之间排除最后一个周六周日后的天数，仿照上面的函数实现了一个新函数，现在想来，有点儿把问题复杂化了，完全可以直接从后往前推的。</p>

<p>
```sql
--函数：类似datediff，不统计截止日期@dt_end前最近一次的周六和周日，若dt_end是周日，则不统计其前面的那个周六。
if exists (select * from dbo.sysobjects where id=object_id(N'[dbo].[f_WorkDayOnce]') and xtype in (N'FN',N'IF',N'TF')) 
drop function [dbo].[f_WorkDayOnce]
GO

CREATE FUNCTION f_WorkDayOnce(
@dt_begin   datetime,
@dt_end     datetime 
)RETURNS int 
AS 
BEGIN 
    DECLARE @day_count int,@weekday int,@weekend_dropped int,@i int,@bz bit,@dt datetime 

    set @dt_begin = convert(datetime, convert(nvarchar(32), @dt_begin, 23))
    set @dt_end = convert(datetime, convert(nvarchar(32), @dt_end, 23))    

    IF @dt_begin>@dt_end 
        SELECT @bz=1,@dt=@dt_begin,@dt_begin=@dt_end,@dt_end=@dt 
    ELSE 
        SET @bz=0 

    SELECT @i=DATEDIFF(Day,@dt_begin,@dt_end),@weekday=(@@datefirst+datepart(weekday,@dt_end)-1)%7,@day_count=@i,@weekend_dropped=0

    if @i=0
        set @day_count=0
    else
    begin
        if @weekday=0
            set @day_count=@i-1
        else
        begin
            while @dt_begin<@dt_end and @weekend_dropped<2
            begin
                select @weekend_dropped=@weekend_dropped+(case when (@@datefirst+datepart(weekday,@dt_begin)-1)%7 between 1 and 5 then 0 else 1 end),@dt_begin=@dt_begin+1
            end
            select @day_count=@day_count-@weekend_dropped
        end
    end

    RETURN(CASE WHEN @bz=1 THEN -@day_count ELSE @day_count END) 
END 
GO
```
</p>

