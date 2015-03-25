Yii2  wrapper for Oci8 to PDO by yajra
======================================
Translate pdo command to oci8

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist iseredov/yii2-oci8pdo "*"
```

or add

```
"iseredov/yii2-oci8pdo": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
        'db' => [
            'class' => 'iseredov\oci8pdo\Connection',
            'dsn' => 'oci:dbname=//dbgost:1521/dbasename',
            'username' => 'username',
            'password' => 'password',
            'charset' => 'utf8',
            'attributes' => [
                PDO::ATTR_STRINGIFY_FETCHES => true,
            ],
        ],```
