<?php
require_once '../util.php';
require_once '../classes/users.class.php';

$params = Params::getRequestParams('register');
doValidateApiParams($params);

$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];

$data = [
    'fname' => $fname,
    'lname' => $lname,
    'email' => $email,
    'phone' => $phone,
    'password' => $password
];