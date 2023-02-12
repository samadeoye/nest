<?php
class CrudActions {
    public static function insert($table, $data)
    {
        global $db;

        $cols = array_keys($data);
        $values = array_values($data);
        //generate the escaping ? based on the number of data keys
        $valString = str_repeat("?,", count($cols));
        //remove the last comma
        $valString = substr($valString, 0, -1);
        $cols = implode(',', $cols);
        $insert = "INSERT INTO ".$table." (".$cols.") VALUES(".$valString.")";
        $insert = $db->prepare($insert);
        $insert->execute($values);
        if($insert)
        {
            return true;
        }
        return false;
    }

    public static function select($table, $data=[])
    {
        global $db;

        $columns = array_key_exists("columns", $data) && !empty($data['columns']) ? $data['columns'] : "*";
        $return_type = array_key_exists("return_type", $data) && !empty($data['return_type']) ? $data['return_type'] : "row";
        $where = "";
        $values = [];
        $allowWhere = false;
        if(array_key_exists("where", $data) && count($data['where']) > 0)
        {
            $allowWhere = true;
        }
        if($allowWhere)
        { 
            $where .= " WHERE "; 
            $i = 0; 
            foreach($data['where'] as $key => $value)
            {
                $con = ($i > 0) ? " AND " : "";
                $where .= $con . $key . " = ?";
                $i++;
            }
            $values = array_values($data['where']);
        }
        $allowOrWhere = false;
        if(array_key_exists("orWhere", $data) && count($data['orWhere']) > 0)
        {
            $allowOrWhere = true;
        }
        if($allowWhere && $allowOrWhere)
        {
            $i = 0; 
            foreach($data['orWhere'] as $key => $value)
            {
                $con = ($i > 0) ? " OR " : "";
                $where .= $con . $key . " = ?";
                $i++;
            }
            $valuesx = array_values($data['orWhere']);
            $values = array_merge($values, $valuesx);
        }
        
        $select = "SELECT ".$columns." FROM ".$table . $where;
        $select = $db->prepare($select);
        $select->execute($values);
        if($select->rowCount() > 0)
        {
            if($return_type == 'row')
            {
                return $select->fetch();
            }
            return $select->fetchAll();
        }
        return [];
    }

    public static function update($table, $data, $where=[])
    {
        global $db;

        $datax = "";
        $values = [];
        if(count($data) > 0)
        {
            $i = 0; 
            foreach($data as $key => $value)
            {
                $con = ($i > 0) ? ", " : "";
                $datax .= $con . $key . " = ?";
                $i++;
            }
            $values = array_values($data);
        }

        $whre = "";
        if(count($where) > 0)
        { 
            $whre .= " WHERE ";
            $i = 0; 
            foreach($where as $key => $value)
            {
                $con = ($i > 0) ? " AND " : "";
                $whre .= $con . $key . " = ?";
                $i++;
            }
            $valuesx = array_values($where);
            $values = array_merge($values, $valuesx);
        }
        
        $update = "UPDATE ".$table." SET ".$datax . $whre;
        $update = $db->prepare($update);
        $update->execute($values);
        if($update)
        {
            return true;
        }
        return false;
    }

    public static function delete($table, $where=[])
    {
        global $db;

        $values = [];
        $whre = "";
        if(count($where) > 0)
        { 
            $whre .= " WHERE ";
            $i = 0; 
            foreach($where as $key => $value)
            {
                $con = ($i > 0) ? " AND " : "";
                $whre .= $con . $key . " = ?";
                $i++;
            }
            $values = array_values($where);
        }
        $delete = "DELETE FROM ".$table . $whre;
        $delete = $db->prepare($delete);
        $delete->execute($values);
        if($delete)
        {
            return true;
        }
        return false;
    }
}