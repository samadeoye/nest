<?php
use Nest\Params\Params;
use Nest\Users\UserActions;
require_once '../includes/util.php';

$params = Params::getRequestParams('verify_account');
doValidateApiParams($params);

$token = trim($_POST['token']);
$email = $emailPhone = isset($_POST['email']) ? trim($_POST['email']) : "";
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : "";

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

UserActions::verifyUser($token, $emailPhone);