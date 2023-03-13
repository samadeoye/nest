<?php
use Nest\Params\Params;
use Nest\Users\UserActions;

$params = Params::getRequestParams('logout');
doValidateApiParams($params);

$token = trim($_POST['token']);

UserActions::logoutUser($token);