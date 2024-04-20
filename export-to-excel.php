<?php
// Include WordPress
define('WP_USE_THEMES', false);
$wp_root = dirname(dirname(dirname(dirname(__FILE__))));
require_once($wp_root . '/wp-load.php');

global $wpdb;

// Capture post_id from URL
$post_id = isset($_GET['wh']) ? intval($_GET['wh']) : 0; // Validate and sanitize

$post_title = get_post( $post_id )->post_title;

$date = date('Y-m-d');
// Set headers for the excel file
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$post_title - $date stock take.xls");

// Query the table with post_id condition
$table_name = $wpdb->prefix . 'dwm_goods_received';
$sql = "SELECT * FROM $table_name WHERE warehouse_id = %d"; // Use prepared statement
$rows = $wpdb->get_results($wpdb->prepare($sql, $post_id), 'ARRAY_A');

// Print table headers
if ($rows) {
    echo implode("\t", array_keys($rows[0])) . "\n";
}

// Print rows
foreach ($rows as $row) {
    echo implode("\t", array_values($row)) . "\n";
}
?>
