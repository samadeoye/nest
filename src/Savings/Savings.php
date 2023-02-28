<?php
namespace Nest\Savings;

use Nest\Crud\CrudActions;
use Nest\Duplicates;

class Savings {
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
                getJsonRow(false, "Duplicate group name found! You already have a savings with this name.");
            }
           
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
                $savingsAmount = $data['amount'];
                $fundingSourceId = $data['funding_source_type_id'];
                $cdate = $tdate = time();

                $insert = CrudActions::insert(
                    DEF_TBL_SAVINGS_TRANS,
                    [
                        'id' => getNewId(),
                        'user_id' => $userId,
                        'parent_id' => $savingsId,
                        'amount' => $savingsAmount,
                        'funding_source_type_id' => $fundingSourceId,
                        'cdate' => $cdate
                        //to add tdate when when making payment
                    ]
                );
                if($insert)
                {
                    $db->commit();
                    getJsonRow(true, "Savings created successfully.");

                    //INITIALIZE SAVINGS TRANSACTION
                    if($fundingSourceId == DEF_SAVINGS_FUNDING_SOURCE_WALLET)
                    {
                        //DEDUCT FROM USER WALLET
                    }
                    if($fundingSourceId == DEF_SAVINGS_FUNDING_SOURCE_CARD)
                    {
                        //INITIALIZE PAYMENT GATEWAY
                    }
                    //getJsonRow(true, "Savings created successfully.");
                }
                else
                {
                    $db->rollBack();
                    getJsonRow(false, "An error occurred. Try again.");
                }
                
            }
            getJsonRow(false, "An error occurred. Try again.");
        }
    }

    public static function updateGroup($data)
    {
        global $userId;

        if(count($data) > 0)
        {
            if(!self::checkIfIsGroupOwner($data['group_id'], $userId))
            {
                getJsonRow(false, "You cannot update this group as you are not the owner.");
            }
            $check = CrudActions::select(
                DEF_TBL_SAVINGS_GROUPS,
                [
                    'columns' => 'id',
                    'where' => ['name' => $data['name']]
                ]
            );
            if(count($check) > 0)
            {
                //check if name is for the same group
                if($check['id'] != $data['group_id'])
                {
                    getJsonRow(false, "Duplicate group name found!");
                }
            }
            $groupId = $data['group_id'];
            $data['name'] = strtoupper($data['name']);
            $data['mdate'] = time();
            unset($data['group_id']);
            $create = CrudActions::update(
                DEF_TBL_SAVINGS_GROUPS,
                $data,
                ['id' => $groupId]
            );
            if($create)
            {
                getJsonRow(true, "Group updated successfully.");
            }
            getJsonRow(false, "An error occurred. Try again.");
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
                    $durationAppend = (typeCastInt($select['plan']) > 1) ? "s" : "";
                    $datax = [
                        'status' => true,
                        'data' => [
                            'id' => $id,
                            'name' => $name,
                            'plan' => typeCastDouble($select['plan']).'/'.getTypeFromTypeId('savings_plan', $select['plan_type_id']),
                            'duration' => typeCastInt($select['duration']).' '.getTypeFromTypeId('savings_duration', $select['duration_type_id']).$durationAppend,
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