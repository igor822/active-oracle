# Active Oracle

This active record allow you to connect with Oracle database without concern about all the oci_* functions

## Example

```php
<?php
$dataSource = array(
	'username' => 'hr',
	'password' => 'root',
	'service' => '//localhost/XE',
	'persistent' => true
);
$dboConn = ActiveOracle\DboSource::connect(array('connector' => 'oracle'));
$connector = $dboConn->getConnector()->setDataSource($_dataSource)->openConnection();
```

To fetch results of some query

```php
// fetch() returns by default an array, if want, you can add a second parameter 'object' and return 
// an object ItemIterator()
$result = $dboConn->fetch('select * from dual');
```