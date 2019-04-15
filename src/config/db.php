<?php
return [
    'default' => [
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'pass',
        'dbname' => 'dbname',
        'className' => '\App\Database\Mysql'
    ],
    'sqlite' => [
        'dbname' => '../tmp/sqlite.db',
        'className' => '\App\Database\Sqlite'
    ]
];