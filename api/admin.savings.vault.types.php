<?php
use Nest\Crud\CrudActions;
require_once '../includes/util.php';

$name = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : "";

if($name == '')
{
    getJsonRow(false, "Please enter a name!");
}

$insert = CrudActions::insert(
    DEF_TBL_SAVINGS_VAULTS,
    [
        'id' => getNewId(),
        'name' => $name,
        'cdate' => time()
    ]
);

if($insert)
{
    getJsonRow(true, "Vault type created successfully!");
}
else
{
    getJsonRow(false, "An error occured while creating your vault!");
}

