<?php
require_once '../util.php';
require_once '../classes/users.class.php';

$params = Params::getRequestParams('login');
doValidateApiParams($params);

$email = $emailPhone = isset($_POST['email']) ? trim($_POST['email']) : "";
$phone = trim($_POST['phone']);
$password = trim($_POST['password']);

$type = 'email';
if(empty($email))
{
    $type = 'phone';
    $emailPhone = $phone;
}

UserActions::authUser($emailPhone, $password, $type);