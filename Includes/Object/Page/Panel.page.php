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
 * Panel
 */
class Panel extends Page
{
    /**
     * @var string $title Page title showed in <title> tag
     */
    protected string $title = 'Panel';

    /**
     * @var string $template Page template
     */
    protected string $template = '/Pages/Panel.phtml';

    /**
     * Body of this page
     *
     * @return void
     */
    public function body( \App\Model\Data $data )
    {
        $user = $data->get('admin') && isset($_GET['u']) ? $_GET['u'] : $data->get('username');
        $data->set('username', $user);
        $data->set('welcome_name', match(explode(' ', $data->get('login'))[0]) {
            'Matej' => 'Matěji',
            'Alexandra' => 'Alexandro',
            'Jonas' => 'Jonáši',
            'Petr' => 'Petře',
            'Lukas' => 'Lukáši',
            'Tomas' => 'Tomáši',
            'Jan' => 'Honzo',
            'Ales' => 'Aleši',
            'Lubos' => 'Luboši',
            'Martin' => 'Martine',
            'Alex' => 'Alexi',
            'Marek' => 'Marku',
            'Vojtech' => 'Vojto',
            'Filip' => 'Filipe',
            'Jakub' => 'Jakube',
            'Krystof' => 'Kryštofe',
            'Ondrej' => 'Ondro',
            'Viktor' => 'Viktore',
            'Michal' => 'Michale',
            'Matyas' => 'Matyáši',
            'Adam' => 'Adame',
            'Andrej' => 'Andreji',
            'David' => 'Davide',
            'Pavel' => 'Pavle',
            'Jindrich' => 'Jindro',
            'Patrik' => 'Patriku',
            'Samuel' => 'Samueli',
            'Sebastian' => 'Sebastiane',
            'Antonín' => 'Tondo',
            'Mojmir' => 'Mojmíre',
            'Radovan' => 'Radovane',
            'Libor' => 'Libore',
            'Zdenek' => 'Zdenku',
            'Milos' => 'Miloši',
            'Richard' => 'Richarde',
            'Milan' => 'Milane',
            'Roman' => 'Romane',
            'Radim' => 'Radime',
            'Edvard' => 'Edo',
            'Artur' => 'Arture',
            'Simon' => 'Šimone',
            'Daniel' => 'Danieli',
            'Robert' => 'Roberte',
            'Rostislav' => 'Rostislave',
            'Tomasz' => 'Tomaszi',
            'Karel' => 'Karle',
            'Josef' => 'Josefe',

            'Lenka' => 'Lenko',
            'Klara' => 'Kláro',
            'Katka' => 'Katko',
            'Petra' => 'Petro',
            'Laura' => 'Lauro',
            'Michaela' => 'Michaelo',
            'Iveta' => 'Iveto',
            'Gabina' => 'Gábino',
            'Tereza' => 'Terezo',
            'Daniela' => 'Danielo',
            'Veronika' => 'Veroniko',
            'Martina' => 'Martino',

            default => explode(' ', $data->get('login'))[0]
        });

        $size = 0;
        $modified = [];
        $getNumberOfFiles = function($path) use (&$getNumberOfFiles, &$size, &$modified)
        {
            $number = 0;
            $path = str_replace(ROOT, '', $path);
            if (is_dir(ROOT . $path))
            {
                foreach (glob(ROOT . $path . '/*') as $file)
                {
                    $number += $getNumberOfFiles($file);
                }

                return $number;
            }
            if (!file_exists(ROOT . $path))
            {
                return 'N/A';
            }
            $size += filesize(ROOT . $path);
            array_push($modified, filemtime(ROOT . $path));
            return 1;
        };

        // FTP
        $data->set('ftp.server', 'N/A');
        $data->set('ftp.username', 'N/A');
        $data->set('ftp.password', 'N/A');
        $variables = githubAPI('/repos/spskarvina/WEB-' . $data->get('username') . '/actions/variables');
        foreach ($variables['variables'] ?? [] as $var) {
            
            if (!str_starts_with($var['name'], 'FTP_')) {
                continue;
            }
            $data->set(str_replace('_', '.', strtolower($var['name'])), $var['value']);
        }


        $repositories = githubAPI('/orgs/spskarvina/repos?per_page=200&visibility=al');
        $found = false;
        foreach($repositories as $repo) {
            if ($repo['name'] == 'WEB-' . $data->get('username')) {
                $found = true;
                $data->set('repo_clone_url', $repo['clone_url']);
                break;
            }
        } 
        $data->set('repository_exists', $found);
        $branchesRoot = array_column(githubAPI('/repos/spskarvina/WEB/git/refs/heads'), 'ref');
        $branchesUser = array_column(githubAPI('/repos/spskarvina/WEB-' . $data->get('username') . '/git/refs/heads'), 'ref');
        $diff = array_diff($branchesRoot, $branchesUser);
        $data->set('repository_synchronized', empty($diff));



        
        $data->set('files',  $getNumberOfFiles('/Websites/' . $data->get('username') . '/'));
        $data->set('size',  str_replace('.', ',', (string)((float)($size / 1024))));
        
        $modifiedValue = '';
        arsort($modified);
        $modified = $modified[0] ?? 'N/A';
        $data->set('modified', $modified);
        if (is_numeric($modified))
        {
            if (date('d/m/Y', $modified) == date('d/m/Y'))
            {
                $modifiedValue = 'Dnes v ' . date('G:i', $modified);
            }

            if (date('d/m/Y', $modified) == date('d/m/Y', time() - (24 * 60 * 60)))
            {
                $modifiedValue = 'Včera v ' . date('G:i', $modified);
            }

            if (!$modifiedValue)
            {
                $modifiedValue = ucfirst((string)strftime('%B %e, %Y', $modified));
            }

            $data->set('modified', $modifiedValue);
        }
    }

    public function action_createRepository( \App\Model\Data $data ) {

        $repositoryName = 'WEB-' . $data->get('username');
        
        // Create repository
        githubAPI('/repos/spskarvina/WEB/forks', [
            'organization' => 'spskarvina',
            'name' => $repositoryName,
            'default_branch_only ' => false
        ], 'POST');

        // Create FTP_USERNAME variable
        githubAPI('/repos/spskarvina/' . $repositoryName . '/actions/variables', [
            'name' => 'FTP_USERNAME',
            'value' => 'w268405_' . $data->get('username')
        ], 'POST');

        // Create FTP_SERVER variable
        githubAPI('/repos/spskarvina/' . $repositoryName . '/actions/variables', [
            'name' => 'FTP_SERVER',
            'value' => '268405.w5.wedos.net'
        ], 'POST');

        // Create FTP_PASSWORD variable
        githubAPI('/repos/spskarvina/' . $repositoryName . '/actions/variables', [
            'name' => 'FTP_PASSWORD',
            'value' => '-'
        ], 'POST');

        // Add owner to collaborator
        githubAPI('/repos/spskarvina/' . $repositoryName . '/collaborators/' . $data->get('username'), [
            'permission' => 'maintain'
        ], 'PUT');

        return true;
    }

    public function action_synchronizeRepository( \App\Model\Data $data ) {

        $branchesRoot = array_column(githubAPI('/repos/spskarvina/WEB/git/refs/heads'), 'red');
        $branchesUser = array_column(githubAPI('/repos/spskarvina/WEB-' . $data->get('username') . '/git/refs/heads'), 'ref');

        $diff = array_diff($branchesRoot, $branchesUser);

        foreach ($diff as $branch) {

            $branch = str_replace('refs/heads/', '', $branch);

            $lastCommit = githubAPI('/repos/spskarvina/WEB/git/refs/heads/' . $branch);

            $sha = $lastCommit['object']['sha'];
            $response = githubAPI('/repos/spskarvina/WEB-' . $data->get('username') . '/git/refs', [
                'ref' => 'refs/heads/' . $branch,
                'sha' => $sha
            ], 'POST');
        }

        return true;
    }
}