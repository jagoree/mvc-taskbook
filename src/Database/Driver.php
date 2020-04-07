<?php

namespace App\Database;

use App\Database\DriverInterface;
use PDO;

/**
 * Represents a database driver
 *
 * @author jagoree
 */
abstract class Driver implements DriverInterface
{

    /**
     * Configuration data
     * 
     * @var array
     */
    protected $_config = [];
    
    /**
     * Instance of PDO
     * 
     * @var PDO|null 
     */
    protected $connection = null;

    /**
     * Constructor
     * 
     * @param array $config
     * @return PDO|null
     */
    public function __construct($config)
    {
        $this->_config = $config;
        return $this->connect();
    }

    /**
     * @inheritDoc
     */
    abstract function connect();

    /**
     * Returns current connection resource
     * 
     * @return PDO|null
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Establishes a connections to the database
     * 
     * @param string $dsn
     * @param array $config
     * @return PDO
     */
    protected function _connect($dsn, $config)
    {
        $connection = new PDO($dsn, $config['user'], $config['password']);
        $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->connection = $connection;
        return $connection;
    }

}
