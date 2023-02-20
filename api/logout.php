<?php
use Nest\Params\Params;
use Nest\Users\UserActions;
require_once '../includes/util.php';

$params = Params::getRequestParams('logout');
doValidateApiParams($params);

$userId = trim($_POST['user_id']);
$token = trim($_POST['token']);

UserActions::logoutUser($userId, $token);