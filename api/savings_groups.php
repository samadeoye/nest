<?php
require_once '../util.php';
require_once '../classes/savings.groups.class.php';

$action = isset($_POST['action']) ? trim($_POST['action']) : "";
if(!in_array($action, ['create', 'update', 'search', 'join', 'details', 'list']))
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
elseif($action == 'details')
{
    $params = Params::getRequestParams('details_savings_group');
}
if($action != 'list')
{
    doValidateApiParams($params);
}

if(in_array($action, ['create', 'update']))
{
    $userId = trim($_POST['user_id']);
    $groupTypeId = trim($_POST['type_id']);
    $groupName = trim($_POST['group_name']);
    $plan = trim($_POST['plan']);
    $planType = trim($_POST['plan_type_id']);
    $duration = trim($_POST['duration']);
    $durationType = trim($_POST['duration_type_id']);
    $description = trim($_POST['description']);

    $data = [
        'user_id' => $userId,
        'type_id' => $groupTypeId,
        'name' => $groupName,
        'plan' => $plan,
        'plan_type_id' => $planType,
        'duration' => $duration,
        'duration_type_id' => $durationType,
        'description' => $description
    ];
}
elseif($action == 'search')
{
    $keyword = trim($_POST['keyword']);
}
elseif(in_array($action, ['join', 'details']))
{
    $userId = trim($_POST['user_id']);
    $groupId = trim($_POST['group_id']);

    $data = [
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
elseif($action == 'details')
{
    SavingsGroup::getGroup($data);
}
elseif($action == 'list')
{
    SavingsGroup::listGroup();
}