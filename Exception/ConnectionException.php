<?php
namespace DataSource\Exception\ConnectionException;

class ConnectionException extends \Exception {

	public function __construct($err = null) {
		parent::__construct($err['message'], $err['code']);
	}

}