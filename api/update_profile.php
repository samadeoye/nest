<?php
require_once '../util.php';
require_once '../classes/users.class.php';

$params = Params::getRequestParams('update_profile');
doValidateApiParams($params);

$fname = trim($_POST['fname']);
$lname = trim($_POST['lname']);
$email = isset($_POST['email']) ? trim($_POST['email']) : "";
$phone = trim($_POST['phone']);
$typeId = typeCastInt($_POST['type_id']);

$data = [
    'fname' => $fname,
    'lname' => $lname,
    'email' => $email,
    'phone' => $phone,
    'type_id' => $typeId
];

UserActions::updateUser($data);