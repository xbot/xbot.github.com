---
title: OmniFocus 插件 checklist v2.0 小记
slug: omnifocus plugin checklist v2.0 release notes
date: 2022-12-11 18:20:34+08:00
tags:
- omnifocus
- 效率工具
- 青梅煮酒
toc: false
---
OmniFocus 是个非常强大的待办列表 App ，最大的特点之一是支持开发插件。如果你跟我一样有各种稀奇古怪的需求且其它待办 App 不能满足时，可以试试它，尽管很贵，但是很值。

[checklist](https://github.com/xbot/omnifocus-plugin-checklist) 是一个把 OmniFocus 变成检查清单 App 的插件，源自我周期性重复检查一些事项的需求，例如：收拾行李、储备物资和审查代码。

之前用过一款专门的手机 App ，对于检查清单这个领域来说，确实做得已经很极致了，但是最大的问题是没有电脑端，对于像代码审查这种需要更多、更复杂的编辑操作的清单来说，在手机上操作真是太麻烦了。还有一点就是手机毕竟还不是一个常规意义上的“正经”工具，工作时间捧着手机显得很奇怪。

checklist 就是这种背景下的产物。它的基本逻辑是：用 OmniFocus 的 project 当清单模板，一个 project 对应一个模板。模板集中存在专门的 folder 下。在需要检查清单的 project 下点击插件按钮选择清单模板，将在该 project 下创建和模板中相同的任务列表。

以下录屏直观地展示了插件的用法：

![2022-12-11-18-57-32-omnifocus-plugin-checklist-v2-demo](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2022-12-11-18-57-32-omnifocus-plugin-checklist-v2-demo.gif)

凭借 OmniFocus 原本就很强大的任务列表功能， checklist 近乎完美地实现了我对一个检查清单 App 的需求。不光我的手机上又少了一个 App ，而且考虑到那个 App 高达一百多的售价，说支持多端的 checklist 价值 200￥ 似乎也不过分。