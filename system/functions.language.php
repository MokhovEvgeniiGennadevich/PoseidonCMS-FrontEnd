<?php
###########################################################################
## Security Check
if ( defined('__SECURITY_CHECK__') === false ) 
{
    http_response_code(404);
    exit();
}

###########################################################################
### Language functions

function __lang(string $text) {
    global $language, $user_language;

    return $language[$user_language][$text];
}