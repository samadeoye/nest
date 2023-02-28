<?php
use Nest\Params\Params;
use Nest\Users\UserActions;
require_once '../includes/util.php';

$params = Params::getRequestParams('logout');
doValidateApiParams($params);

$token = trim($_POST['token']);

UserActions::logoutUser($token);