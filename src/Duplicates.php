<?php
namespace Nest;
use Nest\Crud\CrudActions;

class Duplicates {
    public static function checkDuplicates($table, $data)
    {
        if(count($data) > 0)
        {
            $duplicates = [];
            if(array_key_exists('orWhere', $data))
            {
                $duplicates = CrudActions::select(
                    $table,
                    [
                        'where' => $data['where'],
                        'orWhere' => $data['orWhere']
                    ]
                );
            }
            else
            {
                $duplicates = CrudActions::select(
                    $table,
                    [
                        'where' => $data['where']
                    ]
                );
            }
            if(count($duplicates) > 0)
            {
                return true;
            }
            return false;
        }
        return false;
    }
}