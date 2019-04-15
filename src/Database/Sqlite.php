<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Database;

/**
 * Description of Sqlite
 *
 * @author zhentos
 */
class Sqlite extends Driver
{
    public function connect()
    {
        $options = [
            'user' => null,
            'password' => null
        ];
        $this->checkIfDbExist();
        return $this->_connect('sqlite:' . $this->_config['dbname'], $options);
    }
    
    private function checkIfDbExist()
    {
        if (!file_exists(realpath($this->_config['dbname']))) {
            $this->connection->query("CREATE TABLE tasks (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name TEXT NOT NULL, email TEXT NOT NULL, content TEXT NOT NULL, checked INTEGER unsigned NOT NULL DEFAULT '0', created DATETIME DEFAULT (CURRENT_TIMESTAMP));");
        }
    }
}
