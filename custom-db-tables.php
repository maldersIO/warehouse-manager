<?php

function create_custom_tables()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $dwm_goods_received_table = $wpdb->prefix . 'dwm_goods_received';

    $sql_dwm_goods_received_table = "CREATE TABLE $dwm_goods_received_table (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `product_name` text NOT NULL,
            `batch_number` text NOT NULL,
            `expiry_date` date NOT NULL,
            `pallet_id` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            `amount_of_pallets` int(11) NOT NULL,
            `quantity` int(11) NOT NULL,
            `amount_of_bags` int(11) NOT NULL,
            `rack` int(11) NOT NULL,
            `level` int(11) NOT NULL,
            `position` int(11) NOT NULL,
            `place_id` text NOT NULL,
            `bin_status` int(11) NOT NULL,
            `warehouse_id` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) $charset_collate;";

    $dwm_uom_table = $wpdb->prefix . 'dwm_uom';

    $sql_dwm_uom = "CREATE TABLE  $dwm_uom_table (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `short_name` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `name` text NOT NULL,
        `added_by` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`)
        ) $charset_collate;";


    $sql_dwm_uom_insert = "INSERT INTO $dwm_uom_table (`id`, `short_name`, `name`, `added_by`) VALUES
    (1,	'kg',	'Kilograms',	NULL),
    (2,	'ml',	'Millilitres',	NULL),
    (3,	'l',	'Litres',	NULL),
    (4,	't',	'Tons',	NULL);";

    $dwm_movement_statuses_table = $wpdb->prefix . 'dwm_movement_statuses';

    $sql_movement_statuses = "CREATE TABLE $dwm_movement_statuses_table (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` text NOT NULL,
      `date_created` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) $charset_collate;";

    $sql_movement_statuses_insert = "INSERT INTO $dwm_movement_statuses_table (`id`, `name`, `date_created`) VALUES
    (1,	'Open',	'2023-11-15 11:26:56'),
    (2,	'Cancelled',	'2023-11-15 11:27:05'),
    (3,	'Complete',	'2023-11-15 11:27:12');";

    $dwm_movement_list_items_table = $wpdb->prefix . 'dwm_movement_list_items';

    $sql_dwm_movement_list_items = "CREATE TABLE  $dwm_movement_list_items_table (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `movement_list_id` text NOT NULL,
        `place_id` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        PRIMARY KEY (`id`)
      ) $charset_collate;";

    $dwm_movement_list_table = $wpdb->prefix . 'dwm_movement_list';

    $sql_dwm_movement_list = "CREATE TABLE $dwm_movement_list_table (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `movement_list_id` text NOT NULL,
        `created_date` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
        `bay` int(11) NOT NULL,
        `movement_status` int(11) NOT NULL,
        `warehouse_id` int(11) NOT NULL,
        `created_by` int(11) NOT NULL,
        `confirmed_by` int(11) NOT NULL,
        PRIMARY KEY (`id`)
      )  $charset_collate;";

    $dwm_bin_statuses = $wpdb->prefix . 'dwm_bin_statuses';

    $sql_dwm_bin_statuses = "CREATE TABLE $dwm_bin_statuses (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` text NOT NULL,
        `date_created` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
      )  $charset_collate;";

    $sql_dwm_bin_statuses_insert = "INSERT INTO $dwm_bin_statuses (`id`, `name`, `date_created`) VALUES
    (1,	'Filled',	'2023-11-15 07:45:02'),
    (2,	'Reserved',	'2023-11-15 07:45:19'),
    (3,	'Locked',	'2023-11-15 07:51:41'),
    (4,	'Open',	'2023-11-16 09:03:30');";

    $dwm_bays = $wpdb->prefix . 'dwm_bays';

    $sql_dwm_bays = "CREATE TABLE $dwm_bays (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` text NOT NULL,
        `date_created` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
      )  $charset_collate;";

    $sql_dwm_bays_insert = "INSERT INTO $dwm_bays (`id`, `name`, `date_created`) VALUES
    (1,	'Warehouse',	'2023-11-15 11:27:54'),
    (2,	'Quarantine',	'2023-11-15 11:28:10'),
    (3,	'Discard',	'2023-11-15 11:28:18');";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_dwm_goods_received_table);
    dbDelta($sql_dwm_uom);
    dbDelta($sql_dwm_uom_insert);
    dbDelta($sql_movement_statuses);
    dbDelta($sql_movement_statuses_insert);
    dbDelta($sql_dwm_movement_list_items);
    dbDelta($sql_dwm_movement_list);
    dbDelta($sql_dwm_bin_statuses);
    dbDelta($sql_dwm_bin_statuses_insert);
    dbDelta($sql_dwm_bays);
    dbDelta($sql_dwm_bays_insert);
}

register_activation_hook(__FILE__, 'create_custom_tables');