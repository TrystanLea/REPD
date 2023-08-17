<?php

require "Lib/load_database.php";

$order_by = "Installed Capacity (MWelec)";

$filters = array();
$filters[] = array('column' => 'Operational', 'value' => '', 'not' => true);
$filters[] = array('column' => 'Country', 'value' => 'Wales');
$filters[] = array('column' => 'Technology Type', 'value' => 'Wind Onshore');

// convert to sql
$sql_filters = array();
foreach ($filters as $filter) {
    if (isset($filter['not']) && $filter['not'])
        $sql_filters[] = "`".$filter['column']."`!='".$filter['value']."'";
    else
        $sql_filters[] = "`".$filter['column']."`='".$filter['value']."'";
}
$sql_filters = implode(" AND ", $sql_filters);

$result = $mysqli->query("SELECT * FROM REPD WHERE $sql_filters ORDER BY `$order_by` DESC");

$total_capacity = 0;
while ($row = $result->fetch_assoc()) {
    print $row['Site Name'].", ";
    // print $row['RO Banding (ROC/MWh)'].", ";
    // print $row['FiT Tariff (p/kWh)'].", ";
    // print $row['Turbine Capacity (MW)'].", ";

    print $row['X-coordinate'].", ";
    print $row['Y-coordinate'].", ";

    //print $row['Installed Capacity (MWelec)']."\n";
    print "\n";
    $total_capacity += $row['Installed Capacity (MWelec)'];

}
print $total_capacity."\n";