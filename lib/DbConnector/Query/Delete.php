<?php
namespace DbConnector\Query;

class Delete extends Query implements StatementInterface {

	private $values = null;

	public function __construct($source) {
		parent::__construct($source);
	}

	public function getSql() {
		return $this->sql;
	}

	public function setValues($values = array()) {}

	public function createQuery($values = array()) {
		if (count($values) > 0) $this->values = $values;
		$this->sql = 'DELETE FROM '.$this->table.' '.$this->alias;

		$conditions = $this->fetchPart('where');
		if (null !== $conditions && is_array($conditions)) {
			$this->sql .= $this->implode_r('', $conditions);
		}

		return $this;
	} 

}