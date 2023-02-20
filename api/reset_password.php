<?php
use Nest\Params\Params;
use Nest\Users\UserActions;
require_once '../includes/util.php';

$params = Params::getRequestParams('reset_password');
doValidateApiParams($params);

$token = trim($_POST['token']);
$oldPassword = trim($_POST['old_password']);
$oldPassword = trim($_POST['new_password']);

UserActions::resetPassword($token, $oldPassword, $newPassword);