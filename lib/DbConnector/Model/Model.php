<?php
namespace DbConnector\Model;

use DbConnector\DboSource;
use DbConnector\Query\Query;
use DbConnector\Query\Insert;
use DbConnector\Query\Update;

class Model extends DboSource {

	protected $queryAdp = null;

	protected $table;

	protected $connector = null;

	protected $insertAdp = null;

	protected $updateAdp = null;

	protected $alias = null;

	public function __construct($dataSource) {
		parent::__construct($dataSource);
		$this->connector = $this->getConnector()->openConnection();
		$this->getQueryAdp();
	}

	public function getQueryAdp() {
		if ($this->queryAdp === null) $this->queryAdp = new Query(array('table' => $this->table, 'alias' => $this->alias));
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

	private function buildQuery($options, &$adapter = null) {
		if (null === $adapter) $adapter = &$this->queryAdp;
		if (!empty($options['fields'])) $adapter->select($options['fields']);
		else $adapter->select();

		if (!empty($options['conditions'])) $this->buildConditions($options['conditions'], $adapter);
		if (!empty($options['order'])) $adapter->order($options['order']);
		if (!empty($options['group'])) $adapter->group($options['group']);
	}

	/**
	* array('conditions' => array(
	*   array('field1 = ffeff', 'field2 = qqqqqq'),
	*	array('or' => array('field1 = aaa', 'field2 = bbbb'))
	*))
	*/

	private function buildConditions($conditions, &$adapter = null) {
		if (null === $adapter) $adapter = &$this->queryAdp;

		if (is_array($conditions) && count($conditions) > 0) {
			foreach ($conditions as $key => $cond) {
				if ('or' == strtolower($key)) {
					if (is_array($cond)) {
						foreach ($cond as $or) $adapter->orWhere($or);
					}
				} else {
					if (is_array($cond)) {
						foreach ($cond as $cd) $adapter->where($cd);
					} else {
						$adapter->where($cond);
					}
				}
			}
		}
	}

	/**
	 * Method to insert data to table
	 *
	 * @access public
	 * @param {array} $values Values to be added to table
	 * @return boolean
	 */ 
	public function insert($values) {
		if (null === $this->insertAdp) $this->insertAdp = new Insert(array('table' => $this->table, 'alias' => $this->alias));
		$this->insertAdp->createQuery($values);
		$sql = $this->insertAdp->getSql();
		return $this->execute($sql);
	}

	/**
	 * Method to update data
	 *
	 * @param {array} $values Values to be updated 
	 * @param {array} $options Conditions of query to update
	 * @return boolean
	 */
	public function update($values, $options = array()) {
		if (null === $this->updateAdp) $this->updateAdp = new Update(array('table' => $this->table, 'alias' => $this->alias));

		$this->updateAdp->setValues($values);

		if (count($options) > 0) {
			$this->buildQuery($options, $this->updateAdp);
		}
		$sql = $this->updateAdp->createQuery()->getSql();
		//echo $sql; exit;
		return $this->execute($sql);
	}
}