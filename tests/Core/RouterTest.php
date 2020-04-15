<?php

use PHPUnit\Framework\TestCase;
use App\Core\Router;

class RouterTest extends TestCase
{
    /**
     * 
     * @dataProvider uriCollection
     */
    public function testParseRequest($uri)
    {
        $_SERVER['REQUEST_URI'] = $uri;
        
        $config = Router::parseRequest();
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('controller', $config);
        $this->assertArrayHasKey('action', $config);
    }
    
    /**
     * 
     * @dataProvider uriCollection
     */
    public function testParseRequestHasKeys($uri)
    {
        $_SERVER['REQUEST_URI'] = $uri;
        
        $config = Router::parseRequest();
        
        $this->assertArrayHasKey('controller', $config);
        $this->assertArrayHasKey('action', $config);
    }
    
    public function testParseRequestMainpage()
    {
        $_SERVER['REQUEST_URI'] = '/';
        
        $config = Router::parseRequest();
        
        $this->assertEquals('Tasks', $config['controller']);
    }
    
    public function uriCollection()
    {
        return [
            ['/'],
            ['/tasks'],
            ['path/to/file'],
            ['/path/to/file']
        ];
    }
}
