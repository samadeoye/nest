<?php
class CrudActions {
    public static function insert($table, $data, $values=[])
    {
        global $db;

        $insert = "INSERT INTO ".$table." ".$data;
        $insert = $db->prepare($insert);
        $insert->execute($values);
        if($insert)
        {
            return true;
        }
        return false;
    }

    public static function select($table, $cols="*", $where="", $values=[], $type="row")
    {
        global $db;

        $select = "SELECT ".$cols." FROM ".$table." WHERE ".$where;
        $select = $db->prepare($select);
        $select->execute($values);
        if($select->rowCount() > 0)
        {
            if($type == 'row')
            {
                return $select->fetch();
            }
            return $select->fetchAll();
        }
        return [];
    }

    public static function update($table, $data, $where="", $values=[])
    {
        global $db;

        $update = "UPDATE ".$table." SET ".$data." WHERE ".$where;
        $update = $db->prepare($update);
        $update->execute($values);
        if($update)
        {
            return true;
        }
        return false;
    }

    public static function delete($table, $where="", $values=[])
    {
        global $db;

        $delete = "DELETE FROM ".$table." WHERE ".$where;
        $delete = $db->prepare($delete);
        $delete->execute($values);
        if($delete)
        {
            return true;
        }
        return false;
    }

    public static function checkDuplicate($table, $where, $values)
    {
        $select = self::select($table, "id", $where, $values, "row");
        if(count($select) > 0)
        {
            return true;
        }
        return false;
    }

    public static function validateRecord($table, $where, $values)
    {
        $select = self::select($table, "id", $where, $values, "row");
        if(count($select) > 0)
        {
            return true;
        }
        return false;
    }
}