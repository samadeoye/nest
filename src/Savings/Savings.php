<?php
namespace Nest\Savings;

use Nest\Crud\CrudActions;
use Nest\Users\UserActions;
use Nest\Duplicates;

class Savings {
    public static $table = DEF_TBL_SAVINGS;
    public static $tableTrans = DEF_TBL_SAVINGS_TRANS;
    public static function createSavings($data)
    {
        global $db, $userId;

        if(count($data) > 0)
        {
            $datax = [
                'where' => [
                    'name' => $data['name'],
                    'user_id' => $userId,
                    'deleted' => 0
                ]
            ];
            if(Duplicates::checkDuplicates(DEF_TBL_SAVINGS, $datax))
            {
                getJsonRow(false, "Duplicate savings name found! You already have a savings with this name.");
            }

            try {
                $db->beginTransaction();

                $savingsId = getNewId();
                $data['id'] = $savingsId;
                $data['cdate'] = time();
                $data['user_id'] = $userId;
                if(array_key_exists('start_date', $data))
                {
                    $data['start_date'] = strtotime($data['start_date']);
                }
                if(array_key_exists('end_date', $data))
                {
                    $data['end_date'] = strtotime($data['end_date']);
                }
                if(array_key_exists('payout_date', $data))
                {
                    $data['payout_date'] = strtotime($data['payout_date']);
                }
                
                $create = CrudActions::insert(
                    DEF_TBL_SAVINGS,
                    $data
                );
                if($create)
                {
                    $savingsType = $data['type_id'];
                    $processDone = true;

                    //initialize savings transaction for regular savings only
                    if($savingsType == DEF_SAVINGS_TYPE_REGULAR)
                    {
                        $savingsAmount = doTypeCastDouble($data['amount']);
                        $fundingSourceId = $data['funding_source_type_id'];

                        $insert = CrudActions::insert(
                            self::$tableTrans,
                            [
                                'id' => getNewId(),
                                'user_id' => $userId,
                                'parent_id' => $savingsId,
                                'amount' => $savingsAmount,
                                'funding_source_type_id' => $fundingSourceId,
                                'cdate' => time()
                                //to add tdate when when making payment
                            ]
                        );
                        if(!$insert)
                        {
                            $processDone = false;
                            getJsonRow(false, "An error occurred while processing your first transaction savings. Try again.");
                        }
                        //check if user has enough balance
                        $balance = doTypeCastDouble(UserActions::getUserInfo('balance'));
                        if($balance == 0 || $balance < $savingsAmount)
                        {
                            $processDone = false;
                            getJsonRow(false, "You do not have enough balance for this transaction.");
                        }
                        if($fundingSourceId == DEF_SAVINGS_FUNDING_SOURCE_WALLET)
                        {
                            //deduct from user wallet
                            $newBalance = doTypeCastDouble($balance - $savingsAmount);
                            CrudActions::update(
                                DEF_TBL_USERS,
                                ['balance' => $newBalance]
                            );
                            $updateTrans = CrudActions::update(
                                self::$tableTrans,
                                [
                                    'status' => 1,
                                    'tdate' => time()
                                ]
                            );
                            if(!$updateTrans)
                            {
                                $processDone = false;
                            }
                        }
                        elseif($fundingSourceId == DEF_SAVINGS_FUNDING_SOURCE_CARD)
                        {
                            //initialize payment gateway
                        }
                    }

                    if($processDone)
                    {
                        $db->commit();

                        $msg = "Savings created successfully.";
                        if($savingsType == DEF_SAVINGS_TYPE_REGULAR)
                        {
                            $msg = "Savings created! First transaction completed successfully.";
                        }
                        getJsonRow(true, $msg);
                    }
                }
                getJsonRow(false, "An error occurred while creating your savings. Try again.");

            }
            catch (\Exception $e)
            {
                $db->rollback();
                getJsonRow(false, "An error occurred. Try again.");
            }
           
        }
    }

    public static function updateSavings($data)
    {
        global $userId;

        if(count($data) > 0)
        {
            $savingsId = $data['savings_id'];
            $check = CrudActions::select(
                self::$table,
                [
                    'columns' => 'id',
                    'where' => [
                        'name' => $data['name'],
                        'user_id' => $userId,
                        'deleted' => 0
                    ]
                ]
            );
            
            if(count($check) > 0)
            {
                //check if name is for the same user
                if($check['id'] != $savingsId)
                {
                    getJsonRow(false, "Duplicate savings name found!");
                }
            }
            unset($data['savings_id']);
            CrudActions::update(
                self::$table,
                $data,
                ['id' => $savingsId]
            );
            getJsonRow(true, "Savings updated successfully.");
           
        }
    }

    public static function getSavings($recordId='')
    {
        $rs = self::doGetSavingsRecord($recordId, ['*']);
        if(count($rs) > 0)
        {
            $data = ['status' => true, 'data' => []];

            $counter = 0;
            foreach($rs as $r)
            {
                $durationAppend = doTypeCastInt($r['duration']) > 1 ? "s" : "";
                $savingsTypeId = $r['type_id'];
                $savingsId = $r['id'];
                $amount = $r['amount'];
                $totalAmountSaved = self::doGetTotalAmountSaved($savingsId);

                $data['data'][$counter] = [
                    'id' => $savingsId,
                    'name' => $r['name'],
                    'description' => $r['description'],
                    'savings_type' => getTypeFromTypeId('savings_type', $savingsTypeId),
                ];
                if($savingsTypeId == DEF_SAVINGS_TYPE_REGULAR)
                {
                    $data['data'][$counter]['starting_amount'] = 'N' . doNumberFormat($amount);
                    $data['data'][$counter]['duration'] = doTypeCastInt($r['duration']).' '.getTypeFromTypeId('savings_duration', $r['duration_type_id']).$durationAppend;
                    $data['data'][$counter]['plan'] = getTypeFromTypeId('savings_plan', $r['plan_type_id']);
                }
                elseif(in_array($savingsTypeId, [DEF_SAVINGS_TYPE_TARGET, DEF_SAVINGS_TYPE_VAULT]))
                {
                    $data['data'][$counter]['amount'] = doNumberFormat($amount);
                    if($savingsTypeId == DEF_SAVINGS_TYPE_TARGET)
                    {
                        $data['data'][$counter]['plan'] = getTypeFromTypeId('savings_plan', $r['plan_type_id']);
                        $data['data'][$counter]['start_date'] = $r['start_date'];
                        $data['data'][$counter]['end_date'] = $r['end_date'];
                        $data['data'][$counter]['duration'] = doTypeCastInt($r['duration']).' '.getTypeFromTypeId('savings_duration', $r['duration_type_id']).$durationAppend;
                    }
                    if($savingsTypeId == DEF_SAVINGS_TYPE_VAULT)
                    {
                        $data['data'][$counter]['payout_date'] = $r['payout_date'];
                    }
                }
                
                $data['data'][$counter]['amount_saved'] = doNumberFormat($totalAmountSaved);
                $data['data'][$counter]['date_created'] = getDateFormat($r['cdate']);
                $data['data'][$counter]['date_modified'] = getDateFormat($r['mdate']);

                $counter++;
            }
            getJsonList($data);
        }
        getJsonRow(false, "No record found.");

    }

    public static function getSavingsTransactions($recordId)
    {
        global $userId;

        $rs = CrudActions::select(
            self::$tableTrans,
            [
                'columns' => 'id, amount, debitcredit',
                'where' => [
                    'parent_id' => $recordId,
                    'user_id' => $userId,
                    'status' => 1,
                    'deleted' => 0
                ],
                'return_type' => 'all'
            ]
        );
        if(sizeof($rs) > 0)
        {
            $data = ['status' => true, 'data' => []];
            foreach($rs as $r)
            {
                $data['data'] = [
                    'amount' => 'N' . doNumberFormat($r['amount']),
                    'type' => doTypeCastInt($r['debitcredit']) == 1 ? "debit" : "credit",
                    'description' => doTypeCastInt($r['debitcredit']) == 1 ? "Savings Topup" : "Paid to Wallet"
                ];
            }
            getJsonList($data);
        }
        return [];
    }

    public static function doGetSavingsRecord($recordId='', $arFields=['*'])
    {
        global $userId;

        $fields = is_array($arFields) ? implode(',', $arFields) : $arFields;

        $whereAppend = [];
        if(strlen($recordId) == 36)
        {
            $whereAppend = ['id' => $recordId];
        }
        $whereAppend['user_id'] = $userId;
        $whereAppend['deleted'] = 0;

        $rs = CrudActions::select(
            DEF_TBL_SAVINGS,
            [
                'columns' => $fields,
                'where' => $whereAppend,
                'return_type' => 'all'
            ]
        );
        if(count($rs) > 0)
        {
            return $rs;
        }
        return [];
    }


    public static function doCheckIfSavingsExists($recordId)
    {
        global $userId;

        if(strlen($recordId) == 36)
        {
            $rs = CrudActions::select(
                self::$table,
                [
                    'columns' => 'id, deleted',
                    [
                        'where' => [
                            'id' => $recordId,
                            'user_id' => $userId,
                            'deleted' => 0
                        ]
                    ]
                ]
            );
            if(count($rs) > 0)
            {
                return true;
            }
            return false;
        }
        return false;
    }
    public static function getVaultTypes()
    {
        $rs = CrudActions::select(
            DEF_TBL_SAVINGS_VAULTS,
            [
                'columns' => 'id, name, interest',
                'where' => [
                    'status' => 1,
                    'deleted' => 0
                ],
                'return_type' => 'all'
            ]
        );
        if(sizeof($rs) > 0)
        {
            $data = [
                'status' => true
            ];
            foreach($rs as $r)
            {
                $data['data'][] = [
                    'title' => $r['name'],
                    'description' => 'earn up to '. doTypeCastInt($r['interest']) . '% interest'
                ];
            }
            getJsonList($data);
        }
        getJsonRow(false, "Data could not be fetched.");
    }

    public static function doGetTotalAmountSaved($recordId)
    {
        global $userId;

        $rs = CrudActions::select(
            self::$tableTrans,
            [
                'columns' => 'amount',
                'where' => [
                    'parent_id' => $recordId,
                    'user_id' => $userId,
                    'status' => 1,
                    'deleted' => 0
                ],
                'return_type' => 'all'
            ]
        );
        if(sizeof($rs) > 0)
        {
            $totalAmount = 0;
            foreach($rs as $r)
            {
                $totalAmount += $r['amount'];
            }
            return doTypeCastDouble($totalAmount);
        }
        return 0;
    }

    public static function doSaveAmount($data)
    {
        global $userId;

        if(count($data) > 0)
        {
            $savingsId = $data['savings_id'];
            $amountToSave = $data['amount'];
            $fundingSourceId = $data['funding_source_type_id'];
            $savedCardId = $data['saved_card_id'];

            if(strlen($savingsId) == 36 && $amountToSave > 0)
            {
                if(self::doCheckIfSavingsExists($savingsId))
                {
                    $save = CrudActions::insert(
                        self::$tableTrans,
                        [
                            'id' => getNewId(),
                            'user_id' => $userId,
                            'parent_id' => $savingsId,
                            'amount' => $amountToSave,
                            'funding_source_type_id' => $fundingSourceId,
                            'cdate' => time()
                            //to add tdate when when making payment
                        ]
                    );
                    if(!$save)
                    {
                        getJsonRow(false, "An error occured while processing your savings.");
                    }
                    $processDone = false;
                    //check if user has enough balance
                    $balance = doTypeCastDouble(UserActions::getUserInfo('balance'));
                    if($balance == 0 || $balance < $amountToSave)
                    {
                        $processDone = false;
                        getJsonRow(false, "You do not have enough balance for this transaction.");
                    }
                    if($fundingSourceId == DEF_SAVINGS_FUNDING_SOURCE_WALLET)
                    {
                        //deduct from user wallet
                        $newBalance = doTypeCastDouble($balance - $amountToSave);
                        CrudActions::update(
                            DEF_TBL_USERS,
                            ['balance' => $newBalance]
                        );
                        $updateTrans = CrudActions::update(
                            self::$tableTrans,
                            [
                                'status' => 1,
                                'tdate' => time()
                            ]
                        );
                        if(!$updateTrans)
                        {
                            $processDone = false;
                        }
                    }
                    elseif($fundingSourceId == DEF_SAVINGS_FUNDING_SOURCE_CARD)
                    {
                        //initialize payment gateway
                    }

                    if($processDone)
                    {
                        getJsonRow(true, "Savings transaction completed successfully.");
                    }
                    getJsonRow(false, "An error occured while processing your transaction.");
                }
                getJsonRow(false, "Savings not found!");
            }
            getJsonRow(false, "Savings not found!");
        }
        getJsonRow(false, "Invalid request!");
    }
}