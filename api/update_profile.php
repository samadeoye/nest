<?php
use Nest\Params\Params;
use Nest\Users\UserActions;
require_once '../includes/util.php';

$params = Params::getRequestParams('update_profile');
doValidateApiParams($params);

/*
to discuss update of email/phone with Chris
*/
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