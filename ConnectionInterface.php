<?php
namespace DataSource;

interface ConnectionInterface {

	public static function connect($dataSource);

	/*public function query($sql);

	public function afterFind();

	public function beforeFind();

	public function afterSave();

	public function beforeSave();*/

}