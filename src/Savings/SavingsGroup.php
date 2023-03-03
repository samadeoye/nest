<?php
namespace Nest\Savings;

use Nest\Crud\CrudActions;
use Nest\Duplicates;

class SavingsGroup {
    public static function createGroup($data)
    {
        global $userId;

        if(count($data) > 0)
        {
            $datax = [
                'where' => ['name' => $data['name']]
            ];
            if(Duplicates::checkDuplicates(DEF_TBL_SAVINGS_GROUPS, $datax))
            {
                getJsonRow(false, "Duplicate group name found!");
            }

            global $db;
           
            $db->beginTransaction();

            $data['id'] = getNewId();
            $data['cdate'] = time();
            $create = CrudActions::insert(
                DEF_TBL_SAVINGS_GROUPS,
                $data
            );
            if($create)
            {
                $groupId = $db->lastInsertId();
                $insert = CrudActions::insert(
                    DEF_TBL_SAVINGS_GROUPS_USERS,
                    [
                        'id' => getNewId(),
                        'user_id' => $userId,
                        'parent_id' => $groupId,
                        'cdate' => time()
                    ]
                );
                if($insert)
                {
                    $db->commit();
                    getJsonRow(true, "Group created successfully.");
                }
                $db->rollBack();
                getJsonRow(false, "An error occurred. Try again.");
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

    public static function searchGroup($keyword)
    {
        if(strlen($keyword) > 0)
        {
            $search = CrudActions::select(
                DEF_TBL_SAVINGS_GROUPS,
                [
                    'columns' => 'id, name',
                    'where' => ['type_id' => DEF_SAVINGS_GROUP_TYPE_PUBLIC],
                    'search' => [
                        'name' => '%'.strtoupper($keyword).'%'
                    ],
                    'return_type' => 'all'
                ]
            );
            if(count($search) > 0)
            {
                $data = ['status' => true];
                foreach($search as $r)
                {
                    $data['data'][] = $r;
                }
                getJsonList($data);
            }
            getJsonRow(false, "No record found.");
        }
        getJsonRow(false, "Search keyword is required!");
    }

    public static function joinGroup($data)
    {
        global $userId;

        if(count($data) > 0)
        {
            if(!self::checkIfGroupExists($data['group_id']))
            {
                getJsonRow(false, "Invalid group!");
            }
            if(!self::checkIfIsGroupOwner($data['group_id'], $userId))
            {
                getJsonRow(false, "You are already added to this group as you are the owner.");
            }
            if(count($data) > 0)
            {
                $insert = CrudActions::insert(
                    DEF_TBL_SAVINGS_GROUPS_USERS,
                    [
                        'user_id' => $data['user_id'],
                        'group_id' => $data['group_id'],
                        'cdate' => time()
                    ]
                );
                if($insert)
                {
                    getJsonRow(true, "You have been successfully added to the savings group.");
                }
                getJsonRow(false, "An error occurred");
            }
            getJsonRow(false, "No record found.");
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

    public static function listGroup()
    {   
        $select = CrudActions::select(
            DEF_TBL_SAVINGS_GROUPS,
            [
                'columns' => 'id, name, plan, plan_type_id, duration, duration_type_id, description',
                'return_type' => 'all'
            ]
        );
        if(count($select) > 0)
        {
            $datax = [
                'status' => true,
                'data' => []
            ];
            foreach($select as $r)
            {
                $durationAppend = (doTypeCastInt($r['plan']) > 1) ? "s" : "";
                $datax['data'][] = [
                    'id' => $r['id'],
                    'name' => $r['name'],
                    'plan' => doNumberFormat($r['plan']).'/'.getTypeFromTypeId('savings_plan', $r['plan_type_id']),
                    'duration' => doTypeCastInt($r['duration']).' '.getTypeFromTypeId('savings_duration', $r['duration_type_id']).$durationAppend,
                    'memebers' => self::getGroupMemebersCount($r['id']),
                    'description' => $r['description'],
                    /*
                        To create a redirection from the endpoint below to the api savings_group endpoint for people using the link to join
                    */
                    'invite_link' => SITE_LINK.'/join_group?id='.$r['id']
                ];
            }
            getJsonList($datax);
        }
        getJsonRow(false, "No record found.");
    }

    public static function getGroupMemebersCount($groupId)
    {
        if(!empty($groupId))
        {
            $count = CrudActions::select(
                DEF_TBL_SAVINGS_GROUPS_USERS,
                [
                    'columns' => 'COUNT(id) AS count',
                    'where' => ['parent_id' => $groupId]
                ]
            );
            return doTypeCastInt($count['count']);
        }
        return 0;
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
    public static function checkIfIsGroupMember($groupId, $userId)
    {
        if(!empty($groupId) && strlen($userId) == 36)
        {
            $select = CrudActions::select(
                DEF_TBL_SAVINGS_GROUPS_USERS,
                [
                    'columns' => 'id',
                    'where' => [
                        'parent_id' => $groupId,
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
        return false;
    }
}