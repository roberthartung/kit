kit
===

kit is a powerful php (version >= 5.4) framework for creating websites and large projects.

It comes with a simple template engine and a powerful set of classes to make you develop your projects as fast as possible.

usage
===

kit is fairly easy to use. The kit framework can be shared across all your projects. This makes not only saves space but only makes it easier for you to work with kit.

Let's assume you have the kit framework installed to /git/kit/. Then the structure might look like this: 

### kit structure

```
/git/kit/
	cfg/
	examples/
	inc/
		core.inc.php
		defaults.inc.php
		run.inc.php
	lib/
```

### index.php
The first thing you need to do is to create a PHP file (index.php). This file will contain both the basic setup for the kit framework and project specific options.

```php
<?php
	require_once('/git/kit/inc/core.inc.php');
	
	// register URLs here
	$kit->registerUrl('/', 'index');
	
	require_once(PATH_KIT_ROOT.'inc/run.inc.php');
?>
```

The core.inc.php will setup the basics of kit. Afterwards you can access all parts of kit. Registering URLs is recommended to be done here. Afterwards the run.inc.php will be included. As you can see there is a constant "PATH_KIT_ROOT" available to easily access the kit framework directory. kit also defines a constant for your project: PATH_SITE_ROOT pointing to the directory of index.php.