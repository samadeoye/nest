<?php
use Nest\Params\Params;
use Nest\Savings\Savings;


$savingsId = '';
if(strpos($currentPathBase, 'savings') == false)
{
    $savingsId = $currentPathBase;
}
Savings::getSavings($savingsId);
