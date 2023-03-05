<?php
use Nest\Params\Params;
use Nest\Savings\Savings;
require_once '../includes/util.php';


$savingsId = '';
if(strpos($currentPathBase, 'savings') == false)
{
    $savingsId = $currentPathBase;
}
Savings::getSavings($savingsId);
