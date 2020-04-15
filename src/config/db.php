<?php
use App\Database\Mysql;
use App\Database\Sqlite;

return [
    'default' => [
        'host' => 'localhost',
        'user' => 'db_user',
        'password' => 'db_pass',
        'dbname' => 'db_name',
        'className' => Mysql::class
    ],
    'sqlite' => [
        'dbname' => APP . 'tmp' . DIRECTORY_SEPARATOR . 'sqlite.db',
        'className' => Sqlite::class
    ]
];