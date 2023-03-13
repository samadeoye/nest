<?php
use Nest\Params\Params;
use Nest\Users\UserActions;

$params = Params::getRequestParams('register');
doValidateApiParams($params);

$fname = strtoupper(trim($_POST['fname']));
$lname = strtoupper(trim($_POST['lname']));
$email = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : "";
$phone = trim($_POST['phone']);
$password = trim($_POST['password']);
$typeId = doTypeCastInt($_POST['type_id']);

$data = [
    'fname' => $fname,
    'lname' => $lname,
    'email' => $email,
    'phone' => $phone,
    'password' => $password,
    'type_id' => $typeId
];

UserActions::registerUser($data);