<?php
use Nest\Params\Params;
use Nest\Users\UserActions;

require_once '../includes/util.php';

$params = Params::getRequestParams('login');
doValidateApiParams($params);

$email = $emailPhone = isset($_POST['email']) ? trim($_POST['email']) : "";
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : "";
$password = trim($_POST['password']);

if(empty($email) && empty($phone))
{
    getJsonRow(false, 'Email or phone is required!');
}

$type = 'email';
if(empty($email))
{
    $type = 'phone';
    $emailPhone = $phone;
}

UserActions::authUser($emailPhone, $password, $type);