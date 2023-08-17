<?php

require "Lib/load_database.php";
require "Lib/dbschemasetup.php";

// delete REPD table
$mysqli->query("DROP TABLE IF EXISTS REPD");

$schema = array();
require "schema.php";
print json_encode(db_schema_setup($mysqli, $schema, true))."\n";
