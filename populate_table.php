<?php

// read first line and get the column names
// print list of column names

$file = fopen("repd-april-2023.csv", "r");
$line = fgetcsv($file);
$column_names = array();
foreach ($line as $column) {
    $column_names[] = "`".trim($column)."`";
}

require "Lib/load_database.php";
$schema = array();
require "schema.php";

// Prepare the statement
$stmt = $mysqli->prepare("INSERT INTO REPD (".implode(", ", $column_names).") VALUES (".str_repeat("?, ", count($column_names) - 1)."?)");

while (!feof($file)) {
    $line = fgetcsv($file);
    if ($line) {
        // Filter out non-ASCII characters
        $line = array_map(function($value) {
            return preg_replace('/[^\x20-\x7E]/', '', $value);
        }, $line);

        // convert '' to NULL
        foreach ($line as $key => $value) {
            if ($value == "") {
                $line[$key] = NULL;
            }
        }

        // Bind the parameters
        $types = str_repeat("s", count($column_names));
        $stmt->bind_param($types, ...$line);

        // Execute the statement
        if ($stmt->execute()) {
            print "Inserted row: ".implode(", ", $line)."\n";
        } else {
            print "Error inserting row: ".$stmt->error."\n";
        }
    }
}

// Close the statement
$stmt->close();