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
    $params = Params::getRequestParams('create_regular_savings');
}
elseif($action == 'update')
{
    $params = Params::getRequestParams('update_regular_savings');
}
doValidateApiParams($params);

if(in_array($action, ['create', 'update']))
{
    $name = strtoupper(trim($_POST['name']));
    $planTypeId = trim($_POST['plan_type_id']);
    $duration = trim($_POST['duration']);
    $durationTypeId = trim($_POST['duration_type_id']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : "";
    $fundingSourceId = trim($_REQUEST['funding_source_type_id']);
    $savedCardId = isset($_REQUEST['saved_card_id']) ? trim($_REQUEST['saved_card_id']) : "";

    if($action == 'create')
    {
        $amount = doTypeCastDouble($_POST['starting_amount']);
        $payFirst = doTypeCastInt($_REQUEST['pay_first']);
    }

    /*
        <--- SAVINGS TYPE ID --->
        1: regular
        2: target
        3: vault
        4: flex
    */

    $data = [
        'type_id' => DEF_SAVINGS_TYPE_REGULAR,
        'name' => $name,
        'plan_type_id' => $planTypeId,
        'duration' => $duration,
        'duration_type_id' => $durationTypeId,
        'funding_source_type_id' => $fundingSourceId,
        'saved_card_id' => $savedCardId,
        'description' => $description
    ];
}

if($action == 'create')
{
    Savings::createSavings($data);
}
elseif($action == 'update')
{
    $savingsId = trim($_POST['savings_id']);
    $data['savings_id'] = $savingsId;

    Savings::updateSavings($data);
}