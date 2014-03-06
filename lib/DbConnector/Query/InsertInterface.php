<?php
namespace DbConnector\Query;

interface InsertInterface {

	public function getSql();

	public function setValues($values = array());

	public function createQuery($values = array());

}