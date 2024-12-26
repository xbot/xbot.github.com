---
title: 用MySQL的全文检索实现相关性排序
slug: fulltext searching in mysql
date: 2017-06-19 23:10:41
tags:
- mysql
- 全文检索
- 数据库
- 编程
- 计算机
---

实现根据文章的关键词搜索出相关的文章，并按相关性排序。

数据库版本是5.6.x，还不能像5.7那样支持中文的全文检索，所以另外创建一列保存关键词的编码。方法为base64转码并去掉「%」符号：

```php
<?php
/**
 * 转换关键词到全文检索的格式
 *
 * @param   mixed $keywords 关键词数组或英文逗号分隔的字符串
 * @return  string
 */
function encode_keywords($keywords)
{
    $encoder = function ($keyword) {
        return str_replace('%', '', urlencode(trim($keyword)));
    };
    if (!is_array($keywords)) {
        $keywords = explode(',', $keywords);
    }
    return implode(' ', array_map($encoder, $keywords));
}
```

对该列创建全文检索的索引：

```sql
CREATE FULLTEXT INDEX idx_post_keywords ON posts (keywords_ft);
```

查询语句：

```sql
SELECT
    id,
    title,
    keywords,
    (MATCH (keywords_ft) AGAINST ('E697B6E5B09A E5A8B1E4B990E59C88' IN NATURAL LANGUAGE MODE)) AS `score`
FROM
    posts
WHERE
    MATCH (keywords_ft) AGAINST ('E697B6E5B09A E5A8B1E4B990E59C88' IN NATURAL LANGUAGE MODE)
ORDER BY score DESC
LIMIT 10
```

