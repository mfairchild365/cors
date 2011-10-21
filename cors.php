<?php
/*
 * This is kind of an ugly hack.  In the end it is the same as 
 * 'Access-Control-Allow-Origin: *'.  
 * In orde to use 'Allow Credentials: true' you can /not/ set 'Allow-Origin' to '*'.
 * 
 * So, the ugly hack is to take the origin that was sent to us, and put in in the Allow-Origin header.
 */

//Get the headers.
$headers = getallheaders();


//set the origin to allow all by default.
$origin = "*";

//Check if the Origin header was set.  If it was, make that the new origin.
if (isset($headers['Origin'])) {
    $origin = $headers['Origin'];
}

// Specify domains from which requests are allowed (in this case, the same one that requested).
header('Access-Control-Allow-Origin: ' . $origin);

//Allow cookies to be passed.
header('Access-Control-Allow-Credentials: true');

// Specify which request methods are allowed
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

/*
 * jQuery < 1.4.0 adds an X-Requested-With header which requires pre-flighting
 * requests. This involves an OPTIONS request before the actual GET/POST to 
 * make sure the client is allowed to send the additional headers.
 * We declare what additional headers the client can send here.
 */

// Additional headers which may be sent along with the CORS request
header('Access-Control-Allow-Headers: X-Requested-With');

// Set the age to 1 day to improve speed/caching.
header('Access-Control-Max-Age: 86400');

// Exit early so the page isn't fully loaded for options requests
if (strtolower($_SERVER['REQUEST_METHOD']) == 'options') {
    exit();
}

//Start the lovely session.
session_start();

//initalize a view_count to 0 if it wasn't already set.
if (!isset($_SESSION['view_count'])) {
    $_SESSION['view_count'] = 0;
}

//Increment the view count.
$_SESSION['view_count']++;

// If raw post data, this could be from IE8 XDomainRequest
// Only use this if you want to populate $_POST in all instances
if (isset($HTTP_RAW_POST_DATA)) {
    $data = explode('&', $HTTP_RAW_POST_DATA);
    foreach ($data as $val) {
        if (!empty($val)) {
            list($key, $value) = explode('=', $val);   
            $_POST[$key] = urldecode($value);
        }
    }
}

echo 'Hello CORS, this is '
     . $_SERVER['SERVER_NAME'] . PHP_EOL
     .'You sent a '.$_SERVER['REQUEST_METHOD'] . ' request.' . PHP_EOL;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo 'Your name is ' . htmlentities($_POST['name']) . PHP_EOL;;
}

echo "View Count for session: " . $_SESSION['view_count'];