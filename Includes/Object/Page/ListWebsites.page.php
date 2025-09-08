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

        $branches = githubAPI('/repos/spskarvina/WEB-' . $user . '/git/refs/heads');
        foreach ($branches as $branch)
        {
            $branchName = str_replace('refs/heads/', '', $branch['ref']);
            $branchNameURL = $branchName;
            if (count(explode('/', $branchNameURL)) > 1) {
                $branchNameURL = explode('/', $branchNameURL)[1];
            }
            $commit = githubAPI('/repos/spskarvina/WEB-' . $user . '/commits/' . $branch['object']['sha']);

            $url = '/Websites/' . $user . '/' . $branchNameURL . '/';

            $folderExists = is_dir(ROOT . $url);
            if (!$folderExists) {
                continue;
            }
            
            $files = glob(ROOT . $url . '*');

            $indexFound = false;
            $appFolderFound = false;
            foreach ($files as $file) {
                if (str_ends_with($file, '/index.php') or str_ends_with($file, '/index.html')) {
                    $indexFound = true;
                    break;
                }
            }

            if (!$indexFound) {
                foreach ($files as $file) {
                    if (str_ends_with($file, '/app')) {

                        $files = glob(ROOT . $url . 'app/*');
                        foreach ($files as $file) {
                            if (str_ends_with($file, '/index.php') or str_ends_with($file, '/index.html')) {
                                $appFolderFound = true;
                                $url .= 'app/';
                                break 2;
                            }
                        }
                    }
                }
            }

            if (!$indexFound and !$appFolderFound) {
                $url = '';
            }

            $listOfCommits = githubAPI('/repos/spskarvina/WEB-' . $user . '/commits?sha=' . $branchName);
            if (!$listOfCommits) {
                continue;
            }

            array_push($list, [
                'login' => $user,
                'commitMessage' => $commit['commit']['message'],
                'commitDate' => date_format(date_create($commit['commit']['author']['date']), 'd. m. Y H:i'),
                'website_name' => $branchName,
                'url' => $url,
                'urlDefault' => $indexFound,
                'urlApp' => $appFolderFound
            ]);

        }

        $data->set('websites', $list);
    }
}