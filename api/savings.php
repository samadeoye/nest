<?php
use Nest\Params\Params;
use Nest\Savings\Savings;
require_once '../includes/util.php';


$action = isset($_POST['action']) ? trim($_POST['action']) : "";
if(!in_array($action, ['create', 'update']))
{
    getJsonRow(false, 'Invalid action!');
}

if($action == 'create')
{
    $params = Params::getRequestParams('create_savings');
}
elseif($action == 'update')
{
    $params = Params::getRequestParams('update_savings');
}
doValidateApiParams($params);

if(in_array($action, ['create', 'update']))
{
    $typeId = trim($_POST['type_id']);
    $name = strtoupper(trim($_POST['name']));
    $amount = trim($_POST['amount']);
    $planTypeId = trim($_POST['plan_type_id']);
    $duration = trim($_POST['duration']);
    $durationTypeId = trim($_POST['duration_type_id']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : "";
    $payFirst = trim($_REQUEST['pay_first']);
    $fundingSourceId = trim($_REQUEST['funding_source_type_id']);

    $data = [
        'type_id' => $typeId,
        'name' => $name,
        'amount' => $amount,
        'plan_type_id' => $planTypeId,
        'duration' => $duration,
        'duration_type_id' => $durationTypeId,
        'funding_source_type_id' => $fundingSourceId,
        'description' => $description,
        'pay_first' => $payFirst
    ];

    if($fundingSourceId == DEF_SAVINGS_FUNDING_SOURCE_CARD)
    {
        $savedCardId = isset($_REQUEST['saved_card_id']) ? trim($_REQUEST['saved_card_id']) : "";
        if(strlen($savedCardId) != 36)
        {
            getJsonRow(false, "Funding souce invalid!");
        }
        $data['saved_card_id'] = $savedCardId;
    }
}

if($action == 'create')
{
    Savings::createSavings($data);
}
elseif($action == 'update')
{
    $savingsId = trim($_POST['savings_id']);
    $data['savings_id'] = $savingsId;
    Savings::updateGroup($data);
}