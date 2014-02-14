<?php
namespace DbConnector\Exception;

class StatementException extends \Exception {

	public function __construct($err = null) {
		parent::__construct($err['message'], $err['code']);
	}

}