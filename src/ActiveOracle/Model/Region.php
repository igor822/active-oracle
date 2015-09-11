<?php

namespace ActiveOracle\Model;

class Region extends Model
{
    protected $table = 'regions';

    protected $alias = 'p';

    public function __construct($dsorce)
    {
        parent::__construct($dsorce);
    }

    public function afterFind($result = null, $type = 'array')
    {
        foreach ($result as $row => $values) {
            $result[$row]['REGION_NAME'] = $values['REGION_NAME'].' sdcssds';
        }
        return $result;
    }
}
