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

function doNumberFormat($number)
{
    return number_format($number, 2);
}
function doTypeCastDouble($number)
{
    return doubleval($number);
}
function doTypeCastInt($number)
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
    
    $param = strtolower($param);
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
            $datax['msg'] = $label . ' is required.';
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
    if((!empty($value) && $isEmail) || (!empty($value) && trim($param) == 'email'))
    {
        if(!filter_var($value, FILTER_VALIDATE_EMAIL))
        {
            $datax['status'] = false;
            $datax['msg'] = $label . ' must contain a valid email.';
            return $datax;
        }
    }
    if($length[0] > 0 && $length[1] > 0 && $length[0] == $length[1] && !empty($value))
    {
        $isset = $isset && strlen($value) == $length[0];
        if(!$isset)
        {
            $datax['status'] = false;
            if(strpos($param, '_id') !== false || $param == 'id')
            {
                $datax['msg'] = $label . ' in invalid.';
            }
            else
            {
                $datax['msg'] = $label . ' must be equal to ' . $length[0] .' characters.';
            }
            return $datax;
        }
    }
    if($length[0] > 0 && !empty($value))
    {
        $isset = $isset && strlen($value) >= $length[0];
        if(!$isset)
        {
            $datax['status'] = false;
            if(strpos($param, '_id') !== false || $param == 'id')
            {
                $datax['msg'] = $label . ' in invalid.';
            }
            else
            {
                $datax['msg'] = $label . ' must be greater than or equal to ' . $length[0] .' characters.';
            }
            return $datax;
        }
    }
    if($length[1] > 0 && !empty($value))
    {
        $isset = $isset && strlen($value) <= $length[1];
        if(!$isset)
        {
            $datax['status'] = false;
            if(strpos($param, '_id') !== false || $param == 'id')
            {
                $datax['msg'] = $label . ' in invalid.';
            }
            else
            {
                $datax['msg'] = $label . ' must be less than or equal to ' . $length[1] .' characters.';
            }
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

function getLoginTempSessions($emailPhone, $key)
{
    if(!isset($_SESSION['loginTemp']))
    {
        $_SESSION['loginTemp']['count'][$emailPhone] = 1;
        $_SESSION['loginTemp']['locked'][$emailPhone] = false;
    }
    
    if($key == 'count')
    {
        if(!isset($_SESSION['loginTemp']['count'][$emailPhone]))
        {
            $_SESSION['loginTemp']['count'][$emailPhone] = 1;
        }
        return $_SESSION['loginTemp']['count'][$emailPhone];
    }
    elseif($key == 'locked')
    {
        if(!isset($_SESSION['loginTemp']['locked'][$emailPhone]))
        {
            $_SESSION['loginTemp']['locked'][$emailPhone] = false;
        }
        return $_SESSION['loginTemp']['locked'][$emailPhone];
    }
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

function getTypeFromTypeId($type, $id)
{
    if(!empty($type))
    {
        if(strtolower($type) == 'savings_plan')
        {
            if($id == 1)
            {
                return 'daily';
            }
            elseif($id == 2)
            {
                return 'weekly';
            }
            elseif($id == 3)
            {
                return 'monthly';
            }
            elseif($id == 4)
            {
                return 'anytime';
            }
        }
        elseif(strtolower($type) == 'savings_duration')
        {
            if($id == 1)
            {
                return 'week';
            }
            elseif($id == 2)
            {
                return 'month';
            }
            elseif($id == 3)
            {
                return 'year';
            }
        }
        elseif(strtolower($type) == 'user_type')
        {
            if($id == 1)
            {
                return 'underbanked';
            }
            elseif($id == 2)
            {
                return 'corporative society';
            }
            elseif($id == 3)
            {
                return 'digital professional';
            }
            elseif($id == 4)
            {
                return 'business';
            }
        }
        elseif(strtolower($type) == 'savings_type')
        {
            if($id == DEF_SAVINGS_TYPE_REGULAR)
            {
                return 'regular';
            }
            elseif($id == DEF_SAVINGS_TYPE_TARGET)
            {
                return 'target';
            }
            elseif($id == DEF_SAVINGS_TYPE_VAULT)
            {
                return 'vault';
            }
            elseif($id == DEF_SAVINGS_TYPE_FLEX)
            {
                return 'flex';
            }
        }
    }
}

function getDateFormat($date)
{
    return date('d-m-Y i:s A', $date);
}
