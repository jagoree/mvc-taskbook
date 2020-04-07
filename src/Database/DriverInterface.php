<?php

namespace App\Database;

/**
 * Interface for database driver
 * 
 * @author jagoree
 */
interface DriverInterface
{

    /**
     * Establishes a connection to the database server
     */
    public function connect();

    /**
     * Returns current connection resource
     */
    public function getConnection();
}
