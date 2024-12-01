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
 * Page
 */
abstract class Page
{
    /**
     * @var \App\Model\Data $data Data
     */
    protected \App\Model\Data $data;

    /**
     * @var bool $useBody If false Body.phtml will not be included
     */
    protected bool $useBody = true;

    /**
     * @var string $template Page template
     */
    protected string $template;

    /**
     * @var string $title Page title showed in <title> tag
     */
    protected string $title;

    /**
     * Constructor
     */
    public function __construct( \App\Model\Data $data )
    {
        $this->data = $data;

        if (isset($this->template)) {
            $data->set('template', $this->template);
        }

        if (isset($this->title)) {
            $data->set('title', $this->title);
        }
    }

    /**
     * Ends page and display page
     * 
     * @param string $notice
     *
     * @return void
     */
    public function end()
    {
        if (isset($_GET['ASXX'])) {
            echo $this->title ?? '';
            exit();
        }

        $template = '/Styles/Templates' . $this->data->get('template');
        if (IN_AJAX) {
            require ROOT . '/Styles/Templates' . $this->data->get('template');
            exit();
        }

        if ($this->useBody == false) {
            require ROOT . $template;
            exit();
        }

        require ROOT . '/Styles/Templates/Body.phtml';

        exit();
    }
}