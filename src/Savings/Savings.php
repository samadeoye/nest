<?php
namespace Nest\Savings;

use Nest\Crud\CrudActions;
use Nest\Users\UserActions;
use Nest\Duplicates;

class Savings {
    public static $table = DEF_TBL_SAVINGS;
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
                $create = CrudActions::insert(
                    DEF_TBL_SAVINGS,
                    $data
                );
                if($create)
                {
                    $savingsAmount = doTypeCastDouble($data['amount']);
                    $fundingSourceId = $data['funding_source_type_id'];

                    $insert = CrudActions::insert(
                        DEF_TBL_SAVINGS_TRANS,
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
                        getJsonRow(false, "An error occurred. Try again.");
                    }

                    //initialize savings transaction
                    $payFirst = $data['pay_first'];
                    if($payFirst == 1)
                    {
                        //check if user has enough balance
                        $balance = doTypeCastDouble(UserActions::getUserInfo('balance'));
                        if($balance == 0 || $balance < $savingsAmount)
                        {
                            getJsonRow(false, "You do not have enough balance for this transaction.");
                        }
                        if($fundingSourceId == DEF_SAVINGS_FUNDING_SOURCE_WALLET)
                        {
                            $processDone = false;
                            //deduct from user wallet
                            $newBalance = doTypeCastDouble($balance - $savingsAmount);
                            CrudActions::update(
                                DEF_TBL_USERS,
                                ['balance' => $newBalance]
                            );
                            $updateTrans = CrudActions::update(
                                DEF_TBL_SAVINGS_TRANS,
                                [
                                    'status' => 1,
                                    'tdate' => time()
                                ]
                            );
                            if($updateTrans)
                            {
                                $processDone = true;
                            }
                        }
                        if($fundingSourceId == DEF_SAVINGS_FUNDING_SOURCE_CARD)
                        {
                            //initialize payment gateway
                        }
                    }
                    if($processDone)
                    {
                        $db->commit();

                        $msg = "Savings created successfully.";
                        if($payFirst == 1)
                        {
                            $msg = "Transaction completed successfully.";
                        }
                        getJsonRow(true, $msg);
                    }
                }
                getJsonRow(false, "An error occurred. Try again.");

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

    public static function getGroup($data)
    {
        if(count($data) > 0)
        {
            global $userId;

            $groupId = $data['group_id'];

            if(!self::checkIfGroupExists($groupId))
            {
                getJsonRow(false, "Invalid group!");
            }
            if(!self::checkIfIsGroupMember($groupId, $userId))
            {
                getJsonRow(false, "You are not a member of this group.");
            }
            if(count($data) > 0)
            {
                $select = CrudActions::select(
                    DEF_TBL_SAVINGS_GROUPS,
                    [
                        'columns' => 'id, name, plan, plan_type_id, duration, duration_type_id, description',
                        'where' => [
                            'id' => $groupId
                        ]
                    ]
                );
                if(count($select) > 0)
                {
                    $id = $select['id'];
                    $name = $select['name'];
                    $durationAppend = (doTypeCastInt($select['plan']) > 1) ? "s" : "";
                    $datax = [
                        'status' => true,
                        'data' => [
                            'id' => $id,
                            'name' => $name,
                            'plan' => doNumberFormat($select['plan']).'/'.getTypeFromTypeId('savings_plan', $select['plan_type_id']),
                            'duration' => doTypeCastInt($select['duration']).' '.getTypeFromTypeId('savings_duration', $select['duration_type_id']).$durationAppend,
                            'memebers' => self::getGroupMemebersCount($select['id']),
                            'is_member' => self::checkIfIsGroupMember($groupId, $userId),
                            'description' => $select['description'],
                            /*
                                To create a redirection from the endpoint below to the api savings_group endpoint for people using the link to join
                            */
                            'link' => SITE_LINK.'/join_group?id='.$id
                        ]
                    ];
                    getJsonList($datax);
                }
                getJsonRow(false, "An error occurred");
            }
            getJsonRow(false, "No record found.");
        }
    }

    public static function checkIfGroupExists($groupId)
    {
        $select = CrudActions::select(
            DEF_TBL_SAVINGS_GROUPS,
            [
                'columns' => 'id',
                'where' => ['id' => $groupId]
            ]
        );
        if(count($select) > 0)
        {
            return true;
        }
        return false;
    }

    public static function checkIfIsGroupOwner($groupId, $userId)
    {
        $select = CrudActions::select(
            DEF_TBL_SAVINGS_GROUPS,
            [
                'columns' => 'id',
                'where' => [
                    'id' => $groupId,
                    'user_id' => $userId
                ]
            ]
        );
        if(count($select) > 0)
        {
            return true;
        }
        return false;
    }

    public static function doGetSavingsRecord($recordId, $arFields=['*'])
    {
        global $userId;

        $fields = implode(',', $arFields);

        $rs = CrudActions::select(
            DEF_TBL_SAVINGS_TRANS,
            [
                'columns' => $fields,
                'where' => ['id' => $recordId]
            ]
        );
        if(count($rs) > 0)
        {
            return $rs;
        }
        return [];
    }
}