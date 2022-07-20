<?php
###########################################################################
### Security

define('__SECURITY_CHECK__', true);

ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);


###########################################################################
### Includes

include('../config.php');
include('../../apikun/functions.php');
include('../../system/routing.web.php');
include('../../system/functions.language.php');
include('../language/ru.php');
include('../language/en.php');

###########################################################################
### Routing

if (       $site_get['m'] === 'user_login' ) {
    include('../template/user/user.login.php');
} elseif ( $site_get['m'] === 'user_register' ) {
    include('../template/user/user.register.php');
} else {
    include('../template/main/main.index.php');
}