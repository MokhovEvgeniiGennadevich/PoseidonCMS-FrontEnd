<?php
###########################################################################
## Security Check
if ( defined('__SECURITY_CHECK__') === false ) 
{
    http_response_code(404);
    exit();
}

function return_a_b(string $host, array $data, array $keys, int $count, array $hash_keys, int $hash_count) {
    $iv = openssl_random_pseudo_bytes($keys[$count][1]);

    $json = json_encode($data);

    $encrypted = openssl_encrypt($json, $keys[$count][0], hex2bin($keys[$count][3]), $options=OPENSSL_RAW_DATA, $iv);

    $hash = hash_hmac($hash_keys[$hash_count][0], $encrypted, hex2bin($hash_keys[$hash_count][1]), true);

    return $host . 'a=' . $count . '&b=' . bin2hex($iv) . bin2hex($hash) . bin2hex($encrypted);
}

function url_api($route_name, $query) {
    global $microservice_routes;

    // Adding time
    $query['t'] = time();

    if (isset($microservice_routes[$route_name]) === false) exit();

    $json = json_encode($query);

    $iv        = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($json, 'aes-256-cbc', hex2bin($microservice_routes[$route_name][1]), $options=OPENSSL_RAW_DATA, $iv);
    $hash      = hash_hmac('sha3-512', $encrypted, hex2bin($microservice_routes[$route_name][2]), true);

    return $microservice_routes[$route_name][0] . '?a=0&b=' . bin2hex($hash) . bin2hex($iv) . bin2hex($encrypted);
}

function url_site($route_name, $query) {
    global $site_routes;

    // Adding time
    $query['t'] = time();

    if (isset($site_routes[$route_name]) === false) exit();

    $json = json_encode($query);

    $iv        = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($json, 'aes-256-cbc', hex2bin($site_routes[$route_name][1]), $options=OPENSSL_RAW_DATA, $iv);
    $hash      = hash_hmac('sha3-512', $encrypted, hex2bin($site_routes[$route_name][2]), true);

    return $site_routes[$route_name][0] . '?a=0&b=' . bin2hex($hash) . bin2hex($iv) . bin2hex($encrypted);
}