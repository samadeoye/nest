<?php
use Nest\Params\Params;
use Nest\Savings\Savings;
require_once '../includes/util.php';

$params = Params::getRequestParams('savings_topup');
doValidateApiParams($params);

$savingsId = trim($_POST['savings_id']);
$amount = doTypeCastDouble($_POST['amount']);
$fundingSourceId = trim($_REQUEST['funding_source_type_id']);
$savedCardId = isset($_REQUEST['saved_card_id']) ? trim($_REQUEST['saved_card_id']) : "";

$data = [
    'savings_id' => $savingsId,
    'amount' => $amount,
    'funding_source_type_id' => $fundingSourceId,
    'saved_card_id' => $savedCardId
];
Savings::doSaveAmount($data);

