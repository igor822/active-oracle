<?php
namespace DbConnector;

interface ConnectionInterface {

	public static function connect($dataSource);
	
	public function _afterFind($result);

	/*public function query($sql);

	public function beforeFind();

	public function afterSave();

	public function beforeSave();*/

}