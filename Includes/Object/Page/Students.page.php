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
 * Students
 */
class Students extends Page
{
    /**
     * @var string $title Page title showed in <title> tag
     */
    protected string $title = 'Seznam studentů';

    /**
     * @var string $template Page template
     */
    protected string $template = '/Pages/Students.phtml';

    /**
     * Body of this page
     *
     * @return void
     */
    public function body( \App\Model\Data $data )
    {
        $teams = githubAPI('/orgs/spskarvina/teams');
        //print_r($teams);
        $list = [];

        $row = [];

        foreach ($teams as $team) {
            if ($team['name'] != 'WEB') {
                continue;
            }

            $class = $team['parent']['name'];

            $row = [
                'class' => $class,
                'members' => []
            ];

            $members = githubAPI('/orgs/spskarvina/teams/' . $team['parent']['slug'] . '/members');
            //print_r($members);
            //print_r(json_decode($result2, true));

            foreach ($members as $member) {

                if (!is_dir(ROOT . '/Websites/' . $member['login'])) {
                    //continue;
                }

                array_push($row['members'], [
                    'name' => $member['login']
                ]);
            }

            array_push($list, $row);
        }

        $this->data->set('classes', $list);
    }
}