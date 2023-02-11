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
                    'length' => [3,200],
                    'label' => LBL_FNAME,
                    'required' => true
                ],
                'lname' => [
                    'method' => 'post',
                    'length' => [3,200],
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