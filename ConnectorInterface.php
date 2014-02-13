<?php
namespace DataSource;

interface ConnectorInterface {

	private $_dataSource = array();

	public static function connect($dataSource);

	public function query($sql);

	public function afterFind();

	public function beforeFind();

	public function afterSave();

	public function beforeSave();

}