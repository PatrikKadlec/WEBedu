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
 * Data
 */
class Data 
{
    /**
     * @var array $data Page data
     */
    public array $data = [];

    /**
     * Sets data
     *
     * @param string $key
     * @param mixed $value
     * 
     * @return void
     */
    public function set( string $key, mixed $value )
    {
        $this->data[$key] = $value;
    }

    /**
     * Gets data
     *
     * @param string $key
     * 
     * @return mixed
     */
    public function get( string $key = null )
    {
        if (is_null($key))
        {
            return $this->data;
        }
        
        return $this->data[$key] ?? '';
    }
}
