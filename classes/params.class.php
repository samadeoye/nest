<?php
class Params
{
    public static function getRequestParams($request)
    {
        $data = [];
        if($request == 'register')
        {
            $data = [
                'fname' => [
                    'method' => 'post',
                    'length' => [3,100],
                    'label' => LBL_FNAME,
                    'required' => true
                ],
                'lname' => [
                    'method' => 'post',
                    'length' => [3,100],
                    'label' => LBL_LNAME,
                    'required' => true
                ],
                'phone' => [
                    'method' => 'post',
                    'length' => [11,15],
                    'label' => LBL_PHONE,
                    'required' => true
                ],
                'email' => [
                    'method' => 'post',
                    'length' => [13,200],
                    'label' => LBL_EMAIL,
                    'is_email' => true
                ],
                'password' => [
                    'method' => 'post',
                    'length' => [6,0],
                    'label' => LBL_PASSWORD,
                    'required' => true
                ],
                /*
                    <--- USER TYPE ID --->
                    Underbanked: 1
                    Corporative Society: 2
                    Digital Professional: 3
                    Businesses: 4
                */
                'type_id' => [
                    'method' => 'post',
                    'length' => [1,1],
                    'label' => LBL_USER_TYPE,
                    'required' => true
                ]
            ];
        }
        elseif($request == 'update_profile')
        {
            $data = [
                'fname' => [
                    'method' => 'post',
                    'length' => [3,100],
                    'label' => LBL_FNAME,
                    'required' => true
                ],
                'lname' => [
                    'method' => 'post',
                    'length' => [3,100],
                    'label' => LBL_LNAME,
                    'required' => true
                ],
                'phone' => [
                    'method' => 'post',
                    'length' => [11,15],
                    'label' => LBL_PHONE,
                    'required' => true
                ],
                'email' => [
                    'method' => 'post',
                    'length' => [13,200],
                    'label' => LBL_EMAIL,
                    'is_email' => true
                ],
                /*
                    <--- USER TYPE ID --->
                    Underbanked: 1
                    Corporative Society: 2
                    Digital Professional: 3
                    Businesses: 4
                */
                'type_id' => [
                    'method' => 'post',
                    'length' => [1,1],
                    'label' => LBL_USER_TYPE,
                    'required' => true
                ]
            ];
        }
        elseif($request == 'login')
        {
            $data = [
               'phone' => [
                    'method' => 'post',
                    'length' => [11,15],
                    'label' => LBL_PHONE
                ],
                'email' => [
                    'method' => 'post',
                    'length' => [13,200],
                    'label' => LBL_EMAIL,
                    'is_email' => true
                ],
                'password' => [
                    'method' => 'post',
                    'length' => [6,0],
                    'label' => LBL_PASSWORD,
                    'required' => true
                ]
            ];
        }
        elseif($request == 'logout')
        {
            $data = [
               'user_id' => [
                    'method' => 'post',
                    'length' => [36,36],
                    'label' => LBL_USER,
                    'required' => true
                ],
                'token' => [
                    'method' => 'post',
                    'length' => [10,10],
                    'label' => LBL_TOKEN,
                    'required' => true
                ]
            ];
        }
        elseif($request == 'create_savings_group')
        {
            $data = [
               'user_id' => [
                    'method' => 'post',
                    'length' => [36,36],
                    'label' => LBL_USER,
                    'required' => true
                ],
                'group_name' => [
                    'method' => 'post',
                    'length' => [5,200],
                    'label' => LBL_GROUP_NAME,
                    'required' => true
                ],
                /*
                    <--- SAVINGS GROUP TYPE ID --->
                    Private: 1
                    Public: 2
                */
                'type_id' => [
                    'method' => 'post',
                    'length' => [1,1],
                    'label' => LBL_SAVINGS_GROUP_TYPE,
                    'type' => 'number',
                    'required' => true
                ],
                'action' => [
                    'method' => 'post',
                    'label' => LBL_ACTION,
                    'required' => true
                ]
            ];
        }
        elseif($request == 'update_savings_group')
        {
            $data = [
               'user_id' => [
                    'method' => 'post',
                    'length' => [36,36],
                    'label' => LBL_USER,
                    'required' => true
                ],
                'group_id' => [
                    'method' => 'post',
                    'length' => [1,0],
                    'label' => LBL_GROUP,
                    'required' => true
                ],
                'group_name' => [
                    'method' => 'post',
                    'length' => [5,200],
                    'label' => LBL_GROUP_NAME,
                    'required' => true
                ],
                /*
                    <--- SAVINGS GROUP TYPE ID --->
                    Private: 1
                    Public: 2
                */
                'type_id' => [
                    'method' => 'post',
                    'length' => [1,1],
                    'label' => LBL_SAVINGS_GROUP_TYPE,
                    'type' => 'number',
                    'required' => true
                ],
                'action' => [
                    'method' => 'post',
                    'label' => LBL_ACTION,
                    'required' => true
                ]
            ];
        }
        elseif($request == 'search_savings_group')
        {
            $data = [
               'keyword' => [
                    'method' => 'post',
                    'length' => [1,0],
                    'label' => LBL_KEYWORD,
                    'required' => true
                ]
            ];
        }
        elseif($request == 'join_savings_group')
        {
            $data = [
               'user_id' => [
                    'method' => 'post',
                    'length' => [36,36],
                    'label' => LBL_USER,
                    'required' => true
                ],
                'group_id' => [
                    'method' => 'post',
                    'length' => [1,0],
                    'label' => LBL_GROUP,
                    'required' => true
                ],
                'action' => [
                    'method' => 'post',
                    'label' => LBL_ACTION,
                    'required' => true
                ]
            ];
        }
        
        return $data;
    }
}