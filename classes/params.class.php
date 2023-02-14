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
                ]
            ];
        }
        
        return $data;
    }
}