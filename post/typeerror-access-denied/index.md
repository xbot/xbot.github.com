# IE中使用IFrame上传文件报错——TypeError:拒绝访问

场景为：

点击一个图标，打开文件选择对话框，选择好文件后即时上传。

之前的实现方式是在一个隐藏表单中放一个file类型的input元素，通过调用input.click()打开对话框，同时监听input.onchange，最后通过dojo/request/iframe上传文件。

这个实现方式在Chrome和Firefox里都没问题，但是在IE里报错：“**TypeError:拒绝访问**”。原因是IE要求必须通过点击file类型的input的按钮打开选择对话框，否则就报这个错误。

解决办法是修改input元素的样式，或者直接用dojox.form.Uploader替代。

_因为这个破问题又血战到半夜，只支持IE9+的世界你们好吗，还支持IE7+的屌丝伤不起……不起……起……啊。**IE不死，吾难未已！！！**_

