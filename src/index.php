<?php

// API CREDS
// POST /email/domain/{domain}/redirection

error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

require __DIR__ . '/vendor/autoload.php';
use \Ovh\Api;

$applicationKey = getenv('APPLICATION_KEY');
$applicationSecret = getenv('APPLICATION_SECRET');
$consumerKey = getenv('CONSUMER_KEY');
$domain = getenv('DOMAIN');
$to = getenv('TO');
$token = getenv('TOKEN');

if ($applicationKey == false || $applicationSecret == false || $consumerKey == false || $domain == false || $to == false || $token == false) {
    echo 'Missing OVH API configuration !';
    die;
}

if (!empty($_SERVER['REQUEST_URI'])) {
    $requestUriSegments = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    if (count($requestUriSegments) < 2 || $requestUriSegments[0] !== $token) {
        die('Token is incorrect or missing in the URL.');
    }
    $alias = filter_var($requestUriSegments[1], FILTER_SANITIZE_EMAIL);
} else {
    die;
}

if (!preg_match('/^[a-zA-Z0-9._%+-]+$/D', $alias)) {
    die('Invalid alias !');
}

$ovh = new Api($applicationKey,
               $applicationSecret,
               'ovh-eu',
               $consumerKey);

try {
    $result = $ovh->post('/email/domain/' . $domain . '/redirection', array(
        'localCopy' => false,
        'to' => $to,
        'from' => $alias . '@' . $domain,
    ));
    if (!empty($result['id'])) {
        echo 'Alias '.$alias.' created !';
    } else {
        die;
    }
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'This redirection already exists') !== false) {
        echo 'This redirection already exists';
    } else {
        echo 'An error occured !';
    }
}
