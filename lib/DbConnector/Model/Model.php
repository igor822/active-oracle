<?php
namespace DbConnector\Model;

use DbConnector\DboSource;
use DbConnector\Query\Query;

class Model extends DboSource {

	protected $queryAdp = null;

	protected $table;

	protected $connector = null;

	public function __construct($dataSource) {
		parent::__construct($dataSource);
		$this->connector = $this->getConnector()->openConnection();
		$this->getQueryAdp();
	}

	public function getQueryAdp() {
		if ($this->queryAdp === null) $this->queryAdp = new Query(array('table' => $this->table));
		return $this->queryAdp;
	}

	public function find($type = 'all', $options = array()) {
		$this->buildQuery($options);
		if ($type = 'all') return $this->fetchAll($this->queryAdp->getSql());
		return $this->fetch($this->queryAdp->getSql());
	}

	public static function connect($dataSource) {
		return parent::connect($dataSource);
	}

	private function buildQuery($options) {
		if (!empty($options['fields'])) $this->queryAdp->select($options['fields']);
		else $this->queryAdp->select();

		if (!empty($options['conditions'])) $this->buildConditions($options['conditions']);
		if (!empty($options['order'])) $this->queryAdp->order($options['order']);
		if (!empty($options['group'])) $this->queryAdp->group($options['group']);
	}

	/**
	* array('conditions' => array(
	*   array('field1 = ffeff', 'field2 = qqqqqq'),
	*	array('or' => array('field1 = aaa', 'field2 = bbbb'))
	*))
	*/

	private function buildConditions($conditions) {
		if (is_array($conditions) && count($conditions) > 0) {
			foreach ($conditions as $key => $cond) {
				if ('or' == strtolower($key)) {
					if (is_array($cond)) {
						foreach ($cond as $or) $this->queryAdp->orWhere($or);
					}
				} else {
					if (is_array($cond)) {
						foreach ($cond as $cd) $this->queryAdp->where($cd);
					} else {
						$this->queryAdp->where($cond);
					}
				}
			}
		}
	}
}