Yii 2 Cms
================================

REQUIREMENTS
------------

The minimum requirement by this application template that your Web server supports PHP 5.4.0.


INSTALLATION
------------

### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this application template using the following command:

~~~
composer --prefer-dist install
~~~

CONFIGURATION
-------------

### Database

Edit the file `config/env.php` with real data, for example:

```php
'db' => [
	'class' => 'yii\db\Connection',
	'dsn' => 'mysql:host=localhost;dbname=yii2',
	'username' => 'root',
	'password' => '1234',
	'charset' => 'utf8',
]
```

**NOTE:** Yii won't create the database for you, this has to be done manually before you can access it.

Also check and edit the other files in the `config/` directory to customize your application.

### Modules

please edit `config/main.php` file `modules` section, please remove unused modules


INSTALL OR UPDATE MODULES
-------------------------

  * ./yii install - install modules and migrations
  * ./yii message @app/messages/config.php - extract messages
  
ADMIN
-----
backend url by default /backend.php login: admin@admin.com password: demo
