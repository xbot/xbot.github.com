---
title: 技术团队的两个最佳实践
slug: the best practices of developing
date: 2017-02-24 13:27:57
categories:
- 青梅煮酒
tags:
- 随笔
---

在以往的开发和管理中遇到很多问题，以至于我对理想中的技术团队有了一些想法。其中有两点经过实践证明有很强的可操作性，值得拿出来讨论一下：

1. 能用规则解决的问题就不要靠人解决
2. 能用机器完成的任务就不要用人处理

这两点都反映了我的一个妄念，就是相信规则和机器，不相信人。人是最不靠谱的生产者，你永远无法保证团队中的每个人都处于很好的状态、拥有相近的技术水平和情商，这也是为什么我不觉得结对编程有什么可操作性。但如果有简单可行的规则，用来规范开发过程中的行为，那么解决开发过程中的冲突就不需要人为地和稀泥，生产效率也会得到提升。此外，机器最适合用来做重复性的任务，很多花大量的人力、物力、时间都没做得很好的事，交给机器来做恰恰是最好的解决办法。

这些以往都只是我自己的想法，虽然在团队里有过很好的实践效果，但是并不指望和别人有所共鸣。国内的技术团队大多靠堆人、堆时间，很少有团队会把健康的世界观和可操作的方法论放在重要的位置，技术和技术从业者都是刍狗。当然这也无可厚非，毕竟先要解决生存问题，在模式创新为主的国内互联网行业，更新换代如此之快，产品早一天上线就多一分生存的可能。但是有没有可能既解决生存问题，又做出一个有荣誉感的团队呢？世间安得双全法，这是个值得持续讨论的问题。

最近和别人聊天，竟然听到相同的想法，在具体的方法论上还得到很多补充。

对于第一点。一个项目按业务线划分开发组、按功能模块划分开发任务本来是个很好的模式，但是接口的对接往往会有很多问题，例如术语使用的不严谨导致高昂的沟通成本、问题处理方法的不规范导致扯皮、冲突和低效。

在我的团队里，用wiki维护著一套术语词典，开发过程中所有的文档、沟通都必须使用既定的用语，例如「退单」包括「退款单」和「退货单」，这三个术语分别表示不同而精确的概念，如果因为自己造词产生歧义或错用术语导致开发事故，责任是清楚的，问责对象也没有怨言。让有责任的人承担责任，比和稀泥对解决问题更有利。

再比如，问题在流转过程中很容易出现接口人之间的扯皮甚至冲突，问题的根源并不是别人说你的代码有问题导致你不爽，而是别人做得不够专业让你觉得对方不负责任。在我的团队里，大家约定处理问题的规则是：

* 谁接手，谁处理
* 转交问题时必须提供四项信息：复现问题的环境、完整的接口名、传递的实参和返回结果

首先当一个问题被反馈过来的时候，分配人会有一个初步的判断，指派给谁，谁处理，不能踢皮球。处理人如果界定问题发生在别人的接口里，应该把上述四项信息连同问题移交给相关责任人。这样做的好处是，一来可以避免误判给别人造成不必要的麻烦，二来让下游接口人可以马上复现并解决问题，而无须考虑上游的业务逻辑。这个规则的效果很好，团队里从来没有因为接口问题出现不愉快。

对于第二点。代码质量是日常开发中最让人头疼的问题之一，出现频率高而且代价昂贵。不管是靠开发人员的经验，还是测试人员的工作，都对人的依赖很大，既不稳定又低效。以下这些方法能很好地解决这样的问题：

* 使用Git，先进的生产关系需要更好的生产资料才能带来更高的生产力
* 提交代码时自动检查语法错误和代码规范
* 高覆盖率、自动化的单元测试
* 用模拟工具给单元测试供给测试数据
* 用脚本测试网页交互
* 用脚本给网页截图，用图片diff工具比较某次修改给网页带来的变化