<?php

use PHPUnit\Framework\TestCase;
use App\Database\Connection;
use App\Database\DriverInterface;

class ConnectionTest extends TestCase
{
    public function setUp(): void
    {
        require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'config/paths.php';
        Connection::reset();
    }
    
    public function testGetIsMysql()
    {
        $result = Connection::get('default');
        
        $this->assertInstanceOf(DriverInterface::class, $result);
    }
    
    public function testGetIsSqlite()
    {
        $result = Connection::get('sqlite');
        
        $this->assertInstanceOf(DriverInterface::class, $result);
    }
}
