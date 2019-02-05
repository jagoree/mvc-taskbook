<?php
namespace App\Core;

class Application
{
    public function __construct()
	{
        session_start();
        Router::launche();
    }
}
