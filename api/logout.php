<?php
require_once '../util.php';
require_once '../classes/users.class.php';

$params = Params::getRequestParams('logout');
doValidateApiParams($params);

$userId = trim($_POST['user_id']);
$token = trim($_POST['token']);

UserActions::logoutUser($userId, $token);