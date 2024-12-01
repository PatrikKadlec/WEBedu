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

/**
 * Logout
 */
class Logout extends Page
{
    /**
     * Body of this page
     *
     * @return void
     */
    public function body( \App\Model\Data $data )
    {
        // Remove access token and state from session
        unset($_SESSION['access_token']);
        unset($_SESSION['state']);

        // Remove user data from session
        unset($_SESSION['userData']);

        // Redirect to the homepage
        header('Location: /');
    }
}