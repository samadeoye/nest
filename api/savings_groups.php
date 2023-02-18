<?php
require_once '../util.php';
require_once '../classes/savings.groups.class.php';

$action = isset($_POST['action']) ? trim($_POST['action']) : "";
if(!in_array($action, ['create', 'update', 'search', 'join']))
{
    getJsonRow(false, 'Invalid action!');
}

if($action == 'create')
{
    $params = Params::getRequestParams('create_savings_group');
}
elseif($action == 'update')
{
    $params = Params::getRequestParams('update_savings_group');
}
elseif($action == 'search')
{
    $params = Params::getRequestParams('search_savings_group');
}
elseif($action == 'join')
{
    $params = Params::getRequestParams('join_savings_group');
}
doValidateApiParams($params);

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
elseif($action == 'search')
{
    $keyword = trim($_POST['keyword']);
}
elseif($action == 'join')
{
    $userId = trim($_POST['user_id']);
    $groupId = trim($_POST['group_id']);

    $data = [
        'action' => $action,
        'user_id' => $userId,
        'group_id' => $groupId
    ];
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
elseif($action == 'join')
{
    SavingsGroup::joinGroup($data);
}