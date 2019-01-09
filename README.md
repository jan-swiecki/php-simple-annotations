# PHP Simple Annotations

## Installation

Get [composer](http://getcomposer.org/) and learn to use it.

Library is on [packagist](https://packagist.org/packages/jan-swiecki/simple-annotations).

If you refuse to use composer then instead of `include_once "vendor/autoload.php"` use `include_once "src/DocBlockReader/Reader.php"`.

## Test

You need [PHPUnit](https://github.com/sebastianbergmann/phpunit/). After you get it run:

    > git clone https://github.com/jan-swiecki/php-simple-annotations
    > cd php-simple-annotations
    > composer install
    > phpunit

## Introduction

This library gives you the ability to extract and auto-parse DocBlock comment blocks.

Example:
```php
    class TestClass {
      /**
       * @x 1
       * @y yes!
       */
      private $myVar;
    }

    $reader = new \DocBlockReader\Reader('TestClass', 'myVar', 'property');
    $x = $reader->getParameter("x"); // 1 (with number type)
    $y = $reader->getParameter("y"); // "yes!" (with string type)
 ```

So as you can see to do this you need to construct `Reader` object and target it at something. Then you extract data.

You can point at classes, class methods and class properties.

* Targeting class: `$reader = new \DocBlockReader\Reader(String $className)`
* Targeting method or property: `$reader = new \DocBlockReader\Reader(String $className, String $name [, String $type = 'method'])`

 This will initialize DocBlock Reader on method `$className::$name` or property `$className::$name`.

 To choose method use only two arguments or provide third argument as `method` string value. To get property value put `property` string value in third argument.

To extract parsed properties you have two methods:

* `$reader->getParameter(String $key)`

 Returns DocBlock value of parameter `$key`. E.g.

 ```php
 <?php
 class MyClass
 {
     /**
      * @awesomeVariable "I am a string"
      */
     public function fn()
     {

     }
 }
 ```

 then

 ```php
 $reader = new \DocBlockReader\Reader('MyClass', 'fn');
 $reader->getParameter("awesomeVariable")
 ```

 will return string `I am a string` (without quotes).

* `$reader->getParameters()`

 returns array of all parameters (see examples below).

## API

* Constructor `$reader = new \DocBlockReader\Reader(String $className [, String $name [, String $type = 'method'] ])`
  
  Creates `Reader` pointing at class, class method or class property - based on provided arguments (see Introduction).

* `$reader->getParameter(String $key)`

 Returns value of parameter `$key` extracted from DocBlock.

* `$reader->getParameters()`

 returns array of all parameters (see examples below).

* `$reader->getVariableDeclarations()` - See last example below.


## Examples

Examples based on ReaderTest.php.

Note: DocBlock Reader converts type of values basing on the context (see below).

### Type conversion example

```php
<?php

include_once "vendor/autoload.php";

class MyClass
{
	/**
	 * @var0 1.5
	 * @var1 1
	 * @var2 "123"
	 * @var3 abc
	 * @var4 ["a", "b"]
	 * @var5 {"x": "y"}
	 * @var6 {"x": {"y": "z"}}
	 * @var7 {"x": {"y": ["z", "p"]}}
	 *
	 * @var8
	 * @var9 null
	 *
	 * @var10 true
	 * @var11 tRuE
	 * @var12 false
	 * @var13 null
	 * 
	 */
	private function MyMethod()
	{
	}
};

$reader = new DocBlockReader\Reader("MyClass", "MyMethod");

var_dump($reader->getParameters());
```

will print


<pre class='xdebug-var-dump' dir='ltr'>
<b>array</b> <i>(size=14)</i>
  'var0' <font color='#888a85'>=&gt;</font> <small>float</small> <font color='#f57900'>1.5</font>
  'var1' <font color='#888a85'>=&gt;</font> <small>int</small> <font color='#4e9a06'>1</font>
  'var2' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'123'</font> <i>(length=3)</i>
  'var3' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'abc'</font> <i>(length=3)</i>
  'var4' <font color='#888a85'>=&gt;</font> 
    <b>array</b> <i>(size=2)</i>
      0 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'a'</font> <i>(length=1)</i>
      1 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'b'</font> <i>(length=1)</i>
  'var5' <font color='#888a85'>=&gt;</font> 
    <b>array</b> <i>(size=1)</i>
      'x' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'y'</font> <i>(length=1)</i>
  'var6' <font color='#888a85'>=&gt;</font> 
    <b>array</b> <i>(size=1)</i>
      'x' <font color='#888a85'>=&gt;</font> 
        <b>array</b> <i>(size=1)</i>
          'y' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'z'</font> <i>(length=1)</i>
  'var7' <font color='#888a85'>=&gt;</font> 
    <b>array</b> <i>(size=1)</i>
      'x' <font color='#888a85'>=&gt;</font> 
        <b>array</b> <i>(size=1)</i>
          'y' <font color='#888a85'>=&gt;</font> 
            <b>array</b> <i>(size=2)</i>
              0 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'z'</font> <i>(length=1)</i>
              1 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'p'</font> <i>(length=1)</i>
  'var8' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>true</font>
  'var9' <font color='#888a85'>=&gt;</font> <font color='#3465a4'>null</font>
  'var10' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>true</font>
  'var11' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>true</font>
  'var12' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>false</font>
  'var13' <font color='#888a85'>=&gt;</font> <font color='#3465a4'>null</font>
</pre>

### Multi value example

```php
<?php

include_once "vendor/autoload.php";

class MyClass
{
	/**
	 * @var x
	 * @var2 1024
	 * @param string x
	 * @param integer y
	 * @param array z
	 */
	private function MyMethod()
	{
	}
};

$reader = new DocBlockReader\Reader("MyClass", "MyMethod");

var_dump($reader->getParameters());
```

will print


<pre class='xdebug-var-dump' dir='ltr'>
<b>array</b> <i>(size=3)</i>
  'var' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'x'</font> <i>(length=1)</i>
  'var2' <font color='#888a85'>=&gt;</font> <small>int</small> <font color='#4e9a06'>1024</font>
  'param' <font color='#888a85'>=&gt;</font> 
    <b>array</b> <i>(size=3)</i>
      0 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'string x'</font> <i>(length=8)</i>
      1 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'integer y'</font> <i>(length=9)</i>
      2 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'array z'</font> <i>(length=7)</i>
</pre>

### Variables on the same line

```php
<?php

include_once "vendor/autoload.php";

class MyClass
{
	/**
	 * @get @post
	 * @ajax
	 * @postParam x @postParam y
	 * @postParam z
	 */
	private function MyMethod()
	{
	}
};

$reader = new DocBlockReader\Reader("MyClass", "MyMethod");

var_dump($reader->getParameters());
```

will print

<pre class='xdebug-var-dump' dir='ltr'>
<b>array</b> <i>(size=4)</i>
  'get' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>true</font>
  'post' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>true</font>
  'ajax' <font color='#888a85'>=&gt;</font> <small>boolean</small> <font color='#75507b'>true</font>
  'postParam' <font color='#888a85'>=&gt;</font> 
    <b>array</b> <i>(size=3)</i>
      0 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'x'</font> <i>(length=1)</i>
      1 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'y'</font> <i>(length=1)</i>
      2 <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'z'</font> <i>(length=1)</i>
</pre>

### Variable declarations functionality example

I found below functionality useful for filtering `$_GET`/`$_POST` data in CodeIgniter. Hopefully I will soon release my CodeIgniter's modification.

```php
<?php

include_once "vendor/autoload.php";

class MyClass
{
	/**
	 * @param string var1
	 * @param integer var2
	 */
	private function MyMethod()
	{
	}
};

$reader = new DocBlockReader\Reader("MyClass", "MyMethod");

var_dump($reader->getVariableDeclarations("param"));
```

will print

<pre class='xdebug-var-dump' dir='ltr'>
<b>array</b> <i>(size=2)</i>
  0 <font color='#888a85'>=&gt;</font> 
    <b>array</b> <i>(size=2)</i>
      'type' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'string'</font> <i>(length=6)</i>
      'name' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'var1'</font> <i>(length=4)</i>
  1 <font color='#888a85'>=&gt;</font> 
    <b>array</b> <i>(size=2)</i>
      'type' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'integer'</font> <i>(length=7)</i>
      'name' <font color='#888a85'>=&gt;</font> <small>string</small> <font color='#cc0000'>'var2'</font> <i>(length=4)</i>
</pre>
