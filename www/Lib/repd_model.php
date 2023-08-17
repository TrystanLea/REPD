<?php

class REPD
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function get_columns()
    {
        $result = $this->mysqli->query("SELECT * FROM REPD LIMIT 1");
        $columns = array();
        while ($column = $result->fetch_field()) {
            $columns[] = $column->name;
        }
        return $columns;
    }

    public function query($filters = array(), $order_by = "Installed Capacity (MWelec)") 
    {
        $filters = array();
        // $filters[] = array('column' => 'Operational', 'value' => NULL, 'not' => true);
        $filters[] = array('column' => 'Country', 'value' => 'Wales');
        // $filters[] = array('column' => 'Technology Type', 'value' => 'Solar Photovoltaics');
        //$filters[] = array('column' => 'Planning Application Withdrawn', 'value' => NULL);
        //$filters[] = array('column' => 'Planning Permission Refused', 'value' => NULL);
        //$filters[] = array('column' => 'Development Status', 'value' => 'Abandoned', 'not' => true);
        //$filters[] = array('column' => 'Development Status', 'value' => 'Planning Permission Expired', 'not' => true);
        
        // convert to sql
        $sql_filters = array();
        foreach ($filters as $filter) {
            if (isset($filter['not']) && $filter['not'])
                $sql_filters[] = "`" . $filter['column'] . "`!='" . $filter['value'] . "'";
            else if ($filter['value'] === NULL) {
                $sql_filters[] = "`" . $filter['column'] . "` IS NULL";
            } else 
                $sql_filters[] = "`" . $filter['column'] . "`='" . $filter['value'] . "'";
        }
        $sql_filters = implode(" AND ", $sql_filters);
        
        $result = $this->mysqli->query("SELECT * FROM REPD WHERE $sql_filters ORDER BY `$order_by` DESC");
        
        $data = array();
        $total_capacity = 0;
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}
