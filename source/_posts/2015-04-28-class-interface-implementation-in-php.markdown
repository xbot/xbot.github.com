---
layout: post
title: "PHP類和接口的實現"
date: 2015-04-28 18:38
comments: true
categories: 計算機
tags:
- PHP
- 源碼
---

## Class的實現

類的註冊是在擴展的MINIT方法裡。

{% codeblock lang:c %}
/*
 * this pointer should be put into the header file,
 * so other modules can access this class.
 */
zend_class_entry *c_leigh;

/* just a simple method. */
PHP_METHOD(Leigh, helloWorld)
{
    if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

    php_printf("Hello World !\n");
}

/* getting handle of this object. */
PHP_METHOD(Leigh, getObjectHandle)
{
	zval *obj;

	if (zend_parse_parameters_none() == FAILURE)
	{
		return;
	}

	obj = getThis();

	RETURN_LONG(Z_OBJ_HANDLE_P(obj));
}

/* get value of the property 'bloodType' */
PHP_METHOD(Leigh, getBloodType)
{
	zval *obj, *blood_type;

	if (zend_parse_parameters_none() == FAILURE) {
        return;
    }

	obj = getThis();

    blood_type = zend_read_property(c_leigh, obj, "bloodType", sizeof("bloodType") - 1, 1 TSRMLS_CC);

    RETURN_ZVAL(blood_type, 1, 0);
}

/* set value of the property 'bloodType' */
PHP_METHOD(Leigh, setBloodType)
{
	zval *obj, *new_value;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "z", &new_value) == FAILURE)
	{
		return;
	}

	obj = getThis();

	zend_update_property(c_leigh, obj, "bloodType", sizeof("bloodType")-1, new_value TSRMLS_CC);
}

const zend_function_entry leigh_functions[] = {
	PHP_ME(Leigh, helloWorld, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Leigh, getObjectHandle, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Leigh, getBloodType, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(Leigh, setBloodType, NULL, ZEND_ACC_PUBLIC)
	/* PHP_ABSTRACT_ME(Leigh, abstractMethod, NULL)      // abstract method */
	PHP_FE_END
};

/*
 * create a new class inheriting Leigh
 */
zend_class_entry *c_hero;

/*
 * create an interface
 */
zend_class_entry *i_superman;

const zend_function_entry superman_functions[] = {
	PHP_ABSTRACT_ME(ISuperman, saveEarth, NULL)
	PHP_FE_END
};

/*  PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(donie)
{
	/* If you have INI entries, uncomment these lines
	REGISTER_INI_ENTRIES();
	*/

	time_of_minit = time(NULL);

	/* register a class */
	zend_class_entry tmp_leigh;
	INIT_CLASS_ENTRY(tmp_leigh, "Leigh", leigh_functions);
	c_leigh = zend_register_internal_class(&tmp_leigh TSRMLS_CC);

	/* declare a property initialized as null */
	zend_declare_property_null(c_leigh, "bloodType", sizeof("bloodType")-1, ZEND_ACC_PUBLIC TSRMLS_CC);
	/* declare a class constant */
	zend_declare_class_constant_double(c_leigh, "PI", sizeof("PI")-1, 3.1415926 TSRMLS_CC);

	/* declare an interface */
	zend_class_entry tmp_superman;
	INIT_CLASS_ENTRY(tmp_superman, "ISuperman", superman_functions);
	i_superman = zend_register_internal_interface(&tmp_superman TSRMLS_CC);

	/* inherit a class and implement an interface*/
	zend_class_entry tmp_hero;
	INIT_CLASS_ENTRY(tmp_hero, "Hero", NULL);
	c_hero = zend_register_internal_class_ex(&tmp_hero, c_leigh, NULL TSRMLS_CC);
	zend_class_implements(c_hero TSRMLS_CC, 1, i_superman);

	return SUCCESS;
}
{% endcodeblock %}

#### 方法修飾符

>ZEND_ACC_PUBLIC  
>ZEND_ACC_PROTECTED  
>ZEND_ACC_PRIVATE  
>ZEND_ACC_STATIC  
>ZEND_ACC_FINAL  
>ZEND_ACC_ABSTRACT  

不直接在PHP_ME裡使用ZEND_ACC_ABSTRACT定義抽象方法，用PHP_ABSTRACT_ME()。

#### 取對象句柄

在方法的定義裡使用getThis()拿當前對象的句柄。

#### 屬性的聲明和存取

>zend_declare_property_null(... TSRMLS_DC)  
>zend_declare_property_bool(..., long value TSRMLS_DC)  
>zend_declare_property_long(..., long value TSRMLS_DC)  
>zend_declare_property_double(..., double value TSRMLS_DC)  
>zend_declare_property_string(..., const char *value TSRMLS_DC)  
>zend_declare_property_stringl(..., const char *value, int value_len TSRMLS_DC)  

屬性的修飾符和方法相同。

屬性的獲取使用zend_read_property_*()這組函數。

屬性的更新使用zend_update_property_*()這組函數。

靜態屬性的獲取和更新分別使用zend\_read\_static\_property\_\*()函數組和zend\_update\_static\_property\_\*()函數組。與以上不同的是，參數中不需要對象句柄。

類常量的聲明使用zend_declare_class_constant_*()函數組，參數與聲明屬性相同，只是不需要修飾符。

#### 繼承類

用zend_register_internal_class_ex()。

#### 聲明接口

和聲明類一樣，先聲明一組抽象方法，然後用zend_register_internal_interface()註冊。

#### 實現接口

用zend_class_implements()。
