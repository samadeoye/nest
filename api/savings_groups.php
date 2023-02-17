<?php
require_once '../util.php';
require_once '../classes/savings.groups.class.php';

$action = isset($_POST['action']) ? trim($_POST['action']) : "";
if(!in_array($action, ['create', 'update', 'search']))
{
    getJsonRow(false, 'Invalid action!');
}

if($action == 'create')
{
    $params = Params::getRequestParams('create_savings_group');
    doValidateApiParams($params);
}
elseif($action == 'update')
{
    $params = Params::getRequestParams('update_savings_group');
    doValidateApiParams($params);
}
elseif($action == 'search')
{
    $params = Params::getRequestParams('search_savings_group');
    doValidateApiParams($params);
}

if(in_array($action, ['create', 'update']))
{
    $userId = trim($_POST['user_id']);
    $groupTypeId = trim($_POST['type_id']);
    $groupName = trim($_POST['group_name']);

    $data = [
        'user_id' => $userId,
        'action' => $action,
        'type_id' => $groupTypeId,
        'group_name' => $groupName
    ];
}
else
{
    $keyword = trim($_POST['keyword']);
}

if($action == 'create')
{
    SavingsGroup::createGroup($data);
}
elseif($action == 'update')
{
    $groupId = trim($_POST['group_id']);
    $data['group_id'] = $groupId;
    SavingsGroup::updateGroup($data);
}
elseif($action == 'search')
{
    SavingsGroup::searchGroup($keyword);
}