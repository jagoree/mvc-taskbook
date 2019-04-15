<?php
namespace App\Database;

interface DriverInterface
{    
    public function connect();
    
    public function create($data);
    
    public function update($data);
}