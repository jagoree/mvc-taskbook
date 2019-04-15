<?php

namespace App\Database;

/**
 * Description of Mysql
 *
 * @author zhentos
 */
class Mysql extends Driver
{

    public function connect()
    {
        $options = $this->_config + [
            'host' => 'localhost',
            'user' => null,
            'port' => 3306,
            'password' => null,
            'dbname' => null
        ];
        $dsn = [];
        foreach (['host', 'port', 'dbname'] as $key) {
            $dsn[] = "{$key}={$options[$key]}";
        }
        return $this->_connect('mysql:' . implode(';', $dsn), $options);
    }

    public function read($query)
    {
        try {
            $statement = $this->connection->query("SELECT * FROM tasks");
            foreach ($statement as $a => $b) {
                var_dump($a, $b);
            }
        } catch (\PDOException $e) {
            var_dump($e->getMessage());
        }
        die();
    }

    public function update($data)
    {
        
    }

    public function create($data)
    {
        
    }

}
