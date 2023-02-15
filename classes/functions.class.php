<?php
function getFourDigitPin()
{
    $pin = rand(1231,7879);
    return $pin;
}

function getJsonRow($status, $msg)
{
    $response['status'] = $status;
    $response['msg'] = $msg;
    getJsonList($response);
}
function getJsonList($row)
{
    if(count($row) > 0)
    {
        echo json_encode($row, JSON_PRETTY_PRINT);
    }
    exit;
}

function issetParam($param, $method)
{
    if(strtolower($method) == 'post')
    {
        return isset($_POST[$param]);
    }
    elseif(strtolower($method) == 'get')
    {
        return isset($_GET[$param]);
    }
    return isset($_REQUEST[$param]);
}

function notEmptyParam($param, $method)
{
    if($method == 'post') {
        return !empty($_POST[$param]);
    }
    return !empty($_GET[$param]);
}

function issetNotEmpty($param, $method, $connector)
{
    return issetParam($param, $method) .$connector. notEmptyParam($param, $method);
}

function cleanme($text)
{
    $cleanit = strip_tags(trim($text));
    return $cleanit;
}

function typeCastDouble($number)
{
    return number_format($number, 2);
}
function typeCastInt($number)
{
    return intval($number);
}

function getTransactionReference($type)
{
    return 'NST-REF-'.time();
}

function getNewId()
{
    mt_srand((double)microtime()*10000);
    $charId = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);
    $id = substr($charId, 0, 8).$hyphen
    .substr($charId, 8, 4).$hyphen
    .substr($charId, 12, 4).$hyphen
    .substr($charId, 16, 4).$hyphen
    .substr($charId, 20, 12);
    return $id;
}
  
function doCheckIfEmpty($data)
{
    if(count($data) > 0)
    {
        foreach($data as $dt) {
            if(gettype($dt) == 'string')
            {
                if(strlen($dt) == 0)
                {
                    getJsonRow(false, MSG_FILL_REQUIRED_FIELDS);
                }
            }
            if(gettype($dt) == 'array')
            {
                if(count($dt) == 0)
                {
                    getJsonRow(false, MSG_FILL_REQUIRED_FIELDS);
                }
            }
        }
    }
    getJsonRow(false, MSG_FILL_REQUIRED_FIELDS);
}
function validateAndReturn($rows)
{
    if(count($rows) > 0) {
        getJsonList($rows);
    }
    getJsonRow(false, MSG_NO_RECORD_FOUND);
}
  
function doValidateApiParams($data)
{
    if(count($data) > 0)
    {
        foreach($data as $key => $val)
        {
            $validate = doCheckParamIssetEmpty($key, $val);
            if(!$validate['status'])
            {
                getJsonRow(false, $validate['msg']);
            }
        }
    }
}
function doCheckParamIssetEmpty($param, $data)
{
    $datax = [
        'status' => true,
        'msg' => ''
    ];
    
    $method = $data['method'];
    $label = $data['label'];
    $length = isset($data['length']) ? $data['length'] : [0,0];
    $required = isset($data['required']) ? $data['required'] : false;
    $type = isset($data['type']) ? $data['type'] : "";
    $isEmail = isset($data['is_email']) ? $data['is_email'] : false;

    if(empty($label))
    {
        $label = $param;
    }
    if(strtolower($method) == 'post')
    {
        $isset = isset($_POST[$param]);
        $value = isset($_POST[$param]) ? $_POST[$param] : "";
    }
    elseif(strtolower($method) == 'get')
    {
        $isset = isset($_GET[$param]);
        $value = $isset ? $_GET[$param] : "";
    }
    else
    {
        $isset = isset($_REQUEST[$param]);
        $value = $isset ? $_REQUEST[$param] : "";
    }
    
    if($required)
    {
        $isset = $isset && !empty($value);
        if(!$isset)
        {
            $datax['status'] = false;
            $datax['msg'] = $label . ' ' . MSG_IS_REQUIRED;
            return $datax;
        }
    }
    if(!empty($type) && !empty($value))
    {
        if($type == 'string')
        {
            if(!is_string($value))
            {
                $datax['status'] = false;
                $datax['msg'] = $label . ' must be a string.';
                return $datax;
            }
        }
        elseif($type == 'number')
        {
            if(!is_numeric($value))
            {
                $datax['status'] = false;
                $datax['msg'] = $label . ' must contain only digits.';
                return $datax;
            }
        }
    }
    if((!empty($value) && $isEmail) || (!empty($value) && trim($value) == 'email'))
    {
        if(!filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            $datax['status'] = false;
            $datax['msg'] = $label . ' must contain a valid email.';
            return $datax;
        }
    }
    if($length[0] > 0 && !empty($value))
    {
        $isset = $isset && strlen($value) >= $length[0];
        if(!$isset)
        {
            $datax['status'] = false;
            $datax['msg'] = $label . ' ' . MSG_MUST_BE_GREATER_THAN_EQUAL . ' ' . $length[0] .' '. strtolower(LBL_CHARACTERS);
            return $datax;
        }
    }
    if($length[1] > 0 && !empty($value))
    {
        $isset = $isset && strlen($value) <= $length[1];
        if(!$isset)
        {
            $datax['status'] = false;
            $datax['msg'] = $label . ' ' . MSG_MUST_BE_LESS_THAN_EQUAL . ' ' . $length[1] .' '. strtolower(LBL_CHARACTERS);
            return $datax;
        }
    }
    return $datax;
}

function debugTest()
{
    echo 'test';
    exit;
}

function getMinutesDiff($time1, $time2)
{
    $time1 = strtotime(date('H:i:s', $time1));
    $time2 = strtotime(date('H:i:s', $time2));
    return round((abs($time1) / 60) - (abs($time2) / 60));
}

function getLoginTempSessions($emailPhone)
{
    $ar = [];
    if(!isset($_SESSION['loginTemp']))
    {
        $_SESSION['loginTemp']['count'][$emailPhone] = 1;
        $_SESSION['loginTemp']['locked'][$emailPhone] = false;
    }
    $ar = [
        'count' => $_SESSION['loginTemp']['count'][$emailPhone],
        'locked' => $_SESSION['loginTemp']['locked'][$emailPhone]
    ];
    return $ar;
}
function updateTempSessions($emailPhone, $data)
{
    if(array_key_exists('count', $data))
    {
        if($data['count'] == 'inc')
        {
            $_SESSION['loginTemp']['count'][$emailPhone]++;
        }
        elseif($data['count'] == 'reset')
        {
            $_SESSION['loginTemp']['count'][$emailPhone] = 0;
        }
    }
    if(array_key_exists('locked', $data))
    {
        $_SESSION['loginTemp']['locked'][$emailPhone] = $data['locked'];
    }
}
