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
 * ListWebsites
 */
class ListWebsites extends \App\Page\Page
{
    /**
     * @var string $title Page title showed in <title> tag
     */
    protected string $title = 'Seznam webových stránek';

    /**
     * @var string $template Page template
     */
    protected string $template = '/Pages/Websites.phtml';

    /**
     * Body of this page
     *
     * @return void
     */
    public function body( \App\Model\Data $data )
    {
        $user = $data->get('admin') && isset($_GET['u']) ? $_GET['u'] : $data->get('username');
        $team = isset($_GET['t']) ? $_GET['t'] : '';
        $list = [];

        if ($team)
        {
            $collaborators = githubAPI('/repos/spskarvina/' . $team . '/collaborators');
            $collaborators = array_column($collaborators, 'login');

            if (!in_array($data->get('username'), $collaborators))
            {
                $team = '';
            }
        }

        $user = $user == 'jonda-k' ? 'spsJonasKyml' : $user;

        $data->set('websiteName', explode('-', $team)[3] ?? $user);

        foreach (glob(ROOT . '/Websites/' . ($team ?: $user) . '/*', GLOB_ONLYDIR) as $folder)
        {
            array_push($list, [
                'login' => $user, 
                'website_name' => basename($folder)
            ]);
        }

        $data->set('websites', $list);
    }
}