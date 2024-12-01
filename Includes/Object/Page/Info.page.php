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
 * Info
 */
class Info extends Page
{
    /**
     * @var string $title Page title showed in <title> tag
     */
    protected string $title = 'Informace';

    /**
     * @var string $template Page template
     */
    protected string $template = '/Info.phtml';

    /**
     * Body of this page
     *
     * @return void
     */
    public function body( \App\Model\Data $data )
    {
        $this->data->set('classes', [
            0 => [
                'class' => '3.D',
                'students' => [
                    0 => [
                        'first_name' => 'Petr',
                        'last_name' => 'Vopšálek'
                    ],
                    1 => [
                        'first_name' => 'Luboš',
                        'last_name' => 'Vystrčil'
                    ],
                    2 => [
                        'first_name' => 'Lukáš',
                        'last_name' => 'Kondziolek'
                    ]
                ]
            ]
        ]);
    }
}