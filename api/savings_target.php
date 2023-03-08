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
    $params = Params::getRequestParams('create_target_savings');
}
elseif($action == 'update')
{
    $params = Params::getRequestParams('update_target_savings');
}
doValidateApiParams($params);

if(in_array($action, ['create', 'update']))
{
    $name = strtoupper(trim($_POST['name']));
    $description = trim($_POST['description']);

    /*
        <--- SAVINGS TYPE ID --->
        1: regular
        2: target
        3: vault
        4: flex
    */

    if($action == 'create')
    {
        $planTypeId = trim($_POST['plan_type_id']);
        $duration = trim($_POST['duration']);
        $durationTypeId = trim($_POST['duration_type_id']);
        $fundingSourceId = trim($_REQUEST['funding_source_type_id']);
        $savedCardId = isset($_REQUEST['saved_card_id']) ? trim($_REQUEST['saved_card_id']) : "";
        $amount = doTypeCastDouble($_POST['target_amount']);
        $planAmount = doTypeCastDouble($_POST['plan_amount']);
        $startDate = isset($_REQUEST['start_date']) ? trim($_REQUEST['start_date']) : "";
        $endDate = isset($_REQUEST['end_date']) ? trim($_REQUEST['end_date']) : "";

        $data = [
            'type_id' => DEF_SAVINGS_TYPE_TARGET,
            'name' => $name,
            'amount' => $amount,
            'plan_amount' => $planAmount,
            'plan_type_id' => $planTypeId,
            'duration' => $duration,
            'duration_type_id' => $durationTypeId,
            'funding_source_type_id' => $fundingSourceId,
            'saved_card_id' => $savedCardId,
            'description' => $description,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        Savings::createSavings($data);
    }
    
    elseif($action == 'update')
    {
        $savingsId = trim($_POST['savings_id']);

        $data = [
            'savings_id' => $savingsId,
            'name' => $name,
            'description' => $description
        ];
    
        Savings::updateSavings($data);
    }
}
