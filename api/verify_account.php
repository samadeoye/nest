<?php
use Nest\Params\Params;
use Nest\Users\UserActions;
require_once '../includes/util.php';

$params = Params::getRequestParams('verify_account');
doValidateApiParams($params);

$token = trim($_POST['token']);

UserActions::verifyUser($token);