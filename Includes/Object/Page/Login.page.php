<?php

/**
 * This file is part of the PHPCore forum software
 * 
 * Made by InfinCZ 
 * @link https://github.com/Infin48
 *
 * @copyright (c) PHPCore Limited https://phpcore.cz
 * @license GNU General Public License, version 3 (GPL-3.0)
 */

namespace App\Page;

use \App\Model\Cookie;
use \App\Model\Session;

/**
 * Login
 */
class Login extends Page
{
    /**
     * @var bool $useBody If false Body.phtml will not be included
     */
    protected bool $useBody = false;

    /**
     * @var string $template Page template
     */
    protected string $template = '/Login.phtml';

    /**
     * Body of this page
     *
     * @return void
     */
    public function body( \App\Model\Data $data )
    {

    }
}