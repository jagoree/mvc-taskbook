<?php

namespace App\Database;

use App\Database\DriverInterface;
use PDO;

/**
 * Description of Driver
 *
 * @author zhentos
 */
abstract class Driver implements DriverInterface
{

    protected $_config = [];
    protected $connection = null;

    public function __construct($config)
    {
        $this->_config = $config;
        return $this->connect();
    }

    abstract function connect();

    public function getConnection()
    {
        return $this->connection;
    }

    protected function _connect($dsn, $config)
    {
        $connection = new PDO($dsn, $config['user'], $config['password']);
        $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->connection = $connection;
        return $connection;
    }

}
