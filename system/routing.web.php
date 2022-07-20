<?php
###########################################################################
## Security Check
if ( defined('__SECURITY_CHECK__') === false ) 
{
    http_response_code(404);
    exit();
}

###########################################################################
### Parsing Language

# Trying to autodetect if we have no language in get query
if ( isset($site_get['l']) === false or in_array($site_get['l'], $site_languages) === false ) {
    $temp = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    $user_language = in_array($temp, $site_languages) ? $temp : $site_languages[0];
}

###########################################################################
### Parsing _GET

if ( isset($_GET['a']) === false or isset($_GET['b']) === false ) {
    header('Location: ' . __urlWeb(['m' => 'main']), true, 301);
    exit();
}

if ( $_GET['a'] < 0 or $_GET['a'] > $urlWeb_count or isset($urlWeb[$_GET['a']]) === false ) {
    header('Location: ' . __urlWeb(['m' => 'main']), true, 301);
    exit();
}

## Decrypting

$site_get = decrypt_a_b($urlWeb[$_GET['a']], $_GET['b']);

###########################################################################
### Parsing Language from _GET query

if ( isset($site_get['l']) !== false ) {
    $user_language = in_array($site_get['l'], $site_languages) ? $site_get['l'] : $site_languages[0];
} else {
    header('Location: ' . __urlWeb(['m' => 'main']), true, 301);
    exit();
}

###########################################################################
### _GET Url Generate Encrypt

function __urlWeb(array $array) {
    global $urlWeb, $urlWeb_count, $user_language;

    # Permanently adding language
    if ( isset($array['l']) === false )
        $array['l'] = $user_language;

    # Encoding data to json 
    $json = json_encode($array);

    # Encryption data
    $enc = $urlWeb[$urlWeb_count];

    return '/?a='.$urlWeb_count.'&b=' . encrypt_a_b($enc, $json);
}

###########################################################################
### Encrypting data and returning a/b

function encrypt_a_b (array $enc, string $data) {
    $encrypted = openssl_encrypt($data, $enc[2], hex2bin($enc[1]), $options=OPENSSL_RAW_DATA, hex2bin($enc[0]));
    $hash      = hash_hmac($enc[5], $encrypted, hex2bin($enc[6]), true);

    return bin2hex($hash) . $enc[0] . bin2hex($encrypted);
}

function decrypt_a_b (array $enc, string $data) {
    $data = hex2bin($data);

    $hash_incoming      = substr($data, 0, $enc[7]);
    $iv_incoming        = substr($data, $enc[7], $enc[3]);
    $encrypted_incoming = substr($data, $enc[7] + $enc[3]);

    # Checking HASH
    $hash_canculated = hash_hmac($enc[5], $encrypted_incoming, hex2bin($enc[6]), true);

    if ( hash_equals($hash_incoming, $hash_canculated) === false ) {
        header('Location: ' . __urlWeb(['m' => 'main']), true, 301);
        exit();
    }

    # Decrypt data
    $decrypted_incoming = openssl_decrypt($encrypted_incoming, $enc[2], hex2bin($enc[1]), $options=OPENSSL_RAW_DATA, $iv_incoming);
    if ( !$decrypted_incoming ) {
        header('Location: ' . __urlWeb(['m' => 'main']), true, 301);
        exit();
    }

    # Checking Json 
    $json = json_decode($decrypted_incoming, true);

    if ( !$json ) {
        header('Location: ' . __urlWeb(['m' => 'main']), true, 301);
        exit();
    }

    return $json;
}
