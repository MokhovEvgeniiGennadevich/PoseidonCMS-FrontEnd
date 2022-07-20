<?php
###########################################################################
## Security Check
if ( defined('__SECURITY_CHECK__') === false ) 
{
    http_response_code(404);
    exit();
}

###########################################################################
### Language pack for Russian Language

$language['ru'] = [
    'site_name' => 'АнимеКун',
    'page_generated' => 'Страница получена за',

    # User
    'login' => 'Вход',
    'register' => 'Регистрация',
];