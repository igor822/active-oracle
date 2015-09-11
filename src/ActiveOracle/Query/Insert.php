<?php

namespace ActiveOracle\Query;

class Insert extends Query implements StatementInterface
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
        $field_str = $value_str = '';
        $fields = array_keys($this->values);
        foreach ($fields as $field) {
            $field_str .= ($field_str != '' ? ', ' : '').$field;

            if (is_string($this->values[$field])) {
                $value = "'".$this->values[$field]."'";
            } elseif ($this->values[$field] instanceof \ActiveOracle\DboExpression) {
                $value = $this->values[$field]->getValue();
            } else {
                $value = $this->values[$field];
            }

            $value_str .= ($value_str != '' ? ', ' : '').$value;
        }

        $this->sql = 'INSERT INTO '.$this->table.' ('.$field_str.') VALUES ('.$value_str.')';

        if ($returning = $this->fetchPart('returning')) {
            $this->sql .= ' '.$returning;
        }
        return $this;
    }
}
