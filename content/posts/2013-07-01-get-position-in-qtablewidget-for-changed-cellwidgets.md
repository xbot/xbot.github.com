---
layout: post
title: "PySide: How to Get the Position of a Widget-Value-Changed Cell in QTableWidget"
slug: get position in qtablewidget for changed cellwidgets
date: 2013-07-01 00:57:00
comments: true
categories:
- 计算机
tags:
- Python
- PySide
- 编程
---
Assume we have created an instance of QTableWidget, in which cells are filled with widgets like QComboBox, QCheckBox, etc. When values are changed, we need to get the row and column indexes of the cells. But the QTableWidget.cellChanged() and QTableWidget.itemChanged() signals are only effective for cells containing QTableWidgetItem instances, for those in which widgets are filled in with QTableWidget.setCellWidget(), this doesn't work.

This problem nearly made me crazy last weekend. So how can I make it ?

Here is the solution, by leveraging QSignalMapper, we can send other data to the slots instead of the default value. As the following code snippet shows, I put the row and column indexes into a string, then mapped it with the QComboBox.currentIndexChanged() slot. Notice that this slot has two generics, one accept an int type as the parameter and the other accept a string, so a **QString** key should be used to select the second generic.

```python
signalMapper = QtCore.QSignalMapper(self.ui)
signalMapper.setMapping(comboWidget, '%d,%d' % (rowIdx, colIdx))
comboWidget.currentIndexChanged['QString'].connect(signalMapper.map)
signalMapper.mapped['QString'].connect(self.__onCellWidgetChanged)
```

The only downside of this solution is, we should call QTableWidget.cellWidget() seperately to get the new value. Which I think it's not to much trouble.
