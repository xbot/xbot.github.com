# 使用cx_Freeze的distutils脚本打包Python程序

<p>cx_Freeze打包Python程序的命令基本格式如下：</p>

<p>
```python
cxfreeze main.py --target-dir appdir
```
</p>

<p>它表示把脚本main.py或以main.py为程序入口的程序打包并导出到当前路径中名为<strong>appdir</strong>的目录中。</p>

<p>对于Windows下的GUI应用程序，以上面的命令导出后，运行时会弹出<strong>cmd</strong>命令行的黑窗口，须加上如下命令中的参数：</p>

<p>
```python
cxfreeze main.py --target-dir appdir --base-name=win32gui
```
</p>

<p>对于比较复杂的程序，cx_Freeze支持<a href="http://www.ibm.com/developerworks/cn/linux/sdk/python/charm-19/">distutils</a>格式的打包脚本，当然，彼此之间在引入的模块和支持的参数上还是有差别的。</p>

<p>cx_Freeze的文档中有其支持的全部命令参数及说明，写到setup.py脚本中时，所有参数中的<strong>-</strong>符号应换成下划线。</p>

<p>我的setup.py内容大致如下：</p>

<p>
```python
#!/usr/bin/python
# -*- coding: utf-8 -*-
from cx_Freeze import setup,Executable

includefiles = [('settings.ini.jctest','settings.ini')
        ,'README.mkd']
includes = []
excludes = ['Tkinter']
packages = ['sqlalchemy.engine', 'sqlalchemy.orm', 'sqlalchemy.dialects.mssql']

setup(
    name = 'pyutil',
    version = '0.1',
    description = 'A general enhancement utility for XXX',
    author = 'Lenin Lee',
    author_email = 'lenin.lee@xxx.com',
    options = {'build_exe':{'excludes':excludes,'packages':packages,'include_files':includefiles}},
    executables = [Executable('jcitk.py')
        , Executable('jcvfd.py')
        , Executable('jcvdupcr.py')
        , Executable('jcddupcr.py')
        , Executable('jcclostfd.py')
        , Executable('jcvcard.py')
        , Executable('jcvcardii.py')
        , Executable('jcclostsoid.py')]
)
```
</p>

<p>在摸索如何写setup.py的过程中，遇到一些问题。</p>

<p>首先是如何将配置文件<strong>settings.ini</strong>自动复制到打包文件夹中。这个问题的解决办法是使用build_exe命令的参数<strong>include_files</strong>。此参数的值是一个列表，列表的每一项可以是一个表示要复制的文件的路径的字符串，或者是一个tuple。若是tuple，第一个元素是表示要复制的文件的路径，第二个元素是表示复制后要修改成的文件名。需要说明的是，文件夹可以和文件一样使用这样的方法复制到打包文件夹中。</p>

<p>其次，在打包引入了SQLAlchemy的程序后，若运行该程序时报某模块导入失败的错，应将报错信息中提示的模块所在的包填写到<strong>packages</strong>参数中。虽然也可以在程序中import这些包，但是在setup.py中使用packages参数的做法更合理。而且如果在程序中导入了没有被显式调用的模块或包的话，对于使用<a href="http://swik.net/PyFlakes">pyflakes</a>检查语法错误的<a href="http://www.vim.org/scripts/script.php?script_id=2441">环境</a>，会显示模块或包未被调用的警告，至少看起来不舒服。</p>

<p>再次，Windows下打包时应使用python 2.5，因为2.6版本需要<a href="http://www.microsoft.com/downloads/details.aspx?familyid=9B2DA534-3E03-4391-8A4D-074B9F2BC1BF&amp;displaylang=zh-cn">Microsoft Visual C++ 2008 Redistributable</a>，一般非开发环境的系统中都没安装这个，运行程序时就会报错。</p>

