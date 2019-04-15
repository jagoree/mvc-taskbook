<?php
return [
    'default' => [
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'pass',
        'dbname' => 'beejee',
        'className' => '\App\Database\Mysql'
    ],
    'sqlite' => [
        'dbname' => '../tmp/beejee.sqlite',
        'className' => '\App\Database\Sqlite'
    ]
];