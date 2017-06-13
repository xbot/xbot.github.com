---
layout: post
title: "解決ROX-Filer文件類型識別問題"
date: 2013-05-17 12:52
comments: true
categories: 計算機
tags:
- Linux
- ROX-Filer
- Office
- MIME
---
默認情況下，ROX-Filer會將“\*.docx”、“\*.xlsx”、“\*.pptx”文檔識別為zip壓縮包。由於ROX中與文件關聯的行為實際上都是和文檔類型掛鉤的，所以對這些文檔的操作會遇到很大麻煩，而且沒有變通方法。

實際上，Linux對文件類型的識別比Windows靈活。Windows只能通過文件名的後綴判斷文件類型，隨便創建一個文本文件，然後將後綴“.txt”改成“.exe”，Win就會傻乎乎地認為這是個二進制的可執行程序。這種做法很傻，而且很不安全，試想，如果將一個病毒程序的後綴改成“.jpg”，就可以騙過大多數用戶，然後通過某種手段執行它，後果會怎樣？更傻的是，XP以上版本默認是隱藏後綴的，那攻擊者連改後綴都可以省了。顫抖吧，神一樣的Win！

Linux不但可以通過後綴識別文件類型，更重要的，還可以通過文件開頭的幾個字節實現這一點，這就比前一種方式精確、安全很多。當然，Linux還支持更多的文件類型識別方法。

前面說過，在ROX-Filer下，所有行為都綁定到文件類型上，也就是MIME Types，ROX通過這種方式實現了將可靈活自由定製的文件操作與自身解耦，從而在確保自身穩定的同時實現對高度靈活的自由定製的支持，這是一個很值得學習的實現方式。因此，既然文件的後綴沒有問題，ROX仍將它們識別為ZIP格式，原因是什麼？顯然，最大的嫌疑集中在第二種識別方式上。

一個公開的秘密是，Office文檔本身其實就是一個ZIP壓縮包，裡面包含了描述文檔的XML、多媒體文件等成分，只不過MS賤賤地把壓縮包的後綴改成了docx之類的東東，加上Windows只能通過後綴識別文件類型，所以很多人不知道這一點。瞭解了這個，問題的原因就躍然紙上了，既然都是ZIP壓縮包，那第二種文件類型識別方式也就區分不出Office文件類型與ZIP壓縮包了。

能號出病因，就有方子治病。

既然ROX使用多種文件類型識別方式，那必然有一個優先級的關係，否則就會亂套。打開ROX的MIME Editor：

{% img http://pic.yupoo.com/leninlee/CRNkfq0g/medium.jpg %}

找到MIME類型“application/zip”：

{% img http://pic.yupoo.com/leninlee/CRNkgaEK/medium.jpg %}

打開zip的屬性對話框：

{% img http://pic.yupoo.com/leninlee/CRNkfOsQ/medium.jpg %}

可以看到“Contents matching”這一塊裡，通過檢查文件頭部的幾個字節是否為“PK\003\004”來判斷，並且此項判斷標準的優先級是40。下面所要做的，就是找到相應文件類型，添加一項條件相同的“Contents matching”，並把優先級設得大於40。
