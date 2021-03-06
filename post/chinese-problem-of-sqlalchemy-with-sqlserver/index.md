# SQLAlchemy操作SQL Server的中文问题

<p>最初将脚本的文件编码和coding行都设定为UTF-8，在windows下执行时，中文无法保存，报编码错误。将上述两个编码改为GBK后，保存正常，但查询时报错。</p>

<p>Traceback内容如下：</p>

```
Traceback (most recent call last):
File "test.py", line 36, in <code>&lt;module&gt;</code>
&nbsp;&nbsp;&nbsp;&nbsp;for obj in session.query(User):
File "C:\Python26\lib\site-packages\sqlalchemy-0.6beta1-py2.6.egg\sqlalchemy\orm\query.py", line 1411, in instances
&nbsp;&nbsp;&nbsp;&nbsp;rows = [process[0](row, None) for row in fetch]
File "C:\Python26\lib\site-packages\sqlalchemy-0.6beta1-py2.6.egg\sqlalchemy\orm\mapper.py", line 1788, in _instance
&nbsp;&nbsp;&nbsp;&nbsp;populate_state(state, dict_, row, isnew, only_load_props)
File "C:\Python26\lib\site-packages\sqlalchemy-0.6beta1-py2.6.egg\sqlalchemy\orm\mapper.py", line 1677, in populate_state
&nbsp;&nbsp;&nbsp;&nbsp;populator(state, dict_, row, isnew=isnew, **flags)
File "C:\Python26\lib\site-packages\sqlalchemy-0.6beta1-py2.6.egg\sqlalchemy\orm\strategies.py", line 118, in new_execute
&nbsp;&nbsp;&nbsp;&nbsp;dict_[key] = row[col]
File "C:\Python26\lib\site-packages\sqlalchemy-0.6beta1-py2.6.egg\sqlalchemy\engine\base.py", line 1634, in __getitem__
&nbsp;&nbsp;&nbsp;&nbsp;return self.__colfuncs[key][0](self.__row)
File "C:\Python26\lib\site-packages\sqlalchemy-0.6beta1-py2.6.egg\sqlalchemy\engine\base.py", line 1716, in getcol
&nbsp;&nbsp;&nbsp;&nbsp;return processor(row[index])
File "C:\Python26\lib\site-packages\sqlalchemy-0.6beta1-py2.6.egg\sqlalchemy\types.py", line 568, in process
&nbsp;&nbsp;&nbsp;&nbsp;return decoder(value)[0]
File "C:\Python26\lib\encodings\utf_8.py", line 16, in decode
&nbsp;&nbsp;&nbsp;&nbsp;return codecs.utf_8_decode(input, errors, True)
UnicodeEncodeError: 'ascii' codec can't encode characters in position 0-1: ordinal not in range(128)</p>
```

<p>环境为：</p>

<p>OS：Windows XP简体中文版
DB：SQL Server 2008 Express简体中文版
DB模块：pyodbc
脚本文件编码：GBK
脚本coding行：GBK</p>

<p>脚本内容：</p>

<p>
```python
#!/usr/bin/python
# -*- encoding: gbk -*-

from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
from sqlalchemy import Column, Integer, String, Text, ForeignKey, Numeric, Unicode

Base = declarative_base()

class User(Base):
    """User class"""

    __tablename__ = 'users'

    id = Column(Numeric(22,0), primary_key=True)
    name = Column(Unicode(128), nullable=False, unique=True)

    def __init__(self, id, name):
        self.id = id
        self.name = name

if __name__ == '__main__':
    db_engine = create_engine('mssql://sa:password@localhost/mydatabase', echo=True)
    Session = sessionmaker(bind=db_engine)
    session = Session()

    Base.metadata.drop_all(db_engine)
    Base.metadata.create_all(db_engine)

    jim = User(1, '中文')
    session.add(jim)
    session.commit()

    '''
    for obj in session.query(User):
        print obj.name
    '''
```
</p>

<p>上面的脚本执行后，数据得以正常保存，在数据库中的查询结果也正常，没有乱码。但是，当把从drop_all()到commit()行注释掉，取消for循环前后的多行字符串起止符后，即运行查询时，抛出上面的Traceback。</p>

<p>Google了很长时间，没有找到有用的东西。CPyUG更没指望。</p>

<p>回溯Traceback，打开sqlalchemy的types.py，UnicodeEncodeError的抛出点在String类的result_processor()方法：</p>

<p>
```python
def result_processor(self, dialect, coltype):
    wants_unicode = self.convert_unicode or dialect.convert_unicode
    needs_convert = wants_unicode and \
                    (not dialect.returns_unicode_strings or 
                    self.convert_unicode == 'force')

    if needs_convert:
        # note we *assume* that we do not have a unicode object
        # here, instead of an expensive isinstance() check.
        decoder = codecs.getdecoder(dialect.encoding)
        def process(value):
            if value is not None:
                # decoder returns a tuple: (value, len)
                return decoder(value)[0]
            else:
                return value
        return process
    else:
        return None
```
</p>

<p>这个方法就是根据数据库方言dialect和字段类型coltype返回一个字符串的解码函数。若在if语句上面将needs_convert置为False，即不对该字段使用解码器，则再执行上面的脚本时，查询正常。</p>

<p>由于前面create_engine()函数的encoding参数缺省为UTF-8，故dialect.encoding的值为“UTF-8”，故if语句中decoder实际引用的是codecs.utf_8_decode()。也就是说，result_processor()方法在实际执行过程中返回的是一个封装了utf_8_decode()函数的函数。即，UnicodeEncodeError是在对从数据库中查询出来的中文字符串进行UTF-8解码时抛出的。</p>

<p>对传入process()函数的值作isinstance(value,unicode)判断，显示为True，表明从数据库中查询出来的中文本身就是unicode字节码，当对它再进行UTF-8解码时，就抛出了UnicodeEncodeError的错误。为验证以上判断，做如下实验：</p>

<p>
```python
>>>t = '中文'
>>>u = u'中文'
>>>isinstance(t, str)
True
>>>isinstance(t, unicode)
False
>>>isinstance(u, str)
False
>>>isinstance(u, unicode)
True
>>>x = t.decode('utf-8')
>>>x
u'\u4e2d\u6587'
>>>isinstance(x, unicode)
True
>>>x == u
True
>>> import codecs
>>> dc = codecs.getdecoder('utf-8')
>>> dc(u)
Traceback (most recent call last):
  File "<input>", line 1, in <module>
  File "/usr/lib/python2.6/encodings/utf_8.py", line 16, in decode
    return codecs.utf_8_decode(input, errors, True)
UnicodeEncodeError: 'ascii' codec can't encode characters in position 0-1: ordinal not in range(128)
```
</p>

<p>得证。</p>

<p>在Python中，字符串类型str和unicode类型是两种不同的数据类型，str类型的数据可以通过指定正确的编码来转换成unicode类型，对unicode类型的数据作重复的解码操作就会抛出类似上面的错误。</p>

<p>实事上，若将name字段声明为String类，则保存和查询操作均无问题。但由于我需要sqlalchemy建表时将相应字段的类型设为nvarchar，故必须使用Unicode类声明该列。</p>

<p>那有没有办法使result_processor()方法不返回一个对字段值作重复解码的函数呢？</p>

<p>返回result_processor()方法，self.convert_unicode对于Unicode类是True，dialect.convert_unicode由create_engine()函数的convert_unicode参数控制，缺省为False，故needs_convert变量为True，无法更改；dialect.returns_unicode_strings是由sqlalchemy.engine模块default.py中的DefaultDialect类的_check_unicode_returns()方法返回的，该方法内容为：</p>

<p>
```python
def _check_unicode_returns(self, connection):
    cursor = connection.connection.cursor()
    cursor.execute(
        str(
            expression.select( 
            [expression.cast(
                expression.literal_column("'test unicode returns'"),sqltypes.VARCHAR(60))
            ]).compile(dialect=self)
        )
    )

    row = cursor.fetchone()
    result = isinstance(row[0], unicode)
    cursor.close()
    return result
```
</p>

<p>此方法的功能为生成一条SQL语句，在数据库中执行后，判断返回的值是否为unicode类型。由于SQL Server是ASCII编码，故此方法返回False。因此，dialect.returns_unicode_strings的值为False。最终，needs_convert只能为True。我觉得这是sqlalchemy的一个Bug。</p>

<p>在此条件下，目前尚未找到较好的解决办法，只能使用硬编码强制置result_processor()方法中的needs_convert变量为False。</p>

<p><strong>2010-02-25 更新：</strong></p>

<p>谢谢KL童鞋指出问题原因和解决办法，使问题得以完美解决。</p>

<p>1、由于Python在载入site模块时会删除setdefaultencoding()函数，故不能以在脚本开头调用此函数的方式指定默认编码；sitecustomize.py是一个python会自动导入的模块，故应当使用这个文件指定默认编码；</p>

<p>2、我这里需要使用utf-8作默认编码器，sitecustomize.py的内容如下：</p>

<p>
```python
#!/usr/bin/python
# -*- coding: gbk -*-
import sys
sys.setdefaultencoding('utf-8')
```
</p>

<p>3、将sitecustomize.py保存到python安装目录下的Lib\site-packages目录中；</p>

<p>另外，在<a href="http://www.woodpecker.org.cn/diveintopython/xml_processing/unicode.html">此处</a>发现了跟本问题相关的资料，辅助治疗，效果更佳。</p>

