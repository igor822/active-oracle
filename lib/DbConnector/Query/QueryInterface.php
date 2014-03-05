<?php
namespace DbConnector\Query;

interface QueryInterface {

	public function select($fields = '*');

}