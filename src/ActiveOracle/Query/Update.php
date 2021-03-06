<?php

namespace ActiveOracle\Query;

class Update extends Query implements StatementInterface
{
    private $values = null;

    public function __construct($source)
    {
        parent::__construct($source);
    }

    public function getSql()
    {
        return $this->sql;
    }

    public function setValues($values = array())
    {
        if (count($values) > 0) {
            $this->values = $values;
        }
        return $this;
    }

    public function createQuery($values = array())
    {
        if (count($values) > 0) {
            $this->values = $values;
        }
        $up_field = '';
        $fields = array_keys($this->values);
        foreach ($fields as $field) {
            if (is_string($this->values[$field])) {
                $value = "'".$this->values[$field]."'";
            } elseif ($this->values[$field] instanceof \ActiveOracle\DboExpression) {
                $value = $this->values[$field]->getValue();
            } else {
                $value = $this->values[$field];
            }

            $up_field .= ($up_field != '' ? ', ' : '').$field.' = '.$value;
        }
        $this->sql = 'UPDATE '.$this->table.' SET '.$up_field;

        $conditions = $this->fetchPart('where');
        if (null !== $conditions && is_array($conditions)) {
            $this->sql .= $this->implodeRecursive('', $conditions);
        }

        return $this;
    }
}
