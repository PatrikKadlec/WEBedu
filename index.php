<?php

// Root
define('ROOT', rtrim($_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']), '/'));
mb_internal_encoding('UTF-8');
ob_start();

if (!session_id()) { 
    session_start(); 
} 

//error_reporting(E_ERROR | E_PARSE);
ini_set('memory_limit', '256M');

//ini_set('display_errors', 1);
ini_set('auto_start', 0);

ini_set('opcache.enable', 0);
ini_set('opcache.enable_cli', 0);

@ini_set('session.cookie_secure', 1);
@ini_set('session.use_strict_mode', 1);
@ini_set('session.use_only_cookies ', 1);
@ini_set('session.use_trans_sid ', 0);
@ini_set('session.cookie_httponly', 1);
@ini_set('session.use_cookies ', 1);

define('IN_ACTION', isset($_GET['action']) && !empty($_GET['action']));
define('IN_AJAX', isset($_GET['AX']) || isset($_GET['action']));

$settings = json_decode(file_get_contents(ROOT . '/Includes/.htdata.json'), true);

define('TOKEN', $settings['token']);
define('OAUTH_CLIENT_ID', $settings['OAuth_clientID']);
define('OAUTH_CLIENT_SECRET', $settings['OAuth_clientSecret']);



spl_autoload_register(function ($class)
{
    $_path = explode('\\', $class);
    if (count($_path) <= 1)
    {
        return;
    }

    $shifted = array_shift($_path);

    if ($shifted != 'App') {
        $JSON = json_decode(file_get_contents(ROOT . '/vendor/autoload.json'), true);

        foreach ($JSON as $name => $data) {
            if (isset($data['autoload'][$shifted . '\\' . $_path[0] . '\\'])) {

                array_shift($_path);

                require_once ROOT . '/vendor/' . $data['name'] . '/src/' . implode('/' , $_path) . '.php';
            }
        }

        return;
    }


    $path = '/Includes/Object/' . implode('/', $_path) . '.';


    $path .= match ($_path[0])
    {
        'Page' => 'page.php',
        'Model' => 'model.php',
        'Exception' => 'exception.php',
        default => throw new \App\Exception\Exception('Class "' . $class . '" has unsupported format!')
    };
    
    if (file_exists(ROOT . $path) === false)
    {
        match ($_path[0])
        {
            'Page' => throw new \App\Exception\System('Hledaná stránka \'' . $path . '\' neexistuje!'),
            'Model' => throw new \App\Exception\System('Hledaný model \'' . $path . '\' neexistuje!'),
            'Exception' => throw new \App\Exception\System('Hledaná vyjímka \'' . $path . '\' neexistuje!')
        };
    }
    

    require_once(ROOT . $path);
});

set_exception_handler(function ($exception)
{
    //throw new \App\Exception\System($exception);

    $data = new \App\Model\Data();
    $data->set('error', $exception);
    $route = new \App\Model\Route($data);
    $route->run('/Exception');
});

setlocale(LC_ALL, 'cs_CZ.UTF-8');

function refresh() {
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit();
}

function redirect($url) {
    header('Location: ' . $url);
    exit();
}

function buildPOSTContext(array $data) {
    return stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", [
                'User-Agent: PHP',
                'Accept: application/vnd.github+json',
                'Authorization: Bearer ' . TOKEN,
                'X-GitHub-Api-Version: 2022-11-28',
            ]),
            'content' => json_encode($data)
        ]
    ]);
}

function buildPUTContext(array $data) {
    return stream_context_create([
        'http' => [
            'method' => 'PUT',
            'header' => implode("\r\n", [
                'User-Agent: PHP',
                'Accept: application/vnd.github+json',
                'Authorization: Bearer ' . TOKEN,
                'X-GitHub-Api-Version: 2022-11-28',
            ]),
            'content' => json_encode($data)
        ]
    ]);
}

function buildPATCHContext(array $data) {;
    return stream_context_create([
        'http' => [
            'method' => 'PATCH',
            'header' => implode("\r\n", [
                'User-Agent: PHP',
                'Accept: application/vnd.github+json',
                'Authorization: Bearer ' . TOKEN,
                'X-GitHub-Api-Version: 2022-11-28',
            ]),
            'content' => json_encode($data)
        ]
    ]);
}

function buildGETContext() {
    return stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", [
                'User-Agent: PHP',
                'Accept: application/vnd.github+json',
                'Authorization: Bearer ' . TOKEN,
                'X-GitHub-Api-Version: 2022-11-28',
            ])
        ],
    ]);
}

$JSON = file_get_contents(ROOT . '/Includes/Cache.json');
$JSON = json_decode($JSON, true);

function getCachedRequest($URL) {

    global $JSON;
    foreach ($JSON as $item) {
        if ($URL == $item['URL']) {
            return $item['response'];
        }
    }

    return false;
}

function cacheRequest($URL, $response) {

    global $JSON;
    array_push($JSON, [
        'URL' => $URL,
        'response' => $response
    ]);

    file_put_contents(ROOT . '/Includes/Cache.json', json_encode($JSON));
}

function githubAPI( string $URL, array $data = [], string $method = 'GET') {
    
    if (!$data) {

        $fromCache = getCachedRequest($URL);
        //print_r($fromCache);
        if ($fromCache) {
            return $fromCache;
        }

        $params = '?per_page=100';
        if (str_contains($URL, '?')) {
            $params = '&per_page=100';
        }

        $response = json_decode(@file_get_contents('https://api.github.com' . $URL . $params, false, buildGETContext()) ?: '{}', true);
        cacheRequest($URL, $response);
        return $response;
    }

    
    $context = match($method) {
        'POST' => buildPOSTContext($data),
        'PUT' => buildPUTContext($data),
        'PATCH' => buildPATCHContext($data),
        default => buildGETContext()
    };
    
    return json_decode(file_get_contents('https://api.github.com' . $URL, false, $context) ?: '{}', true);
}

require_once ROOT . '/Assets/GitHubOAuth/GitHubOAuth.php';
// Initialize Github OAuth client class 
$gitClient = new \Github_OAuth_Client([ 
    'client_id' => OAUTH_CLIENT_ID, 
    'client_secret' => OAUTH_CLIENT_SECRET
]); 

function authorize($gitClient) {

    if (isset($_GET['accessToken'])) {
        $_SESSION['access_token'] = $_GET['accessToken'];
    }
    
    if (isset($_GET['code'])) {
    
        if (!$_GET['state'] || $_SESSION['state'] != $_GET['state']) { 
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } 
            
        // Exchange the auth code for a token 
        $accessToken = $gitClient->getAccessToken($_GET['state'], $_GET['code']); 
    
        $_SESSION['access_token'] = $accessToken; 
    
        redirect('/');
    }
            
    if (!isset($_SESSION['access_token']) or !$gitUser = $gitClient->getAuthenticatedUser($_SESSION['access_token'])) {
        return false;
    }

    return $gitUser;
}

$data = new \App\Model\Data();
$route = new \App\Model\Route($data);

$git = authorize($gitClient);
if ($git == false) {

    // Generate a random hash and store in the session for security 
    $_SESSION['state'] = hash('sha256', microtime(TRUE) . rand() . $_SERVER['REMOTE_ADDR']); 
        
    // Remove access token from the session 
    //unset($_SESSION['access_token']);

    $data->set('href', $gitClient->getAuthorizeURL($_SESSION['state']));

    $route->run('/Login');
    exit();
}

// Basic variables
$data->set('login', implode(' ', array_filter(preg_split('/(?=[A-Z])/', preg_replace('/^sps/', '', $git->login)))));
$data->set('name', $git->name ?: implode(' ', preg_split('/(?=[A-Z])/', preg_replace('/^sps/', '', $git->login))));
$data->set('class', '3.D');
$data->set('username', $git->login);
$data->set('avatar_url', $git->avatar_url);

$data->set('admin', false);
if (in_array($git->login, ['PatrikKadlec']))
{
    $data->set('admin', true);
}

$route->set('/weby', '/ListWebsites');
$route->set('/studenti', '/Students');
$route->set('/info', '/Info');
$route->set('/projekty', '/Projects');

// Zobrazení webových stránek studenů - NEMAZAT!
$route->set('/Websites', '/Websites');

$route->run('/Panel');