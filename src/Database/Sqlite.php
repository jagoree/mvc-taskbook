<?php

namespace App\Database;

/**
 * Sqlite driver
 *
 * @author jagoree
 */
class Sqlite extends Driver
{

    public function connect()
    {
        $options = [
            'user' => null,
            'password' => null
        ];
        $pathToDb = dirname($this->_config['dbname']);
        if (!is_dir($pathToDb)) {
            mkdir($pathToDb, 0777, true);
        }
        $connection = $this->_connect('sqlite:' . $this->_config['dbname'], $options);
        $this->checkIfDbExist();
        return $connection;
    }

    private function checkIfDbExist()
    {
        if (!file_exists(realpath($this->_config['dbname']))) {
            $this->createDb();
        }
    }
    
    private function createDb()
    {
        $this->connection->query("
            CREATE TABLE tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                content TEXT NOT NULL,
                checked INTEGER unsigned NOT NULL DEFAULT '0',
                created DATETIME DEFAULT (CURRENT_TIMESTAMP)
            );");
    }

}
