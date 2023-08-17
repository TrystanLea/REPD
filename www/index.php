<?php

require "Lib/load_database.php";
require "core.php";
require "route.php";

require "Lib/repd_model.php";
$repd = new REPD($mysqli);

$path = get_application_path(false);
$route = new Route(get('q'), server('DOCUMENT_ROOT'), server('REQUEST_METHOD'));

if ($route->controller=="") {
    $route->controller = "table";
}

switch ($route->controller) {
    case "table":
        $columns = $repd->get_columns();
        $output = view("view/table.php",array('columns'=>$columns));
        break;

    case "map":
        $output = view("view/map.php",array('data'=>$repd->query()));
        break;
        
    case "api":
        $route->format = "json";
        $output = $repd->query();
        break;
}

if ($route->format=="json") {
    header('Content-Type: application/json');
    echo json_encode($output);
} else {
    echo $output;
}

// echo view("theme.php",array("content"=>$output));