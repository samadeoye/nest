<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

require_once 'constants.php';
require_once 'classes/functions.class.php';
require_once 'header.auth.php';
require_once 'db.php';
require_once 'classes/crud.class.php';
require_once 'classes/params.class.php';