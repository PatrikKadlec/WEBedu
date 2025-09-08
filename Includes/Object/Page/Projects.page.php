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
 * Projects
 */
class Projects extends \App\Page\Page
{
    /**
     * @var string $title Page title showed in <title> tag
     */
    protected string $title = 'Ukázkové projekty';

    /**
     * @var string $template Page template
     */
    protected string $template = '/Pages/Projects.phtml';

    /**
     * Body of this page
     *
     * @return void
     */
    public function body( \App\Model\Data $data )
    {
       
    }
}