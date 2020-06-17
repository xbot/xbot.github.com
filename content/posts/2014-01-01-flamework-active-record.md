---
layout: post
title: "PHP框架实战（五）：ORM与ActiveRecord"
date: 2014-01-01 20:45:00
comments: true
categories:
- 计算机
tags:
- PHP
- Flamework
- 框架
- 编程
---

简述
----

Model是MVC框架中最复杂的部分，它要提供与业务逻辑相关的数据及数据处理方法的封装，一般要提供数据对象、数据库连接、事务管理、SQL语句构造、数据CRUD、高级通用业务逻辑等一系列功能。由于Model与Controller和View是解耦的，并且本身具备很高的通用性和复杂性，所以有很多独立的实现。本文希望能通过开发一个简单的ActiveRecord，验证这种Model实现方案的原理和过程。

ORM：对象关系映射
-----------------

ORM的全称是Object Relational Mapping，即对象关系映射。它是为了解决关系数据库的数学模型和编程语言的对象模型之间的阻抗不匹配问题而提出的解决方案。

阻抗不匹配是个逼格很高的词。

阻抗是指电路中的电容、电感、电阻对交流电的障碍作用，就像电阻对直流电的障碍作用。两个系统传递信号可以形象地看成电压的传递，公式为：

>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;**U**(out) \* **Z**(in)  
**U**(in) =  --------------------------  
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;**Z**(in) + **Z**(out)

即输入电压等于输出电压与输入阻抗的积除以输入阻抗与输出阻抗的和。

理想情况肯定是输入电压等于输出电压，这时信号是没有失真的，也就是要求Z(in)与Z(in)+Z(out)之商无限逼近1，这个过程就叫阻抗匹配。关系型数据库是建立在数学模型的基础上，而编程语言中的对象是建立在人对客观世界认知的具象模型上。说白了，阻抗不匹配问题就是说因这两种模型不一致而导致的问题。

ORM通过建立表与对象、列与属性（_这只是一般情况_）之间的映射关系而解决问题，这可以实现像操作对象一样对数据库中的数据进行增删改查，简化了开发过程。不过ORM的缺点是不能很好地处理复杂数据关系，会出现效率低下的问题，因此必要时仍然需要直接使用SQL。

ActiveRecord
------------

ActiveRecord是Ruby on Rails提出的一个概念，其实就是ORM的一种实现，它是对象类型、数据、CRUD方法的合体，使对数据的操作以更具象化的方式实现。下面介绍在Flamework中实现一个简单的ActiveRecord的过程。

首先实现数据库的接口，提供数据库连接、查询、执行SQL语句、事务管理等基本功能。这里使用PDO实现：

```php
<?php
namespace org\x3f\flamework\base;

/**
 * Database connection above PDO
 *
 * @author Donie Leigh <donie.leigh@gmail.com>
 * @link http://0x3f.org
 * @copyright Copyright &copy; 2013-2014 Donie Leigh
 * @license BSD (3-terms)
 * @since 1.0
 */
class DBConnection
{
    /**
     * @var PDO Database connection 
     * @since 1.0
     */
    private $_c;
    /**
     * @var array PDO options 
     * @since 1.0
     */
    private $_options = array(
        'connection_string' => 'sqlite::memory:',
        'username' => null,
        'password' => null,
        'pdo_options' => null,
    );
    /**
     * @var PDOStatement Last PDO statement 
     * @since 1.0
     */
    private $_lastStmt;

    public function __construct($options)
    {
        $this->_options = array_merge($this->_options, $options);
    }

    /**
     * Init DB connection
     * @param string $dsn DB connection string
     * @param string $user DB user name
     * @param string $password DB password
     * @param array $options PDO options
     * @return void
     * @since 1.0
     */
    private function _connectDB($dsn, $user='', $password='', $options=array())
    {
        if ($this->_c == null) {
            $this->_c = new \PDO($dsn, $user, $password, $options);
        }
    }
   
    /**
     * Execute sql statement
     * @param mixed $sql SQL statement or template
     * @param array $params Parameters for SQL template
     * @return bool
     * @since 1.0
     */
    public function execute($sql, $params=array())
    {
        $this->_connectDB(
            $this->_options['connection_string'],
            $this->_options['username'],
            $this->_options['password'],
            $this->_options['driver_options']
        );
        $stmt = $this->_c->prepare($sql);
        $this->_lastStmt = $stmt;
        return $stmt->execute($params);
    }
    
    /**
     * Fetch rows
     * @return array Associative array holding data rows
     * @since 1.0
     */
    public function rows($sql, $params=array())
    {
        $this->execute($sql, $params);
        $stmt = $this->getLastStmt();
        $rows = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }
    
    /**
     * Return the last PDO statement
     * @return PDOStatement
     * @since 1.0
     */
    public function getLastStmt()
    {
        return $this->_lastStmt;
    }
    
    /**
     * Begin transaction
     * @return void
     * @since 1.0
     */
    public function beginTransaction()
    {
        $this->_c->beginTransaction();
    }
    
    /**
     * Commit the current transaction
     * @return void
     * @since 1.0
     */
    public function commit()
    {
        $this->_c->commit();
    }
    
    /**
     * Rollback the current transaction
     * @return void
     * @since 1.0
     */
    public function rollback()
    {
        $this->_c->rollBack();
    }
    
}
?>
```

然后实现ActiveRecord类：

```php
<?php
namespace org\x3f\flamework\base;
use org\x3f\flamework\Flame;

/**
 * Ancestor class for active records
 *
 * @abstract
 * @author Donie Leigh <donie.leigh@gmail.com>
 * @link http://0x3f.org
 * @copyright Copyright &copy; 2013-2014 Donie Leigh
 * @license BSD (3-terms)
 * @since 1.0
 */
abstract class ActiveRecord
{
    /**
     * @var array Models 
     * @since 1.0
     */
    private static $_models = array();
    /**
     * @var array Attributes and values
     * @since 1.0
     */
    private $_data = array();
    /**
     * @var array Attributes and values which are changed
     * @since 1.0
     */
    private $_dirtyData = array();
    /**
     * @var bool Whether this record is a new one 
     * @since 1.0
     */
    private $_isNew = true;

    /**
     * Get model instance
     * @param string $className
     * @return ActiveRecord
     * @since 1.0
     */
    public static function getModel($className = __CLASS__)
    {
        if (isset(self::$_models[$className])) {
            return self::$_models[$className];
        } else {
            $model = self::$_models[$className] = new $className;
            return $model;
        }
    }
    
    /**
     * Get table name of this ActiveRecord
     * @return string Table name
     * @since 1.0
     */
    abstract public function getTableName();

    /**
     * Get the name of the primary key column
     * @return string Column name
     * @since 1.0
     */
    abstract public function getPrimaryKey();
    
    /**
     * Magic method for accessing model attributes
     * @param string $attr Attribute name
     * @return mixed Attribute value
     * @since 1.0
     */
    public function __get($attr)
    {
        if (isset($this->_data[$attr])) {
            return $this->_data[$attr];
        }
        return null;
    }
    
    /**
     * Magic method for setting attribute value
     * @param string $attr Attribute name
     * @param mixed $val Attribute value
     * @return void
     * @since 1.0
     */
    public function __set($attr, $val)
    {
        $this->_data[$attr] = $val;
        if (!$this->getIsNew()) {
            $this->_dirtyData[$attr] = $val;
        }
    }
    
    /**
     * Magic method for checking if an attribute is set
     * @param string $attr Attribute name
     * @return bool
     * @since 1.0
     */
    public function __isset($attr)
    {
        return isset($this->_data[$attr]);
    }
    
    /**
     * Magic method for unsetting an attribute
     * @param string $attr Attribute name
     * @return void
     * @since 1.0
     */
    public function __unset($attr)
    {
        unset($this->_data[$attr]);
    }
    
    /**
     * Set this record to be $isNew
     * @param bool $isNew
     * @return void
     * @since 1.0
     */
    public function setIsNew($isNew)
    {
        $this->_isNew = $isNew;
    }
    
    /**
     * Whether this record is new
     * @return bool
     * @since 1.0
     */
    public function getIsNew()
    {
        return $this->_isNew;
    }
    
    /**
     * Find a record by primary key
     * @param mixed $val Primary key value
     * @return ActiveRecord
     * @since 1.0
     */
    public function findByPk($val)
    {
        $sql = "select * from ".$this->getTableName()." where ".$this->getPrimaryKey()."=?";
        $rows = Flame::app()->getDBConnection()->rows($sql, array($val));
        if (count($rows) > 0) {
            return $this->createInstance($rows[0]);
        }
        return null;
    }
    
    /**
     * Create an instance with given data
     * @param array $row Associative array
     * @return ActiveRecord
     * @since 1.0
     */
    public function createInstance($row)
    {
        $className = get_class($this);
        $instance = new $className;
        foreach ($row as $col=>$val){
            $instance->$col = $val;
        }
        $instance->setIsNew(false);
        return $instance;
    }
    
    /**
     * Save this record
     * @return void
     * @since 1.0
     */
    public function save()
    {
        if ($this->getIsNew()) {
            $this->_insert();
        } else {
            $this->_update();
        }
    }
    
    /**
     * Save this record into the database as a new row
     * @return void
     * @since 1.0
     */
    private function _insert()
    {
        if (count($this->_data) > 0) {
            $cols = implode(', ', array_keys($this->_data));
            $placeHolders = implode(', ', array_fill(0, count($this->_data), '?'));
            $sql = "insert into ".$this->getTableName(). " ($cols) values ($placeHolders)";
            Flame::app()->getDBConnection()->execute($sql, array_values($this->_data));
        }
    }
    
    /**
     * Save this record
     * @return void
     * @since 1.0
     */
    private function _update()
    {
        if (count($this->_dirtyData) > 0) {
            $pairs = implode('=?, ', array_keys($this->_dirtyData)).'=?';
            $sql = 'update '.$this->getTableName()." set $pairs where ".$this->getPrimaryKey().'=?';
            $pk = $this->getPrimaryKey();
            Flame::app()->getDBConnection()->execute($sql, array_merge(array_values($this->_dirtyData), array($this->$pk)));
        }
    }
    
    /**
     * Delete this record
     * @return void
     * @since 1.0
     */
    public function delete()
    {
        if (!$this->getIsNew()) {
            $pk = $this->getPrimaryKey();
            $sql = 'delete from '.$this->getTableName()." where $pk=?";
            Flame::app()->getDBConnection()->execute($sql, array($this->$pk));
        }
    }
    
}
?>
```

约定，所有子类都必须覆盖和实现getModel()、getTableName()、getPrimaryKey()这三个方法。getModel()返回不包含具体数据的ActiveRecord实例，用于执行对象类型范畴的操作，例如查询符合特定条件的对象。在包含具体数据的ActiveRecord实例中执行针对该具体对象的操作，例如保存和删除。

为了更好地区分ActiveRecord的属性和对象数据，这里将对象数据存放在关联数组ActiveRecord::$\_data里，然后使用\_\_get()、\_\_set()等魔术方法实现像使用ActiveRecord自身属性一样使用对象数据。

在更新对象时，出于性能考虑，应该只更新被修改过的列。这里借助魔术方法\_\_set()，实现向对象属性赋值时将被修改的属性和值添加到关联数组ActiveRecord::$\_dirtyData中。最后构造update语句时，从该数组中取值即可。

ActiveRecord的使用
------------------

在Demo项目中实现一个对象，继承ActiveRecord：

```php
<?php
namespace org\x3f\flamedemo\model;
use org\x3f\flamework\base\ActiveRecord;

class Post extends ActiveRecord
{
    public static function getModel($className=__CLASS__)
    {
        return parent::getModel($className);
    }
    
    public function getTableName()
    {
        return 'post';
    }
    
    public function getPrimaryKey()
    {
        return 'id';
    }
    
}
?>
```

用法如下：

```php
<?php
// 根据ID查询对象
$p = Post::getModel()->findByPk(1);
var_dump($p);

// 更新对象
$p = new Post();
$p->setIsNew(false);
$p->id = 3;
$p->title = 'bad name 2';
$p->save();

// 删除对象
$p = new Post();
$p->setIsNew(false);
$p->id = 3;
$p->delete();
?>
```

总结
----

ORM的根本任务是解决关系模型与对象模型的阻抗不匹配问题。而ActiveRecord是时下很流行的一种ORM的实现方式。在对ActiveRecord的实现中使用了PDO，这是PHP 5.1开始引入的一个轻量的数据访问抽象层，相比以前针对每种数据库使用不同的函数集的方式，它使PHP的数据库操作变得更简单。此外，魔术方法的使用简化了代码，使数据操作变得更灵活。

本文只实现了一个最基本的ActiveRecord，实际使用时，还要包含SQL语句构造等复杂的逻辑，不过只要弄清楚了核心原理和实现方式，其它也就水到渠成了。
