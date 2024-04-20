<?php 

global $wpdb;

// Table name
$table_name = $wpdb->prefix . 'dwm_goods_received';

// Bin ID you want to search for
$bin_id = get_the_title();

// SQL query to retrieve the row
$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE place_id = '{$bin_id}'");

// Execute the query
$row = $wpdb->get_row($sql);

if ($row) {
    // Row found, you can access the data like this
    echo $row->id . "<br>";
    echo "product_name -" .$row->product_name . "<br>";
    echo "batch_number -" .$row->batch_number . "<br>";
    echo "expiry_date -" .$row->expiry_date . "<br>";
    echo "pallet_id -" .$row->pallet_id . "<br>";
    echo "quantity -" .$row->quantity . "<br>";
    // ... access other fields as needed
} else {
    // No row found
    echo "No row found for place ID $bin_id in the 'wp_dwm_goods_received' table.";
}

?>