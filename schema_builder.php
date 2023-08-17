<?php

// read first line and get the column names
// print list of column names

$file = fopen("repd-april-2023.csv", "r");
$line = fgetcsv($file);
$column_names = array();
foreach ($line as $column) {
    $column_names[] = $column;
}

// record max length of each column
$column_lengths = array();

// check if numeric
$column_numeric = array();

// read to end of file
while (!feof($file)) {
    $line = fgetcsv($file);
    if ($line) {
        // print each line
        foreach ($line as $key => $value) {
            // record max length of each column
            if (!isset($column_lengths[$key]))
                $column_lengths[$key] = 0;
            $column_lengths[$key] = max($column_lengths[$key], strlen($value));

            // detect if all values of column are either int, float, or varchar
            if (!isset($column_numeric[$key])) {
                $column_numeric[$key] = 0;
            }

            if (($value === "") || ($value === NULL)) {
                continue;
            }
            // using preg_match
            if (preg_match('/^[0-9]+$/', $value)) {
                 $column_numeric[$key] = max($column_numeric[$key], 0);
            } else if (preg_match('/^[0-9]+\.[0-9]+$/', $value)) {
                 $column_numeric[$key] = max($column_numeric[$key], 1);
            } else {
                 $column_numeric[$key] = max($column_numeric[$key], 2);
            }
            
        }
    }
}

// print list of column names
foreach ($column_lengths as $key => $value) {
    // print $column_names[$key].", ".$value."\n";
    if ($column_numeric[$key] === 0) {
        print "'$column_names[$key]' => array('type' => 'int'),\n";
    } else if ($column_numeric[$key] === 1) {
        print "'$column_names[$key]' => array('type' => 'float'),\n";
    } else if ($column_numeric[$key] === 2) {
        print "'$column_names[$key]' => array('type' => 'varchar($value)'),\n";
    }
}