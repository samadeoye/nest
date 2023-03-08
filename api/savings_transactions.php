<?php
use Nest\Params\Params;
use Nest\Savings\Savings;
require_once '../includes/util.php';

$params = Params::getRequestParams('get_savings_transactions');
doValidateApiParams($params);

$savingsId = trim($_GET['savings_id']);
Savings::getSavingsTransactions($savingsId);
