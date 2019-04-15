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
}
