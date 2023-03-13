<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

$httpHost = $_SERVER['HTTP_HOST'];
$httpFolderPath = '/api';
if($httpHost == 'localhost')
{
    $httpFolderPath = '/nest';
}

define('DEF_DOC_ROOT', $_SERVER['DOCUMENT_ROOT'] . $httpFolderPath);

require_once DEF_DOC_ROOT.'/vendor/autoload.php';
require_once 'constants.php';
require_once 'functions.php';
require_once 'db.php';
require_once 'header.auth.php';

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$currentPathDir = dirname($currentPath);
$currentPathFile = basename($currentPath, '.php');
$arCurrentPath = explode('/', $currentPathDir);
if($arCurrentPath[1] == 'nest')
{
    unset($arCurrentPath[1]);
    $currentPathDir = implode('/', $arCurrentPath);
}
unset($currentPath);
unset($arCurrentPath);