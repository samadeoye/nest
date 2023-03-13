<?php
use Nest\Params\Params;
use Nest\Savings\Savings;

$params = Params::getRequestParams('get_savings_transactions');
doValidateApiParams($params);

$savingsId = trim($_GET['savings_id']);
Savings::getSavingsTransactions($savingsId);
