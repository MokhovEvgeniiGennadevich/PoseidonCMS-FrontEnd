<?php
###########################################################################
## Security Check
if ( defined('__SECURITY_CHECK__') === false ) 
{
    http_response_code(404);
    exit();
}

###########################################################################
### Language pack for English Language

$language['en'] = [
    'site_name' => 'AnimeKun',
    'page_generated' => 'Page downloaded',

    # User
    'login' => 'Login',
    'register' => 'Register',
];