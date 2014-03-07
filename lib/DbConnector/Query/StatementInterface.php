<?php
namespace DbConnector\Query;

interface StatementInterface {

	public function getSql();

	public function setValues($values = array());

	public function createQuery($values = array());

}