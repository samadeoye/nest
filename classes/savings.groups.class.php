<?php
class SavingsGroup {
    public static function createGroup($data)
    {
        if(count($data) > 0)
        {
            $datax = [
                'where' => ['name' => $data['group_name']]
            ];
            if(Duplicates::checkDuplicates(DEF_TBL_SAVINGS_GROUPS, $datax))
            {
                getJsonRow(false, "Duplicate group name found!");
            }

            global $db;
           
            $db->beginTransaction();

            $create = CrudActions::insert(
                DEF_TBL_SAVINGS_GROUPS,
                [
                    'user_id' => $data['user_id'],
                    'name' => strtoupper($data['group_name']),
                    'type_id' => $data['type_id'],
                    'cdate' => time()
                ]
            );
            if($create)
            {
                $groupId = $db->lastInsertId();
                $insert = CrudActions::insert(
                    DEF_TBL_SAVINGS_GROUPS_USERS,
                    [
                        'user_id' => $data['user_id'],
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
        if(count($data) > 0)
        {
            if(!self::checkIfIsGroupOwner($data['group_id'], $data['user_id']))
            {
                getJsonRow(false, "You cannot update this group as you are not the owner.");
            }
            $check = CrudActions::select(
                DEF_TBL_SAVINGS_GROUPS,
                [
                    'columns' => 'group_id',
                    'where' => ['name' => $data['group_name']]
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
            $create = CrudActions::update(
                DEF_TBL_SAVINGS_GROUPS,
                [
                    'name' => strtoupper($data['group_name']),
                    'type_id' => $data['type_id'],
                    'mdate' => time()
                ],
                ['id' => $data['group_id']]
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
        if(count($data) > 0)
        {
            if(!self::checkIfGroupExists($data['group_id']))
            {
                getJsonRow(false, "Invalid group!");
            }
            if(!self::checkIfIsGroupOwner($data['group_id'], $data['user_id']))
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

    public function checkIfGroupExists($groupId)
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

    public function checkIfIsGroupOwner($groupId, $userId)
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
}