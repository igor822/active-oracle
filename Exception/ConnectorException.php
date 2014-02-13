<?php
namespace DataSource\Exception;

class ConnectorException extends \Exception {

	public function __construct($message = null, $code = null) {
		parent::__construct($message, $code);
	}

}