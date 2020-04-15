<?php

use PHPUnit\Framework\TestCase;
use App\Model\Task;

class TaskTest extends TestCase
{
    protected $task = null;
    
    public function setUp(): void
    {
        require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'config/paths.php';
        $this->task = new Task();
    }
    
    public function testValidationIsOk()
    {
        $result = $this->task->validate([
            'name' => 'User',
            'email' => 'email@domain.tld',
            'content' => 'Lorem ipsum etc...'
        ]);
        
        $this->assertEmpty($result);
    }
    
    public function testValidationEmptyName()
    {
        $result = $this->task->validate([
            'email' => 'email@domain.tld',
            'content' => 'Lorem ipsum etc...'
        ]);
        
        $this->assertArrayHasKey('name', $result);
    }
    
    /**
     * @dataProvider wrongEmails
     */
    public function testValidationWrongEmail($email)
    {
        $result = $this->task->validate([
            'name' => 'User',
            'email' => $email,
            'content' => 'Lorem ipsum etc...'
        ]);
        
        $this->assertArrayHasKey('email', $result);
    }
    
    public function testValidationErrorShortContent()
    {
        $result = $this->task->validate([
            'name' => 'User',
            'email' => 'email@domain.tld',
            'content' => 'Lorem'
        ]);
        
        $this->assertArrayHasKey('content', $result);
    }
    
    /**
     * @dataProvider wrongData
     */
    public function testValidationHasErrors($name, $email, $content)
    {
        $result = $this->task->validate(
                compact('name', 'email', 'content')
        );
        
        $this->assertNotEmpty($result);
    }
    
    public function wrongEmails()
    {
        return [
            ['email@'],
            ['email@domian'],
            ['/path/to/file'],
            ['domain.tld'],
            ['email@domain@dot.com'],
        ];
    }
    
    public function wrongData()
    {
        return [
            [
                null,
                null,
                null
            ],
            [
                null,
                'email@domain.tld',
                'Lorem ipsum etc...'
            ],
            [
                null,
                null,
                'Lorem ipsum etc...'
            ],
            [
                'User',
                null,
                'Lorem ipsum etc...'
            ],
            [
                'User',
                'email@domain.tld',
                null
            ]
        ];
    }
}
