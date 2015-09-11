<?php
namespace ActiveOracle\Model;

use ActiveOracle\DboSource;
use ActiveOracle\Query\Query;
use ActiveOracle\Query\Insert;
use ActiveOracle\Query\Update;
use ActiveOracle\Query\Delete;

class Model extends DboSource {

	protected $queryAdp = null;

	protected $table;

	protected $connector = null;

	protected $insertAdp = null;

	protected $updateAdp = null;

	protected $deleteAdp = null;

	protected $alias = null;

	private $sourceModel = array();

	protected  $pk = '';

	public $id = null;

	public function __construct($dataSource) {
		parent::__construct($dataSource);
		$this->connector = $this->getConnector()->openConnection();
		$this->sourceModel = array('table' => $this->table, 'alias' => $this->alias);
		$this->getQueryAdp();
	}

	public function getQueryAdp($statement = 'query') {
		switch ($statement){
			case 'query':
				if ($this->queryAdp === null) $this->queryAdp = new Query($this->sourceModel);
				return $this->queryAdp;
			break;
			case 'insert':
				if ($this->insertAdp === null) $this->insertAdp = new Insert($this->sourceModel);
				return $this->insertAdp;
			break;
			case 'update':
				if ($this->updateAdp === null) $this->updateAdp = new Update($this->sourceModel);
				return $this->updateAdp;
			break;
		}
		
		return $this->queryAdp;
	}

	public function find($type = 'all', $options = array()) {
		$this->buildQuery($options);
		if ($type = 'all') return $this->fetchAll($this->queryAdp->getSql());
		return $this->fetch($this->queryAdp->getSql());
	}

	public static function connect($dataSource = null) {
		return parent::connect($dataSource);
	}

	private function buildQuery($options, &$adapter = null) {
		if (null === $adapter) $adapter = &$this->queryAdp;
		if (method_exists($adapter, 'clean')) $adapter->clean();
		if (!empty($options['fields'])) $adapter->select($options['fields']);
		else $adapter->select();

		if (!empty($options['conditions'])) $this->buildConditions($options['conditions'], $adapter);
		if (!empty($options['order'])) $adapter->order($options['order']);
		if (!empty($options['group'])) $adapter->group($options['group']);
		if (!empty($options['returning'])) {
			$adapter->returning($options['returning']);
		} 
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
	public function insert($values, $options = array()) {
		if (null === $this->insertAdp) $this->insertAdp = new Insert($this->sourceModel);
		if (count($options) > 0) {
			$this->buildQuery($options, $this->insertAdp);
		}
		$this->insertAdp->createQuery($values);
		$sql = $this->insertAdp->getSql();

		$stid = $this->prepare($sql);
		if (!empty($options['returning'])) $this->connector->bindParam($stid, 'id', $this->id);
		return $this->execute($stid);
	}

	/**
	 * Method to update data
	 *
	 * @param {array} $values Values to be updated 
	 * @param {array} $options Conditions of query to update
	 * @return boolean
	 */
	public function update($values, $options = array()) {
		if (null === $this->updateAdp) $this->updateAdp = new Update($this->sourceModel);

		$this->updateAdp->setValues($values);

		if (count($options) > 0) {
			$this->buildQuery($options, $this->updateAdp);
		}
		$sql = $this->updateAdp->createQuery()->getSql();
		//echo $sql; exit;
		return $this->query($sql);
	}

	public function delete($options = array()) {
		if (null === $this->deleteAdp) $this->deleteAdp = new Delete($this->sourceModel);
		if (count($options) > 0) {
			$this->buildQuery($options, $this->deleteAdp);
		}

		$sql = $this->deleteAdp->createQuery()->getSql();
		return $this->query($sql);
	}

	public function getPK() {
		return $this->pk;
	}
}