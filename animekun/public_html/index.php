<?php
include('../config.php');
include('../../apikun/functions.php');


if ( $_GET['a'] === 'user_login' ) {
    include('../template/user_login.php');
}
elseif ( $_GET['a'] === 'user_register' ) {
    include('../template/user_register.php');
}
else {
    include('../template/index.php');
}