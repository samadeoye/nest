<?php
require_once 'includes/util.php';
use Nest\Router\ApiRouter;

$objRoute = new ApiRouter($currentPathDir, $currentPathFile);
$objRoute = $objRoute->doRequestProcess();
if($objRoute != '')
{
    require_once $objRoute;
}
getJsonRow(false, 'Invalid request!');
