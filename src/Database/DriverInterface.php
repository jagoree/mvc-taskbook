<?php

namespace App\Database;

interface DriverInterface
{

    public function connect();

    public function getConnection();
}
