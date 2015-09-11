<?php

namespace ActiveOracle\Connector;

interface ConnectorInterface
{
    public static function connect($dataSource);

    public function openConnection();

    public function fetch($stid, $type);

    public function query($sql);
}
