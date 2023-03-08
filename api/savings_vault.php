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
    $params = Params::getRequestParams('create_vault_savings');
}
elseif($action == 'update')
{
    $params = Params::getRequestParams('update_vault_savings');
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
        $vaultTypeId = trim($_POST['vault_type_id']);
        $fundingSourceId = trim($_REQUEST['funding_source_type_id']);
        $savedCardId = isset($_REQUEST['saved_card_id']) ? trim($_REQUEST['saved_card_id']) : "";
        $amount = doTypeCastDouble($_POST['vault_amount']);
        $planAmount = doTypeCastDouble($_POST['plan_amount']);
        $payoutDate = isset($_REQUEST['payout_date']) ? trim($_REQUEST['payout_date']) : "";

        $data = [
            'type_id' => DEF_SAVINGS_TYPE_VAULT,
            'vault_type_id' => $vaultTypeId,
            'name' => $name,
            'description' => $description,
            'amount' => $amount,
            'plan_amount' => $planAmount,
            'funding_source_type_id' => $fundingSourceId,
            'saved_card_id' => $savedCardId,
            'payout_date' => $payoutDate
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
