<?php 
// php -S localhost:8010

############################################################################
###
# GET a = int (1,2,3) -> int 
# GET b = str (fsdfsdfsdf) -> json
# POST a = int (1,2,3) -> int 
# POST b = str (fsdfsdfsdfs) -> json encoded by server
# POST c = str (fdsfsdfsdfs) -> json encoded by user

ini_set('error_reporting', -1);
$max_request_time = 60 * 60 * 24;

############################################################################
### JSON Result

$suc  = array();
$err  = array();
$log  = array();
$time = array();

function json_result() {
    global $suc, $err, $log, $time;

    $time []= round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 4);

    echo json_encode(array('suc' => $suc, 'err' => $err, 'log' => $log, 'time' => $time));
    exit;
}

$log []= 'Started 1';

############################################################################
### Checking _GET variables

$a_get = filter_input(INPUT_GET, 'a', FILTER_VALIDATE_INT);

if ($a_get === false) {
    $log []= '_GET["a"] is empty';

    json_result();
}

$b_get = (string) filter_input(INPUT_GET, 'b', FILTER_SANITIZE_ENCODED);

if ($b_get === false) {
    $log []= '_GET["b"] is empty';

    json_result();
}

$b_get_bin = hex2bin($b_get);

# $log []= '_GET["a"] is: ' . $a_get;
# $log []= '_GET["b"] is: ' . $b_get;

# Parameters

$iv   = substr($b_get_bin, 64, 16);
$hash = substr($b_get_bin, 0,  64);
$data = substr($b_get_bin, 80);

if ( strlen($iv) !== 16 ) {
    $log []= '_GET[] strlen $iv is not 16';

    json_result();
}

if ( strlen($hash) !== 64 ) {
    $log []= '_GET[] strlen $hash is not 64';

    json_result();
}

if ( strlen($data) < 1 )  {
    $log []= '_GET[] strlen $data is < 1';

    json_result();
}

// Checking HASH
$hash1 = hash_hmac('sha3-512', $data, hex2bin('dbb9d0a4f8002ec5795c1d353056ccb845b2fe3d7d3c6a74808b70c5b571b0551bd78b372f5c017be98b667831eff8f04721b5ce64b4c388462da254292c1232'), true);

if ( hash_equals($hash1, $hash) === false ) {
    $log []= '_GET hash_equals false';

    json_result();
}

// Trying to decode _GET parameters
$decrypted = openssl_decrypt($data, 'aes-256-cbc', hex2bin('b1888edbfe2baa3772f1d8ddc11d1059ba63f31c386b2a9b89919be6914d4091'), $options=OPENSSL_RAW_DATA, $iv);
if ( !$decrypted ) {
    $log []= '_GET openssl_decrypt false';

    json_result();
}

$get_json = json_decode($decrypted, true);
if ( !$get_json ) {
    $log []= '_GET json_decode false';

    json_result();
}

// Checking request time 

if ( $get_json['t'] < time() - $max_request_time) {
    $log []= '_GET time expired';

    json_result();
}

# $log []= '_GET variables passed, module: ' . $get_json['m'];

$log []= 'Module: ' . $get_json['module'];


############################################################################
// Checking _POST variables

$a_post = filter_input(INPUT_POST, 'a', FILTER_VALIDATE_INT);

if ($a_post === false) json_result();

$b_post = (string) filter_input(INPUT_POST, 'b', FILTER_SANITIZE_ENCODED);

if ($b_post === false) json_result();

$c_post = (string) filter_input(INPUT_POST, 'c', FILTER_SANITIZE_ENCODED);

if ($c_post === false) json_result();

############################################################################
// Decode server data
$c_bin = hex2bin($_POST['c']);

if ( $c_bin === false or strlen($c_bin) < 81 ) json_result();

// Parameters

$iv   = substr($c_bin, 0, 16);
$hash = substr($c_bin, 16, 64);
$data = substr($c_bin, 80);

if ( strlen($iv) !== 16 )   json_result();
if ( strlen($hash) !== 64 ) json_result();
if ( strlen($data) < 1 )    json_result();

// Checking HASH
$hash1 = hash_hmac('sha3-512', $data, hex2bin('dbb9d0a4f8002ec5795c1d353056ccb845b2fe3d7d3c6a74808b70c5b571b0551bd78b372f5c017be98b667831eff8f04721b5ce64b4c388462da254292c1232'), true);

if ( hash_equals($hash1, $hash) === false ) {
    json_result();
}

// Trying to decode server parameters
$decoded = openssl_decrypt($data, 'aes-256-cbc', hex2bin('b1888edbfe2baa3772f1d8ddc11d1059ba63f31c386b2a9b89919be6914d4091'), $options=OPENSSL_RAW_DATA, $iv);
if ( !$decoded ) json_result();

$server_decode_json = json_decode($decoded, true);
if ( !$server_decode_json ) json_result();

# Checking request time 

if ( $server_decode_json['open_time'] < time() - $max_request_time) json_result();

# $log ['server_decode_json'] = $server_decode_json;

############################################################################
// Decode user data 
$b_bin = hex2bin($b_post);

if ( $b_bin === false or strlen($b_bin) < 1 ) json_result();

// Parameters

$hash  = substr($b_bin, 0, 64);
$b_bin = substr($b_bin, 64);

if ( strlen($hash) !== 64 ) json_result();
if ( strlen($b_bin) < 1 )   json_result();

// Checking HASH
$hash1 = hash('sha512', bin2hex($b_bin), true);

if ( hash_equals($hash1, $hash) === false ) {
    json_result();
}

// Trying to decrypt user data
$user_decrypted = openssl_decrypt($b_bin, 'aes-256-cbc', hex2bin($server_decode_json['open_key']), $options=OPENSSL_RAW_DATA, hex2bin($server_decode_json['open_iv']));
if ( !$user_decrypted ) json_result();

$user_json = json_decode($user_decrypted, true);
if ( !$user_json ) {
    $log [] = 'user_json cannot parse';

    json_result();
}

# $log ['user_json'] = $user_json;

############################################################################
### Sending data to servers



function send() {
    global $log, $get_json, $user_json;

    $url = 'http://localhost:8006';

    $post = $user_json;

    $post ['module'] = $get_json['module'];

    $options = array();


    # Encode request
    $iv = openssl_random_pseudo_bytes(16);
    $key = 'b1888edbfe2baa3772f1d8ddc11d1059ba63f31c386b2a9b89919be6914d4091';
    $encrypt = openssl_encrypt(json_encode($post), 'aes-256-cbc', hex2bin($key), $options=OPENSSL_RAW_DATA, $iv);

    $hash = hash_hmac('sha3-512', $encrypt, hex2bin('2ec5b3b3afe0757ef58a233d1dc295bb2c8d28a4ff85718dade8f575df572e4de0796fbab15343c32d0e25ed8e8a1ca5ccf7d9f2a99749f6922d120e3bad4e80'), true);

    $encrypted_json = bin2hex($iv . $hash . $encrypt);

    $post = array('a' => 0, 'b' => $encrypted_json);

    $defaults = array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_PORT => 8006,
        CURLOPT_URL => $url,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_POSTFIELDS => $post
    );

    $ch = curl_init();
    curl_setopt_array($ch, $defaults);

    if( !$result = curl_exec($ch))
    {
        $log []= curl_error($ch);

        json_result();
    }
    curl_close($ch);

    $json_decode = json_decode($result, true);

    if ( !$json_decode ) {
        $log []= $result;

        json_result();
    }

    return $json_decode; 
}

$log []= 'Sending CURL request';

$curl = send();

############################################################################
### Merging suc, err, log

if ( isset($curl['suc']) !== false and isset($curl['err']) !== false and isset($curl['log']) !== false and isset($curl['time']) !== false ) {
    $suc  = array_merge($suc,  $curl['suc']);
    $err  = array_merge($err,  $curl['err']);
    $log  = array_merge($log,  $curl['log']);
    $time = array_merge($time, $curl['time']);
} else {
    $err []= 'error';
}

############################################################################
### Cookies

$cookie_keys = array(
     array(
        'aes-256-cbc',
        16,
        32,
        'b1888edbfe2baa3772f1d8ddc11d1059ba63f31c386b2a9b89919be6914d4091',
        64,
        'sha3-512',
        'dbb9d0a4f8002ec5795c1d353056ccb845b2fe3d7d3c6a74808b70c5b571b0551bd78b372f5c017be98b667831eff8f04721b5ce64b4c388462da254292c1232',
    ),
);

if ( strpos ($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $domain = false;
} else {
    $domain = $_SERVER['HTTP_HOST'];
}

$arr_cookie_options = array (
    'expires'   => time() + 60*60*24*30,
    'path'      => '/',
    'domain'    => $domain,
    'secure'    => true,
    'httponly'  => true,
    'samesite'  => 'Strict'
);
setcookie('a', '1', $arr_cookie_options);  
setcookie('b', '12321312451254363463463463463463441234124124', $arr_cookie_options);  

############################################################################
### The End 

json_result();