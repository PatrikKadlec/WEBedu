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

namespace App\Model;

/**
 * Database
 */
class Route
{
    /**
     * @var string $path URL path
     */
    private string $path;

    /**
     * Constructor
     */
    public function __construct( \App\Model\Data $data )
    {
        $this->data = $data;
        $this->path = explode('?', $_SERVER['REQUEST_URI'])[0];
    }

    public function getPath() {
        return $this->path;
    }

    /**
     * Returns true if application is connected with database
     *
     * @return void
     */
    public function set( string $url, $page )
    {
        if (str_starts_with($this->path, $url)) {

            $page = '\App\Page' . str_replace('/', '\\', $page);
     
            $page = new $page($this->data);
            $page->body($this->data);
            $page->end();

            exit(0);
        }
    }

    public function run( $page)
    {
        $page = '\App\Page' . str_replace('/', '\\', $page);
        
        $page = new $page($this->data);
        $page->body($this->data);
        $page->end();
        
        exit(0);
    }
}