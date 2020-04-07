<?php

namespace App\Controller;

/**
 *
 * @author jagoree
 */
interface ControllerInterface
{
    /**
     * 
     * @param array $data Variables for template file
     * @param string $name The name of the rendering template file
     */
    public function render(array $data = [], string $name = null);
}
