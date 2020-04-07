<?php

namespace App\Controller;

class TasksController extends BaseController
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
