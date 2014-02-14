# DbConnector

This library allow you to connect with Oracle database without concern about all the oci_* functions

## Example

```php
<?php
$_dataSource = array(
	'username' => 'hr',
	'password' => 'root',
	'service' => '//localhost/XE',
	'persistent' => true
);
$dbo_conn = DbConnector\DboSource::connect(array('connector' => 'oracle'));
$connector = $dbo_conn->getConnector()->setDataSource($_dataSource)->openConnection();
```

To fetch results of some query

```php
$query = 'select * from dual';
$result = $connector->fetchAll($stid);
print_r($result);
```