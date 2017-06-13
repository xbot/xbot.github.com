---
layout: post
title: "PHP框架實戰（五）：ORM與ActiveRecord"
date: 2014-01-01 20:45
comments: true
categories: 計算機
tags:
- PHP
- Flamework
- 框架
- 編程
---

簡述
----

Model是MVC框架中最複雜的部分，它要提供與業務邏輯相關的數據及數據處理方法的封裝，一般要提供數據對象、數據庫連接、事務管理、SQL語句構造、數據CRUD、高級通用業務邏輯等一系列功能。由於Model與Controller和View是解耦的，並且本身具備很高的通用性和複雜性，所以有很多獨立的實現。本文希望能通過開發一個簡單的ActiveRecord，驗證這種Model實現方案的原理和過程。

ORM：對象關係映射
-----------------

ORM的全稱是Object Relational Mapping，即對象關係映射。它是為了解決關係數據庫的數學模型和編程語言的對象模型之間的阻抗不匹配問題而提出的解決方案。

阻抗不匹配是個逼格很高的詞。

阻抗是指電路中的電容、電感、電阻對交流電的障礙作用，就像電阻對直流電的障礙作用。兩個系統傳遞信號可以形象地看成電壓的傳遞，公式為：

>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;**U**(out) \* **Z**(in)  
**U**(in) =  --------------------------  
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;**Z**(in) + **Z**(out)

即輸入電壓等於輸出電壓與輸入阻抗的積除以輸入阻抗與輸出阻抗的和。

理想情況肯定是輸入電壓等於輸出電壓，這時信號是沒有失真的，也就是要求Z(in)與Z(in)+Z(out)之商無限逼近1，這個過程就叫阻抗匹配。關係型數據庫是建立在數學模型的基礎上，而編程語言中的對象是建立在人對客觀世界認知的具象模型上。說白了，阻抗不匹配問題就是說因這兩種模型不一致而導致的問題。

ORM通過建立表與對象、列與屬性（_这只是一般情况_）之間的映射關係而解決問題，這可以實現像操作對象一樣對數據庫中的數據進行增刪改查，簡化了開發過程。不過ORM的缺點是不能很好地處理複雜數據關係，會出現效率低下的問題，因此必要時仍然需要直接使用SQL。

ActiveRecord
------------

ActiveRecord是Ruby on Rails提出的一個概念，其實就是ORM的一種實現，它是對象類型、數據、CRUD方法的合體，使對數據的操作以更具象化的方式實現。下面介紹在Flamework中實現一個簡單的ActiveRecord的過程。

首先實現數據庫的接口，提供數據庫連接、查詢、執行SQL語句、事務管理等基本功能。這裡使用PDO實現：

{% codeblock lang:php %}
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
{% endcodeblock %}

然後實現ActiveRecord類：

{% codeblock lang:php %}
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
{% endcodeblock %}

約定，所有子類都必須覆蓋和實現getModel()、getTableName()、getPrimaryKey()這三個方法。getModel()返回不包含具體數據的ActiveRecord實例，用於執行對象類型範疇的操作，例如查詢符合特定條件的對象。在包含具體數據的ActiveRecord實例中執行針對該具體對象的操作，例如保存和刪除。

為了更好地區分ActiveRecord的屬性和對象數據，這裡將對象數據存放在關聯數組ActiveRecord::$\_data裡，然後使用\_\_get()、\_\_set()等魔術方法實現像使用ActiveRecord自身屬性一樣使用對象數據。

在更新對象時，出於性能考慮，應該只更新被修改過的列。這裡借助魔術方法\_\_set()，實現向對象屬性賦值時將被修改的屬性和值添加到關聯數組ActiveRecord::$\_dirtyData中。最後構造update語句時，從該數組中取值即可。

ActiveRecord的使用
------------------

在Demo項目中實現一個對象，繼承ActiveRecord：

{% codeblock lang:php %}
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
{% endcodeblock %}

用法如下：

{% codeblock lang:php %}
<?php
// 根據ID查詢對象
$p = Post::getModel()->findByPk(1);
var_dump($p);

// 更新對象
$p = new Post();
$p->setIsNew(false);
$p->id = 3;
$p->title = 'bad name 2';
$p->save();

// 刪除對象
$p = new Post();
$p->setIsNew(false);
$p->id = 3;
$p->delete();
?>
{% endcodeblock %}

總結
----

ORM的根本任務是解決關係模型與對象模型的阻抗不匹配問題。而ActiveRecord是時下很流行的一種ORM的實現方式。在對ActiveRecord的實現中使用了PDO，這是PHP 5.1開始引入的一個輕量的數據訪問抽象層，相比以前針對每種數據庫使用不同的函數集的方式，它使PHP的數據庫操作變得更簡單。此外，魔術方法的使用簡化了代碼，使數據操作變得更靈活。

本文只實現了一個最基本的ActiveRecord，實際使用時，還要包含SQL語句構造等複雜的邏輯，不過只要弄清楚了核心原理和實現方式，其它也就水到渠成了。
