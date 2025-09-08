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
 * Websites
 */
class Websites extends Page
{
    /**
     * Body of this page
     *
     * @return void
     */
    public function body( \App\Model\Data $data )
    {
        define('PATH', explode('?', $_SERVER['REQUEST_URI'])[0]);

        $arrowBack = '<a class="arrow-back" href="/" style="font-family:Arial;z-index:999;display:block;position:fixed;bottom:2rem;right:2rem;background-color:#ff581c;border-radius:3px;box-shadow:1px 1px 20px #00000040;padding:0.6rem 0.8rem;font-size:1rem;text-decoration:none;font-weight:600;color:white;transition:0.3s ease background;}.arrow-back:hover{background:#d16717;color:white;">< Zpět</a>';

        if (is_dir(ROOT . PATH))
        {
            if (file_exists(ROOT . PATH . '/index.html'))
            {
                require ROOT . PATH . '/index.html';
                //echo $arrowBack;
                exit();
            }

            if (file_exists(ROOT . PATH . '/index.php'))
            {
                require ROOT . PATH . '/index.php';
                //echo $arrowBack;
                exit();
            }

            throw new \App\Exception\System('Index soubor nebyl nalezen!');
        }

        if (file_exists(ROOT . PATH))
        {
            require ROOT . PATH;
            exit();
        }

        throw new \App\Exception\System('Soubor nebyl nalezen!');
    }
}