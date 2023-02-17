<?php
class SavingsGroup {
    public static function createGroup($data)
    {
        if(count($data) > 0)
        {
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
                getJsonRow(true, "Group created successfully.");
            }
            getJsonRow(false, "An error occurred. Try again.");
        }
    }

    public static function updateGroup($data)
    {
        if(count($data) > 0)
        {
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
}