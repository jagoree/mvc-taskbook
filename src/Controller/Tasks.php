<?php

namespace App\Controller;

class Tasks extends Base
{

    public function shutdown()
    {
        if (isset($_SESSION['added'])) {
            unset($_SESSION['added']);
        }
        if (isset($_SESSION['edited'])) {
            unset($_SESSION['edited']);
        }
    }

}
