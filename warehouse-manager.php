<?php

/**
 * Plugin Name: Distinct Warehouse Manager
 * Plugin URI: https://wpracks.com/
 * Description: Warehouse managing system 
 * Version: 1.1
 * Author: Distinct
 * Author URI: https://distinct.africa
 **/

include plugin_dir_path(__FILE__) . 'custom-settings.php';
include plugin_dir_path(__FILE__) . 'enqueue-manager.php';
include plugin_dir_path(__FILE__) . 'ajax-functions.php';
include plugin_dir_path(__FILE__) . 'warehouse-admin.php';

function create_custom_tables()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $dwm_goods_received_table = $wpdb->prefix . 'dwm_goods_received';

    $sql_dwm_goods_received_table = "CREATE TABLE $dwm_goods_received_table (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `product_name` text NOT NULL,
            `expiry_date` date NOT NULL,
            `custom_input` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            `custom_input_2` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            `custom_input_3` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            `custom_input_4` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            `custom_input_5` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            `amount_of_pallets` int(11) NOT NULL,
            `quantity` int(11) NOT NULL,
            `pallet_id` text NOT NULL,
            `amount_of_bags` int(11) NOT NULL,
            `bag_total` int(11) NOT NULL,
            `rack` int(11) NOT NULL,
            `level` int(11) NOT NULL,
            `position` int(11) NOT NULL,
            `bin_id` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            `bin_status` int(11) NOT NULL,
            `warehouse_id` int(11) NOT NULL,
            `movement_list_id` text NOT NULL,
            `bay_id` int(11) NOT NULL,
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
        `bin_id` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
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
        `reason` text NOT NULL,
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
        id` int(11) NOT NULL AUTO_INCREMENT,
        `name` text NOT NULL,
        `color` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `type_id` int(11) NOT NULL,
        `warehouse_id` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `date_created` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
      )  $charset_collate;";

    $sql_dwm_bays_insert = "INSERT INTO $dwm_bays (`id`, `name`, `color`, `warehouse_id`, `date_created`) VALUES
    (1,	'Warehouse',	'',	'',	'2023-11-15 11:27:54'),
    (2,	'Quarantine',	'',	'',	'2023-11-15 11:28:10'),
    (3,	'Discard',	'',	'',	'2023-11-15 11:28:18'),
    (4,	'Receiving',	'',	'',	'2023-12-18 08:17:07');";

    $dwm_bay_types = $wpdb->prefix . 'dwm_bay_types';

    $sql_dwm_bay_types = "CREATE TABLE $dwm_bay_types (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `date_created` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
      )  $charset_collate;";

    $sql_dwm_bay_types_insert = "INSERT INTO $dwm_bay_types (`id`, `name`, `date_created`) VALUES
    (1,	'Receiving',	'2024-01-31 10:18:06'),
    (2,	'Internal',	'2024-01-31 10:18:34'),
    (3,	'Dispatch',	'2024-01-31 10:18:42');";

    $dwm_picking_list = $wpdb->prefix . 'dwm_picking_list';

    $sql_dwm_picking_list = "CREATE TABLE $dwm_picking_list (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `picking_list_id` text NOT NULL,
        `picking_list_status` int(11) NOT NULL,
        `warehouse_id` int(11) NOT NULL,
        `picking_list` text NOT NULL,
        `created_by` int(11) NOT NULL,
        `movement_list_id` text NOT NULL,
        `reference_number` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `note` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
        `date_created` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    )  $charset_collate;";

    $dwm_picking_list_statuses = $wpdb->prefix . 'dwm_picking_list_statuses';

    $sql_dwm_picking_list_statuses = "CREATE TABLE $dwm_picking_list_statuses (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(11) NOT NULL,
        `date_created` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    )  $charset_collate;";

    $sql_dwm_picking_list_statuses_insert = "INSERT INTO $dwm_picking_list_statuses (`id`, `name`, `date_created`) VALUES
    (1,	'Open',	'2024-03-26 09:07:40'),
    (2,	'Completed',	'2024-03-26 09:07:56');";


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
    dbDelta($sql_dwm_bay_types);
    dbDelta($sql_dwm_bay_types_insert);
    dbDelta($sql_dwm_picking_list);
    dbDelta($sql_dwm_picking_list_statuses);
    dbDelta($sql_dwm_picking_list_statuses_insert);
}

register_activation_hook(__FILE__, 'create_custom_tables');

function my_plugin_custom_template($template)
{
    // Check if the current page is 'Create Labels'
    if (is_page('Create Labels')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/create-labels.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    // Check if the current page is 'Picking List'
    if (is_page('Picking List')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/picking-list.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }

    // Return the original template if neither custom page is detected
    return $template;
}
add_filter('template_include', 'my_plugin_custom_template', 99);





function create_my_custom_pages()
{
    // Array of page titles and their corresponding template files
    $pages = array(
        'Create Labels' => 'templates/create-labels.php',
        'Picking List'  => 'templates/picking-list.php',
    );

    foreach ($pages as $page_title => $template_file) {
        // Check if the page exists

        $args = array(
            'post_type'      => 'page',
            'posts_per_page' => 1,
            'no_found_rows'  => true,
            'post_status'    => 'publish',
            'title'          => $page_title
          );
          
          $query = new WP_Query($args);
          
          if ($query->have_posts()) {
              while ($query->have_posts()) {
                  $query->the_post();
                  // Now $post->ID is the ID of the page you can use
                  $page = get_post(get_the_ID());
              }
          } else {
              $page = null; // No page found
          }
          
          wp_reset_postdata(); // Reset post data to avoid conflicts
          

        // If the page doesn't exist, create it
        if (!$page) {
            // Set up the page data
            $my_page = array(
                'post_title'    => $page_title,
                'post_content'  => "This is the content of my custom page titled '{$page_title}'.",
                'post_status'   => 'publish',
                'post_author'   => 1,
                'post_type'     => 'page',
            );

            // Insert the page into the database
            $page_id = wp_insert_post($my_page);

            // Check if the page was created successfully
            if ($page_id && !is_wp_error($page_id)) {
                // Assign the custom template from the templates directory
                update_post_meta($page_id, '_wp_page_template', $template_file);
            }
        }
    }
}
add_action('wp_loaded', 'create_my_custom_pages');


// Function to register the custom post type and taxonomies when the plugin is activated
function custom_plugin_activate()
{
    // Register the 'Warehouses' custom post type
    register_post_type(
        'warehouses',
        array(
            'labels' => array(
                'name' => __('Warehouses'),
                'singular_name' => __('Warehouse'),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon'   => 'dashicons-grid-view',
            'supports' => array('title', 'thumbnail'),
        )
    );

    // Register the 'Bins' custom post type
    register_post_type(
        'bins',
        array(
            'labels' => array(
                'name' => __('Bins'),
                'singular_name' => __('Bin'),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon'   => 'dashicons-excerpt-view',
            'supports' => array('title'),
        )
    );

    // Register the 'Products' custom post type
    register_post_type(
        'warehouse-products',
        array(
            'labels' => array(
                'name' => __('Warehouse Products'),
                'singular_name' => __('Warehouse Products'),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon'   => 'dashicons-products',
            'supports' => array('title'),
        )
    );
}
add_action('init', 'custom_plugin_activate', 0);

function warehouse_updated_messages($messages)
{
    $post = get_post();

    $messages['warehouses'] = array(
        0 => '', // Unused. Messages start at index 1.
        1 => __('Warehouse updated.', 'text_domain'),
        2 => __('Custom field updated.', 'text_domain'),
        3 => __('Custom field deleted.', 'text_domain'),
        4 => __('Warehouse updated.', 'text_domain'),
        5 => isset($_GET['revision']) ? sprintf(__('Warehouse restored to revision from %s', 'text_domain'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
        6 => __('Warehouse published.', 'text_domain'),
        7 => __('Warehouse saved.', 'text_domain'),
        8 => __('Warehouse submitted.', 'text_domain'),
        9 => sprintf(
            __('Warehouse scheduled for: <strong>%1$s</strong>.', 'text_domain'),
            date_i18n(__('M j, Y @ G:i', 'text_domain'), strtotime($post->post_date))
        ),
        10 => __('Warehouse draft updated.', 'text_domain')
    );

    return $messages;
}
add_filter('post_updated_messages', 'warehouse_updated_messages');

function add_manage_warehouse_action($actions, $post)
{
    if ($post->post_type == 'warehouses') {
        $manage_url = admin_url('admin.php?page=manage_warehouse&post_id=' . $post->ID);

        // Echo the URL if you need to use it in HTML or a template

        $actions['manage_warehouse'] = '<a href="' . $manage_url . '">Manage Warehouse</a>';
    }
    return $actions;
}
add_filter('post_row_actions', 'add_manage_warehouse_action', 10, 2);


function manage_warehouse_page_callback()
{
    $post_id = isset($_GET['post_id']) ? $_GET['post_id'] : false;
    if ($post_id) {
        // Load your manage warehouse template or code here
        // Include your custom template
        include_once(plugin_dir_path(__FILE__) . 'templates/manage-warehouse.php');
        exit;
    } else {
        echo 'No warehouse selected.';
    }
}

function add_manage_warehouse_admin_page()
{
    add_menu_page(
        'Manage Warehouse',
        'Manage Warehouse',
        'manage_options',
        'manage_warehouse',
        'manage_warehouse_page_callback',
        'dashicons-warehouse',
        6 // The position in the menu order this item should appear.
    );
}

add_action('admin_menu', 'add_manage_warehouse_admin_page');



function hide_manage_warehouse_menu()
{
    remove_menu_page('manage_warehouse');
}

// Hook into the admin_menu action with lower priority to ensure it's called after the menu is added.
add_action('admin_menu', 'hide_manage_warehouse_menu', 999);




// Function to modify the "Add title" text to "Warehouse name" for the 'warehouses' post type
function custom_change_title_text($title, $post)
{
    if ('warehouses' == $post->post_type) {
        $title = 'Warehouse name';
    }
    return $title;
}
add_filter('enter_title_here', 'custom_change_title_text', 10, 2);


// Function to load the custom single-warehouse.php template
function custom_single_warehouse_template($template)
{
    if (is_singular('warehouses')) {
        $custom_template = plugin_dir_path(__FILE__) . 'templates/single-warehouse.php';

        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }

    return $template;
}

// Hook the function to the 'template_include' filter
add_filter('template_include', 'custom_single_warehouse_template');

// Function to load the custom single-bin.php template
function custom_single_bin_template($template)
{
    if (is_singular('bins')) {
        $custom_template = plugin_dir_path(__FILE__) . 'templates/single-bin.php';

        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }

    return $template;
}



function add_warehouse_product_meta_boxes()
{
    add_meta_box(
        'warehouse_product_details',
        __('Product Details'),
        'render_warehouse_products_meta_box',
        'warehouse-products',
        'normal',
        'high'
    );
}
// Function to render the content of the custom meta box
function render_warehouse_products_meta_box($post)
{
    // Retrieve the values of predefined fields, if they exist
    $pack_size = get_post_meta($post->ID, 'pack_size', true);
    $pack_unit_id = get_post_meta($post->ID, 'pack_unit_id', true);
    $inventory_id = get_post_meta($post->ID, 'inventory_id', true);
    // Output the HTML form fields for predefined fields
?>
    <div class="plugin-form">
        <label for="pack_size"><?php _e('Pack Size:'); ?></label>
        <input class="form-control" type="number" id="pack_size" name="pack_size" value="<?php echo esc_attr($pack_size); ?>"><br>

        <label for="pack_unit_id"><?php _e('Pack Unit:'); ?></label>
        <select class="form-select" name="pack_unit_id" aria-label="Pack unit of measurement">
            <option selected>Select</option>
            <?php


            global $wpdb;
            // Table name
            $table_name = $wpdb->prefix . 'dwm_uom';
            $sql = $wpdb->prepare("SELECT * FROM $table_name");

            $rows = $wpdb->get_results($sql);

            $default_value = get_post_meta($post, 'pack_unit_id', true); // Replace with your actual method to get the default value

            foreach ($rows as $row) {
                $selected = ($row->id == $default_value) ? 'selected' : '';
                echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->short_name . '</option>';
            }
            ?>
        </select><br>

        <label for="inventory_id"><?php _e('Inventory ID:'); ?></label>
        <input class="form-control" type="text" id="inventory_id" name="inventory_id" value="<?php echo esc_attr($inventory_id); ?>"><br>

        <div class="branding">
            <p><strong>WP <span>Racks</span></strong> - Warehouse Management</p>
        </div>
    </div>
<?php
}

// Function to save the values of predefined fields
function save_warehouse_products_meta($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Save the values of predefined fields
    $fields = array(
        'pack_size',
        'pack_unit_id',
        'inventory_id',
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}

// Hook the meta box functions to WordPress actions
add_action('add_meta_boxes', 'add_warehouse_product_meta_boxes');
add_action('save_post_warehouse-products', 'save_warehouse_products_meta');



// Hook the function to the 'template_include' filter
add_filter('template_include', 'custom_single_bin_template');

function add_warehouse_roles()
{
    // Adding Warehouse Manager Role
    add_role(
        'warehouse_manager',
        'Warehouse Manager',
        array(
            'read' => true, // basic WordPress capability
            // ... add other capabilities here
        )
    );

    // Adding Warehouse Administrator Role
    add_role(
        'warehouse_admin',
        'Warehouse Administrator',
        array(
            'read' => true,
            // ... add other capabilities here
        )
    );

    // Adding Warehouse Supervisor Role
    add_role(
        'warehouse_supervisor',
        'Warehouse Supervisor',
        array(
            'read' => true,
            // ... add other capabilities here
        )
    );


    // Adding Receiving clerk Role
    add_role(
        'receiving_clerk',
        'Receiving clerk',
        array(
            'read' => true,
            // ... add other capabilities here
        )
    );

    // Adding Dispatch clerk Role
    add_role(
        'dispatch_clerk',
        'Dispatch clerk',
        array(
            'read' => true,
            // ... add other capabilities here
        )
    );
}

// Hooking up the function to WordPress 'init'
add_action('init', 'add_warehouse_roles');



function dwm_goods_receiving_form($atts)
{

    $atts = shortcode_atts(array(
        'racks' => $atts['racks'],
        'levels_per_rack' => $atts['levels_per_rack'],
        'capacities' => $atts['capacities']
    ), $atts, 'warehouse');

    global $wpdb;
    $table_name = $wpdb->prefix . 'dwm_goods_received';

    $warehouse_id = $_GET['post_id'];

    $sql = "SELECT * FROM $table_name WHERE warehouse_id = $warehouse_id AND bin_status NOT IN (4);";
    $results = $wpdb->get_results($sql);

    $binArr = array();

    foreach ($results as $res) {
        array_push($binArr, $res->bin_id);
    }

    $bin_ids = implode(",", $binArr);

    ob_start();

?>

    <form method="post" id="goods-form">
        <!-- Product details -->
        <?php $title = "Product Details"; ?>
        <h2> <?php echo $title; ?></h2>
        <p>Please provide the product information that you are booking in</p>

        <div class="row">
            <div class="col">
                <div class="form-floating">
                    <select class="form-select" id="product_name" name="product_name" aria-label="Default select example" placeholder="Select a product" required>
                        <option selected disabled>Select a product</option>
                        <?php


                        $args = array(
                            'post_type' => 'warehouse-products', // Change the post type to your specific post type if needed
                            'meta_key' => 'inventory_id',
                            'posts_per_page' => -1, // Retrieve all matching posts
                            'fields' => 'ids', // Get only post IDs to reduce overhead
                        );



                        $query = new WP_Query($args);



                        if ($query->have_posts()) {
                            $post_ids = $query->posts;



                            foreach ($post_ids as $post_id) {
                                $post = get_post($post_id);
                                $product_name =  get_the_title($post);
                                $inventory_id = get_post_meta($post_id, 'inventory_id', true);
                                echo '<option value="' . $post_id . '">' . $product_name . '(' . $inventory_id . ')</option>';
                            }

                            wp_reset_postdata(); // Restore the original post data
                        }
                        ?>
                    </select>
                    <label for="product_name" class="form-label">Product name:</label>
                </div>
            </div>
        </div>
        <input type="hidden" id="wh_racks" name="wh_racks" value="<?php echo $atts['racks'] ?>">
        <input type="hidden" id="wh_levels_per_rack" name="wh_levels_per_rack" value="<?php echo $atts['levels_per_rack'] ?>">
        <input type="hidden" id="wh_capacities" name="wh_capacities" value="<?php echo $atts['capacities'] ?>">
        <input type="hidden" id="bin_ids" name="bin_ids" value="<?php echo $bin_ids ?>">
        <div class="row">
            <?php
            $custom_input_label = get_option('custom_input_label', '');
            $custom_input_2_label = get_option('custom_input_2_label', '');
            $custom_input_3_label = get_option('custom_input_3_label', '');
            $custom_input_4_label = get_option('custom_input_4_label', '');
            $custom_input_5_label = get_option('custom_input_5_label', '');
            if ($custom_input_label != '') {
            ?>
                <div class="col">
                    <div class="form-floating">
                        <input type="text" id="custom_input" name="custom_input" class="form-control" placeholder="Custom input">
                        <label for="custom_input" class="form-label"><?php echo get_option('custom_input_label', 'Default Custom Input Label'); ?>:</label>
                    </div>
                </div>
            <?php }
            if ($custom_input_2_label != '') {
            ?>
                <div class="col">
                    <div class="form-floating">
                        <input type="text" id="custom_input_2" name="custom_input_2" class="form-control" placeholder="Batch number" required>
                        <label for="pallet_id" class="form-label"><?php echo get_option('custom_input_2_label', 'Default Custom Input 2 Label'); ?>:</label>
                    </div>
                </div>
            <?php }
            ?>
        </div>
        <div class="row">
            <?php
            if ($custom_input_3_label != '') {
            ?>
                <div class="col">
                    <div class="form-floating">
                        <input type="text" id="custom_input_3" name="custom_input_3" class="form-control" placeholder="<?php echo get_option('custom_input_3_label', 'Default Custom Input 3 Label'); ?>">
                        <label for="custom_input_3" class="form-label"><?php echo get_option('custom_input_3_label', 'Default Custom Input 3 Label'); ?>:</label>
                    </div>
                </div>
            <?php }
            if ($custom_input_4_label != '') {
            ?>
                <div class="col">
                    <div class="form-floating">
                        <input type="text" id="custom_input_4" name="custom_input_4" class="form-control" placeholder="<?php echo get_option('custom_input_4_label', 'Default Custom Input 4 Label'); ?>" required>
                        <label for="custom_input_4" class="form-label"><?php echo get_option('custom_input_4_label', 'Default Custom Input 4 Label'); ?>:</label>
                    </div>
                </div>
            <?php }
            if ($custom_input_5_label != '') {
            ?>
                <div class="col">
                    <div class="form-floating">
                        <input type="text" id="custom_input_5" name="custom_input_5" class="form-control" placeholder="<?php echo get_option('custom_input_5_label', 'Default Custom Input 5 Label'); ?>" required>
                        <label for="custom_input_5" class="form-label"><?php echo get_option('custom_input_5_label', 'Default Custom Input 5 Label'); ?>:</label>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-floating">
                    <?php $required = (get_option('enable_expiry_date') == "on") ? "required" : ""; ?>
                    <input type="date" id="expiry_date" name="expiry_date" class="form-control" placeholder="Expiry date" <?php echo $required ?>>
                    <label for="expiry_date" class="form-label">Expiry date:</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-floating">
                    <select class="form-select" id="bay" name="bay" aria-label="Default select example" placeholder="Select a bay" required>
                        <option selected disabled>Select a bay</option>
                        <?php

                        $bays_tbl = $wpdb->prefix . 'dwm_bays';
                        $sql = "SELECT * FROM $bays_tbl WHERE warehouse_id = $warehouse_id AND type_id = 1";

                        $bay_results = $wpdb->get_results($sql);
                        echo "<pre>";
                        print_r($bay_results);
                        echo "</pre>";

                        if ($bay_results) {
                            foreach ($bay_results as $bay) {
                                echo '<option value="' . $bay->id . '">' . $bay->name . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <label for="bay" class="form-label">Receiving Bay:</label>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <div class="form-floating">
                    <input type="number" id="quantity" name="quantity" class="form-control" placeholder="Quantity" required>
                    <label for="quantity" class="form-label">Quantity:</label>
                </div>
            </div>
        </div>



        <div class="row">
            <div class="col">
                <div class="form-floating">
                    <input type="number" id="amount_of_pallets" name="amount_of_pallets" class="form-control" placeholder="Amount of pallets">
                    <label for="amount_of_pallets" class="form-label">Amount of pallets:</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating">
                    <select id="rack_all" name="rack_all" class="form-control" placeholder="Select a rack" required></select>
                    <label for="rack_all" class="form-label">Select a rack:</label>
                </div>
            </div>
            <div class="col">
                <div class="form-floating">
                    <select id="level_all" name="level_all" class="form-control" placeholder="Select a level" required></select>
                    <label for="level_all" class="form-label">Select a level:</label>
                </div>
            </div>
        </div>

        <div id="pdf">
            <div id="dynamic-rows-container">
                <!-- Rows will be generated here -->
            </div>
        </div>
        <input type="hidden" name="warehouse_id" value="<?php echo $warehouse_id ?>">
        <input type="submit" name="submit_good_receiving" class="btn btn-primary" value="Submit" />
    </form>

<?php
    return ob_get_clean();
}
add_shortcode('dwm_goods_receiving_form', 'dwm_goods_receiving_form');


function handle_goods_receiving_submission()
{
    if (isset($_POST['submit_good_receiving'])) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'dwm_goods_received';
        $all_inserts_successful = true; // Flag to track if all inserts are successful

        $randomCode = generateRandomCode('ML-');

        for ($i = 0; $i < $_POST['amount_of_pallets']; $i++) {

            $product_name = sanitize_text_field($_POST['product_name']);
            $expiry_date = sanitize_text_field($_POST['expiry_date']);
            $amount_of_pallets = sanitize_text_field($_POST['amount_of_pallets']);
            $custom_input = sanitize_text_field($_POST['custom_input']);
            $custom_input_2 = sanitize_text_field($_POST['custom_input_2']);
            $quantity = sanitize_text_field($_POST['quantity']);
            $pallet_id = sanitize_text_field($_POST['pallet_id'][$i]);
            $amount_of_bags = sanitize_text_field($_POST['amount_of_bags'][$i]);
            $bag_total = sanitize_text_field($_POST['amount_of_bags'][$i]);
            $rack = sanitize_text_field($_POST['rack'][$i]);
            $level = sanitize_text_field($_POST['level'][$i]);
            $position = sanitize_text_field($_POST['position'][$i]);
            $warehouse = sanitize_text_field($_POST['warehouse_id']);
            $bay = sanitize_text_field($_POST['bay']);
            $bin_id = $rack . "-" . $level . "-" . $position;
            $bin_status = 2;

            $data = array(
                'product_name' => $product_name,
                'expiry_date' => $expiry_date,
                'amount_of_pallets' => $amount_of_pallets,
                'custom_input' => $custom_input,
                'custom_input_2' => $custom_input_2,
                'quantity' => $quantity,
                'pallet_id' => $pallet_id,
                'amount_of_bags' => $amount_of_bags,
                'bag_total' => $bag_total,
                'rack' => $rack,
                'level' => $level,
                'position' => $position,
                'bin_id' => $bin_id,
                'bin_status' => $bin_status,
                'warehouse_id' => $warehouse,
                'movement_list_id' => $randomCode,
                'bay_id' => $bay
            );

            $data_movement_list_items = array(
                'movement_list_id' => $randomCode,
                'expiry_date' => $expiry_date,
                'amount_of_pallets' => $amount_of_pallets,
                'custom_input' => $custom_input,
                'custom_input_2' => $custom_input_2,
                'quantity' => $quantity,
                'pallet_id' => $pallet_id,
                'amount_of_bags' => $amount_of_bags,
                'bag_total' => $bag_total,
                'rack' => $rack,
                'level' => $level,
                'position' => $position,
                'bin_id' => $bin_id,
                'bin_status' => $bin_status,
                'warehouse_id' => $warehouse,
            );

            $movement_list_items_table_name = $wpdb->prefix . 'dwm_movement_list_items';

            $result = $wpdb->insert($table_name, $data);

            if ($result === false) {
                // An error occurred during the insert.

                $all_inserts_successful = false;

                set_transient('submission_message', 'Products submission has failed', 10);
            } else {
                // Insert was successful.


                // Create a new post in the 'bins' custom post type with a title
                $bin_title = $bin_id;

                // Set up the post data
                $new_bin = array(
                    'post_title'    => $bin_title,
                    'post_type'     => 'bins', // Your custom post type name
                    'post_status'   => 'publish', // You can use 'draft' or 'publish'
                );

                // Insert the post into the database
                $new_bin_id = wp_insert_post($new_bin);

                if ($new_bin_id) {

                    // Post created successfully, now add the warehouse ID as post meta
                    add_post_meta($new_bin_id, 'warehouse_id', $warehouse, true);
                } else {
                    // Failed to create the post
                    echo "Failed to create the 'bins' post.";
                }
            }
        }

        // If all inserts were successful, then display a message
        if ($all_inserts_successful) {
            // Set a session or cookie variable to show a success message
            set_transient('submission_message', 'Products have successfully reserved for the warehouse', 10);
            $warehouse_administrator = get_post_meta(get_the_ID(), 'warehouse_administrator', true);

            if (!empty($warehouse_administrator)) {
                $user = get_userdata($warehouse_administrator);
                $to = $user->user_email;
            } else {
                $to = 'tevinhendricks16@gmail.com';
            }
            $subject = "Movement list has been created";
            $message = "<p>{$randomCode} has been created and is open</p>";
            $link = site_url() . "/create-labels/?ml={$randomCode}&wh={$warehouse}";
            my_plugin_send_email($to, $subject, $message, $link, "Print Barcodes");

            $data_ml = array(
                'movement_list_id' => $randomCode,
                'created_date' => date("Y-m-d H:i:s"),
                'bay' => 1,
                'movement_status' => 1,
                'warehouse_id' => $warehouse,
                'created_by' => get_current_user_id(),
            );

            $movement_list_table_name = $wpdb->prefix . 'dwm_movement_list';


            $wpdb->insert($movement_list_table_name, $data_ml);

            for ($i = 0; $i < $_POST['amount_of_pallets']; $i++) {
                $rack = sanitize_text_field($_POST['rack'][$i]);
                $level = sanitize_text_field($_POST['level'][$i]);
                $position = sanitize_text_field($_POST['position'][$i]);
                $bin_id = $rack . "-" . $level . "-" . $position;
                $data_movement_list_items = array(
                    'movement_list_id' => $randomCode,
                    'bin_id' => $bin_id,
                );

                $movement_list_items_table_name = $wpdb->prefix . 'dwm_movement_list_items';

                $result = $wpdb->insert($movement_list_items_table_name, $data_movement_list_items);

                if ($result === false) {
                    echo "Movement list could not be created";
                }
            }

            // Get the title of the post
            $title = get_post($warehouse)->post_title;
            $slug = sanitize_title($title); // Convert title to slug

            // Construct the URL for the manage warehouse page in admin
            $url = admin_url('admin.php?page=manage_warehouse&post_id=' . $warehouse);

            // Redirect to the manage warehouse page
            wp_redirect($url);
            exit; // Always call exit after wp_redirect

        }
    }
}
add_action('init', 'handle_goods_receiving_submission');


function handle_picking_list()
{
    if (isset($_POST['submit_picking_list'])) {
    
        global $wpdb;
        $table_goods = $wpdb->prefix . 'dwm_goods_received';

        $picking_list = $_POST['picking_list'];

        foreach($picking_list as $list_item){

            $wpdb->update(
                $table_goods, // The table to update.
                array('bin_status' => 3), // The column to update and the value to set.
                array(
                    'id' => $list_item['goods_received_id'],
                    'bin_id' => $list_item['bin_id'],
                )
            );
        }

        $randomCode = generateRandomCode('PL-');
        $randomCode_movement = generateRandomCode('ML-');

        $table_name = $wpdb->prefix . 'dwm_picking_list';

        $picking_list_id = $randomCode;
        $picking_list_status = 1;
        $warehouse_id = sanitize_text_field($_POST['warehouse_id']);
        $created_by = get_current_user_id();
        $serialized_picking_list = serialize($_POST['picking_list']);
        $reference_number = $_POST['reference_number'];
        $note = $_POST['note'];
        $bay_id = isset($_POST['bay']) ? sanitize_text_field($_POST['bay']) : 0;

        $data = array(
            'picking_list_id' => $picking_list_id,
            'picking_list_status' => $picking_list_status,
            'warehouse_id' => intval($warehouse_id),
            'picking_list' => $serialized_picking_list,
            'created_by' => $created_by,
            'movement_list_id' => $randomCode_movement,
            'reference_number' => $reference_number,
            'note' => $note,
            'date_created' => date("Y-m-d H:i:s"),
        );


        $result = $wpdb->insert($table_name, $data);

        if ($result === false) {
            echo "Picking list could not be created";
        } else {
            set_transient('submission_message', 'Picking list has successfully been created', 10);

            $data_ml = array(
                'movement_list_id' => $randomCode_movement,
                'created_date' => date("Y-m-d H:i:s"),
                'bay' => $bay_id,
                'movement_status' => 1,
                'warehouse_id' => $warehouse_id,
                'created_by' => get_current_user_id(),
            );

            $movement_list_table_name = $wpdb->prefix . 'dwm_movement_list';


            $result_ml = $wpdb->insert($movement_list_table_name, $data_ml);

            if ($result_ml === false) {
                echo "Movement list could not be created";
            } else {
                foreach ($_POST['picking_list'] as $pl_item) {
                    $data_movement_list_items = array(
                        'movement_list_id' => $randomCode_movement,
                        'bin_id' => $pl_item['bin_id'],
                    );

                    $movement_list_items_table_name = $wpdb->prefix . 'dwm_movement_list_items';

                    $result = $wpdb->insert($movement_list_items_table_name, $data_movement_list_items);

                    if ($result === false) {
                        echo "Movement list item could not be created";
                    }
                }
            }
            $warehouse_administrator = get_post_meta($warehouse_id, 'warehouse_administrator', true);

            if (!empty($warehouse_administrator)) {
                $user = get_userdata($warehouse_administrator);
                $to = $user->user_email;
            } else {
                $to = 'tevinhendricks16@gmail.com';
            }
            $subject = "Picking list has been created";
            $message = "<p>{$randomCode} has been created and is open</p>";
            $link = site_url() . "/picking-list/?pl={$randomCode}&wh={$warehouse_id}";
            my_plugin_send_email($to, $subject, $message, $link, "Print Picking list");

            $user_id = get_current_user_id(); // Specify the user ID for which you want to delete the picking list meta data
            $meta_key = 'picking_list'; // Specify the meta key for the picking list


            // Delete the picking list meta data
            delete_user_meta($user_id, $meta_key);
        }
    }
}
add_action('init', 'handle_picking_list');

function generateRandomCode($prefix)
{
    // $prefix = "ML-"; // Fixed prefix
    $randomNumber = mt_rand(10000000, 99999999); // Generates a random number between 10000000 and 99999999

    return $prefix . $randomNumber;
}

function warehouse_svg($racks, $levels_per_rack, $capacities, $post_id)
{

    global $wpdb;

    // Table name
    $table_name = $wpdb->prefix . 'dwm_goods_received';

    $levels_per_rack = explode(',', $levels_per_rack);
    $capacities = explode(',', $capacities);
    $rack_gap = 30; // Gap between racks in pixels
    $p_size = 15; // 15px by 15px
    $total_bins = 0; // Initialize total bins counter
    $max_capacities = max($capacities); // Get the maximum number of bins
    $sum_levels = array_sum($levels_per_rack); // Get the total amount of levels in all racks
    $w_depth = $p_size * $max_capacities; // Set the image height to match the number of capacities
    $w_length = ($sum_levels * $p_size) + ($racks * $rack_gap) - $rack_gap; // Set the image length to match the number of levels and gaps
    // Check if the provided capacities array is valid and has enough elements.
    if (!is_array($capacities) || count($capacities) < $racks) {
        return "Invalid capacities array. Please provide capacities for each rack.";
    }
    // Initialize an empty string to store the SVG content
    $svg = '';
    // Adjust the SVG height to accommodate the rack numbers
    $text_height = 20; // Text height
    $w_depth += $text_height; // Increase the height to create space for the rack numbers
    // Start building the SVG content with the adjusted viewBox
    $svg .= '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 ' . $w_length . ' ' . $w_depth . '" style="enable-background:new 0 0 ' . $w_length . ' ' . $w_depth . ';" xml:space="preserve"><style type="text/css">.level-1 .bin{fill:#777;}.level-2 .bin{fill:#999;}.level-3 .bin{fill:#bbb;}.level-4 .bin{fill:#eee;}.bin{stroke:#ccc; stroke-width: 0.7;}.bin:hover{fill:#007d8c}.rack-number{fill:#999;font-weight:bold;} .filled{fill: #590A3A !important;}</style>';
    // Initialize an initial x-position
    $x_position = 0;
    // Loop for creating racks
    for ($rack_counter = 1; $rack_counter <= $racks; $rack_counter++) {
        $rack_x = $x_position;
        $svg .= '<g class="rack rack-' . $rack_counter . '">';
        // Calculate the x-coordinate for the text element
        $rackTextX = $rack_x + ($levels_per_rack[$rack_counter - 1] * $p_size / 2); // Center the text
        $rackTextY = $w_depth - 2; // Position the text near the bottom of the SVG
        $svg .= '<text class="rack-number" x="' . $rackTextX . '" y="' . $rackTextY . '" text-anchor="middle">' . $rack_counter . '</text>';
        $num_levels = $levels_per_rack[$rack_counter - 1];
        // Loop for creating levels within the rack
        for ($level_counter = 1; $level_counter <= $num_levels; $level_counter++) {
            $level_x = $rack_x + ($level_counter - 1) * $p_size;
            $svg .= '<g class="level level-' . $level_counter . '">';
            // Get the number of bins for the current rack from the capacities array
            $capacity_index = $rack_counter - 1; // Adjusted index
            if (isset($capacities[$capacity_index])) {
                $bins_per_level = $capacities[$capacity_index];
            } else {
                $bins_per_level = 0; // Set to 0 if capacity is not provided
            }
            // Loop for creating bins within the level
            for ($position_counter = 1; $position_counter <= $bins_per_level; $position_counter++) {
                $x = $level_x;
                $y = ($position_counter - 1) * $p_size;
                $id =  $rack_counter . '-' . $level_counter . '-' . $position_counter;
                $warehouse_id = $post_id;
                $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE bin_id = '$id' AND warehouse_id = $warehouse_id");
                $row = $wpdb->get_row($sql);
                if ($row && $row->bin_status == 1) {
                    $svg .= '<a data-bs-toggle="tooltip" onclick="myFunction(\'' . $id . '\',' . $warehouse_id . ')" data-placement="top" title="' . $id . '"><rect x="' . $x . '" y="' . $y . '" class="bin filled" width="' . $p_size . '" height="' . $p_size . '" id="' . $id . '" /></a>';
                } else if ($row && $row->bin_status == 2) {
                    $svg .= '<a data-bs-toggle="tooltip" onclick="myFunction(\'' . $id . '\',' . $warehouse_id . ')" data-placement="top" title="' . $id . '"><rect x="' . $x . '" y="' . $y . '" class="bin reserved" width="' . $p_size . '" height="' . $p_size . '" id="' . $id . '" /></a>';
                } else if ($row && $row->bin_status == 3) {
                    $svg .= '<a data-bs-toggle="tooltip" onclick="myFunction(\'' . $id . '\',' . $warehouse_id . ')" data-placement="top" title="' . $id . '"><rect x="' . $x . '" y="' . $y . '" class="bin locked" width="' . $p_size . '" height="' . $p_size . '" id="' . $id . '" /></a>';
                } else if ($row && $row->bin_status == 4) {
                    $svg .= '<a data-bs-toggle="tooltip" onclick="myFunction(\'' . $id . '\',' . $warehouse_id . ')" data-placement="top" title="' . $id . '"><rect x="' . $x . '" y="' . $y . '" class="bin" width="' . $p_size . '" height="' . $p_size . '" id="' . $id . '" /></a>';
                } else {
                    $svg .= '<a data-bs-toggle="tooltip" onclick="myFunction(\'' . $id . '\',' . $warehouse_id . ')" data-placement="top" title="' . $id . '"><rect x="' . $x . '" y="' . $y . '" class="bin" width="' . $p_size . '" height="' . $p_size . '" id="/' . $id . '" /></a>';
                }
            }
            $total_bins += $bins_per_level; // Update the total bins counter
            $svg .= '</g>';
        }
        $svg .= '</g>';
        // Increment the x-position with the rack gap
        $x_position += $num_levels * $p_size + $rack_gap;
    }
    $svg .= '</svg>';

    $bins_total = get_post_meta($post_id, 'total_bins', true);

    if (empty($bins_total)) {
        add_post_meta($post_id, 'total_bins', $total_bins, true);
    }


    return $svg;
}

function warehouse_svg_layout($racks, $levels_per_rack, $capacities, $post_id)
{

    $levels_per_rack = explode(',', $levels_per_rack);
    $capacities = explode(',', $capacities);
    $rack_gap = 30; // Gap between racks in pixels
    $p_size = 15; // 15px by 15px
    $total_bins = 0; // Initialize total bins counter
    $max_capacities = max($capacities); // Get the maximum number of bins
    $sum_levels = array_sum($levels_per_rack); // Get the total amount of levels in all racks
    $w_depth = $p_size * $max_capacities; // Set the image height to match the number of capacities
    $w_length = ($sum_levels * $p_size) + ($racks * $rack_gap) - $rack_gap; // Set the image length to match the number of levels and gaps
    // Check if the provided capacities array is valid and has enough elements.
    if (!is_array($capacities) || count($capacities) < $racks) {
        return "Invalid capacities array. Please provide capacities for each rack.";
    }
    // Initialize an empty string to store the SVG content
    $svg = '';
    // Adjust the SVG height to accommodate the rack numbers
    $text_height = 20; // Text height
    $w_depth += $text_height; // Increase the height to create space for the rack numbers
    // Start building the SVG content with the adjusted viewBox
    $svg .= '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 ' . $w_length . ' ' . $w_depth . '" style="enable-background:new 0 0 ' . $w_length . ' ' . $w_depth . ';" xml:space="preserve"><style type="text/css">.level-1 .bin{fill:#777;}.level-2 .bin{fill:#999;}.level-3 .bin{fill:#bbb;}.level-4 .bin{fill:#eee;}.bin{stroke:#ccc; stroke-width: 0.7;}.bin:hover{fill:#007d8c}.rack-number{fill:#999;font-weight:bold;} .filled{fill: #590A3A !important;}</style>';
    // Initialize an initial x-position
    $x_position = 0;
    // Loop for creating racks
    for ($rack_counter = 1; $rack_counter <= $racks; $rack_counter++) {
        $rack_x = $x_position;
        $svg .= '<g class="rack rack-' . $rack_counter . '">';
        // Calculate the x-coordinate for the text element
        $rackTextX = $rack_x + ($levels_per_rack[$rack_counter - 1] * $p_size / 2); // Center the text
        $rackTextY = $w_depth - 2; // Position the text near the bottom of the SVG
        $svg .= '<text class="rack-number" x="' . $rackTextX . '" y="' . $rackTextY . '" text-anchor="middle">' . $rack_counter . '</text>';
        $num_levels = $levels_per_rack[$rack_counter - 1];
        // Loop for creating levels within the rack
        for ($level_counter = 1; $level_counter <= $num_levels; $level_counter++) {
            $level_x = $rack_x + ($level_counter - 1) * $p_size;
            $svg .= '<g class="level level-' . $level_counter . '">';
            // Get the number of bins for the current rack from the capacities array
            $capacity_index = $rack_counter - 1; // Adjusted index
            if (isset($capacities[$capacity_index])) {
                $bins_per_level = $capacities[$capacity_index];
            } else {
                $bins_per_level = 0; // Set to 0 if capacity is not provided
            }
            // Loop for creating bins within the level
            for ($position_counter = 1; $position_counter <= $bins_per_level; $position_counter++) {
                $x = $level_x;
                $y = ($position_counter - 1) * $p_size;
                $id =  $rack_counter . '-' . $position_counter . '-' . $level_counter;
                $svg .= '<a data-bs-toggle="tooltip" data-placement="top" title="' . $id . '"><rect x="' . $x . '" y="' . $y . '" class="bin" width="' . $p_size . '" height="' . $p_size . '" id="/' . $id . '" /></a>';
            }
            $total_bins += $bins_per_level; // Update the total bins counter
            $svg .= '</g>';
        }
        $svg .= '</g>';
        // Increment the x-position with the rack gap
        $x_position += $num_levels * $p_size + $rack_gap;
    }
    $svg .= '</svg>';

    echo $svg;
}

// Hook into the 'add_meta_boxes' action
add_action('add_meta_boxes', 'add_warehouse_id_metabox');

function add_warehouse_id_metabox()
{
    // Add a meta box to the 'bins' post type
    add_meta_box(
        'warehouse_id_metabox',       // Unique ID for the meta box
        __('Warehouse ID', 'textdomain'),  // Title of the meta box
        'warehouse_id_metabox_callback',   // Callback function
        'bins',                      // Admin page (or post type)
        'side',                        // Context
        'default'                      // Priority
    );
}

// Display the meta box content
function warehouse_id_metabox_callback($post)
{
    // Use nonce for verification
    wp_nonce_field(basename(__FILE__), 'warehouse_id_nonce');

    // Get the warehouse_id data if it's already been entered
    $warehouse_id = get_post_meta($post->ID, 'warehouse_id', true);

    // Output the field
    echo '<p>' . __('Warehouse ID:', 'textdomain') . '</p>';
    echo '<p>' . esc_attr($warehouse_id) . '</p>';
}

function my_plugin_custom_post_type_template($template)
{
    if (is_post_type_archive('warehouses')) {
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/archive-warehouses.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter('template_include', 'my_plugin_custom_post_type_template');


// send_custom_email();
function my_plugin_send_email($to, $subject, $message, $link = "", $link_text = "")
{
    $headers = array('Content-Type: text/html; charset=UTF-8');

    $primary = get_option('bs-primary');
    $secondary = get_option('bs-secondary-color');

    $post_id = $_GET['post_id'];
    $title = get_the_title();
    // Get the custom field values
    $address = get_post_meta($post_id, 'warehouse_address', true);

    $email_body = '<!doctype html>
    <html lang="en">
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>Simple Transactional Email</title>
            <style media="all" type="text/css">
                /* -------------------------------------
                GLOBAL RESETS
                ------------------------------------- */
                
                body {
                    font-family: Helvetica, sans-serif;
                    -webkit-font-smoothing: antialiased;
                    font-size: 16px;
                    line-height: 1.3;
                    -ms-text-size-adjust: 100%;
                    -webkit-text-size-adjust: 100%;
                }
                
                table {
                    border-collapse: separate;
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                    width: 100%;
                    margin:0;
                    padding:0!important;
                }
                table, td, tr{
                    border:none;
                    padding:0;
                }
                
                table td {
                    font-family: Helvetica, sans-serif;
                    font-size: 16px;
                    vertical-align: top;
                }
                /* -------------------------------------
                BODY & CONTAINER
                ------------------------------------- */
                
                body {
                    background-color: #f4f5f6;
                    margin: 0;
                    padding: 0;
                }
                
                .body {
                    background-color: #f4f5f6;
                    width: 100%;
                }
                
                .container-email {
                    margin: 0 auto !important;
                    max-width: 600px;
                    padding: 0;
                    padding-top: 24px;
                    width: 600px;
                }
                
                .content {
                    box-sizing: border-box;
                    display: block;
                    margin: 0 auto;
                    max-width: 600px;
                    padding: 0;
                }
                .message{
                    padding-bottom: 24px!important;
                }
                .container-email img{
                    max-width:50%;
                }
                /* -------------------------------------
                HEADER, FOOTER, MAIN
                ------------------------------------- */
                
                .main {
                    background: #ffffff;
                    border: 1px solid #eaebed;
                    border-radius: 16px;
                    width: 100%;
                }
                
                .wrapper-email {
                    box-sizing: border-box;
                    padding: 24px!important;
                }
                
                .footer, .header-email {
                    clear: both;
                    padding: 24px 0;
                    text-align: center;
                    width: 100%;
                }
                
                .footer td,
                .footer p,
                .footer span,
                .footer a {
                    color: #9a9ea6;
                    text-align: center;
                }
                .powered-by{
                    padding-top: 10px;
                }
                /* -------------------------------------
                TYPOGRAPHY
                ------------------------------------- */
                
                p {
                    font-family: Helvetica, sans-serif;
                    font-size: 16px;
                    font-weight: normal;
                    margin: 0;
                    margin-bottom: 16px;
                }
                
                a {
                    color: ' . $primary . ';
                    text-decoration: none;
                }
                /* -------------------------------------
                BUTTONS
                ------------------------------------- */
                
                .btn-email {
                    box-sizing: border-box;
                    min-width: 100% !important;
                    width: 100%;
                }
                
                .btn-email > tbody > tr > td {
                    padding-bottom: 16px;
                }
                
                .btn-email table {
                    width: auto;
                }
                
                .btn-email table td {
                    background-color: #ffffff;
                    border-radius: 4px;
                    text-align: center;
                }
                
                .btn-email a {
                    background-color: #ffffff;
                    border: solid 2px ' . $primary . ';
                    border-radius: 4px;
                    box-sizing: border-box;
                    color: #0867ec;
                    cursor: pointer;
                    display: inline-block;
                    font-size: 16px;
                    font-weight: bold;
                    margin: 0;
                    padding: 12px 24px;
                    text-decoration: none;
                    text-transform: capitalize;
                }
                
                .btn-primary-email table td {
                    background-color: ' . $primary . ';
                }
                
                .btn-primary-email a {
                    background-color: ' . $primary . ';
                    border-color: ' . $primary . ';
                    color: #ffffff;
                }
                
                @media all {
                    .btn-primary-email table td:hover {
                        background-color: ' . $primary . ' !important;
                    }
                    .btn-primary-email a:hover {
                        background-color: ' . $primary . ' !important;
                        border-color: ' . $primary . ' !important;
                    }
                }
                
                /* -------------------------------------
                RESPONSIVE AND MOBILE FRIENDLY STYLES
                ------------------------------------- */
                
                @media only screen and (max-width: 640px) {
                    .main p,
                    .main td,
                    .main span {
                        font-size: 16px !important;
                    }
                    .wrapper-email {
                        padding: 8px !important;
                    }
                    .content {
                        padding: 0 !important;
                    }
                    .container-email {
                        padding: 0 !important;
                        padding-top: 8px !important;
                        width: 100% !important;
                    }
                    .main {
                        border-left-width: 0 !important;
                        border-radius: 0 !important;
                        border-right-width: 0 !important;
                    }
                    .btn-email table {
                        max-width: 100% !important;
                        width: 100% !important;
                    }
                    .btn-email a {
                        font-size: 16px !important;
                        max-width: 100% !important;
                        width: 100% !important;
                    }
                }
    
                /* -------------------------------------
                PRESERVE THESE STYLES IN THE HEAD
                ------------------------------------- */
                
                @media all {
                    .ExternalClass {
                        width: 100%;
                    }
                    .ExternalClass,
                    .ExternalClass p,
                    .ExternalClass span,
                    .ExternalClass font,
                    .ExternalClass td,
                    .ExternalClass div {
                        line-height: 100%;
                    }
                    .apple-link a {
                        color: inherit !important;
                        font-family: inherit !important;
                        font-size: inherit !important;
                        font-weight: inherit !important;
                        line-height: inherit !important;
                        text-decoration: none !important;
                    }
                    #MessageViewBody a {
                        color: inherit;
                        text-decoration: none;
                        font-size: inherit;
                        font-family: inherit;
                        font-weight: inherit;
                        line-height: inherit;
                    }
                }
            </style>
        </head>
        <body>
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
                <tr>
                    <td>&nbsp;</td>
                    <td class="container-email">
                        <div class="content">
                            <!-- START CENTERED WHITE CONTAINER -->
                            <!-- START HEADER -->
                            <div class="header-email">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="content-block">
                                            <!-- Add a place for you to paste a custom image URL to add a logo -->
                                            <img src="https://staging.chemunique.co.za/wp-racks/wp-content/uploads/2024/02/CMQ-Logo-Teal.svg">
                                        </td>
                                    </tr>
                                    
                                </table>
                            </div>
                            <!-- END HEADER -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="main">
                                <!-- START MAIN CONTENT AREA -->
                                <tr>
                                    <td class="wrapper-email">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="message">
                                            <tbody>
                                                <tr>
                                                    <td align="left">
                                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                            <tbody>
                                                                <tr>
                                                                    <td>';
    $email_body .= $message;
    $email_body .= ' 
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>';

    if ($link != "") {
        $email_body .= '<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn-email btn-primary-email">
                                            <tbody>
                                                <tr>
                                                    <td align="left">
                                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                        <tr>
                                                            <td> <a href="' . $link . '" target="_blank">' . $link_text . '</a> </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>';
    }

    $email_body .= '<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="">
                                            <tbody>
                                                <tr>
                                                    <td align="left">
                                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <p>Kind regards,</p>
                                                                        <p><strong>' . $title . '</strong></p>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <!-- END MAIN CONTENT AREA -->
                            </table>
                            <!-- START FOOTER -->
                            <div class="footer">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="content-block">
                                            <span class="apple-link"><strong>' . $title . '</strong></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="content-block">
                                            <span class="apple-link">' . $address . '</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="content-block powered-by">
                                            <small>Powered by <strong><a href="htts://wpracks.com">WP Racks - Warehouse Management</a></strong></small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <!-- END FOOTER -->
                            <!-- END CENTERED WHITE CONTAINER -->
                        </div>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </body>
    </html>
    ';


    $sent = wp_mail($to, $subject, $email_body, $headers);


    if (!$sent) {
        // Handle the error. You can log it or send a notification.
        error_log('Email sending failed.');
    } else {
        // The email was sent successfully
        error_log('Email sent successfully.');
    }
}

function send_email_shortcode($atts)
{

    $atts = shortcode_atts(array(
        'test' => false,
        "link" => ""
    ), $atts, 'warehouse');

    if ($atts['test'] == true) {
        $message = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime mollitia,
        molestiae quas vel sint commodi repudiandae consequuntur voluptatum laborum
        numquam blanditiis harum quisquam eius sed odit fugiat iusto fuga praesentium
        optio, eaque rerum! Provident similique accusantium nemo autem. Veritatis
        obcaecati tenetur iure eius earum ut molestias architecto voluptate aliquam
        nihil, eveniet aliquid culpa officia aut! Impedit sit sunt quaerat, odit,
        tenetur error, harum nesciunt ipsum debitis quas aliquid. Reprehenderit,
        quia. Quo neque error repudiandae fuga? Ipsa laudantium molestias eos 
        sapiente officiis modi at sunt excepturi expedita sint? Sed quibusdam
        recusandae alias error harum maxime adipisci amet laborum.";
    }
    $headers = array('Content-Type: text/html; charset=UTF-8');

    $primary = get_option('bs-primary');
    $secondary = get_option('bs-secondary-color');

    $email_body = '<!doctype html>
    <html lang="en">
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <title>Simple Transactional Email</title>
            <style media="all" type="text/css">
                /* -------------------------------------
                GLOBAL RESETS
                ------------------------------------- */
                
                body {
                    font-family: Helvetica, sans-serif;
                    -webkit-font-smoothing: antialiased;
                    font-size: 16px;
                    line-height: 1.3;
                    -ms-text-size-adjust: 100%;
                    -webkit-text-size-adjust: 100%;
                }
                
                table {
                    border-collapse: separate;
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                    width: 100%;
                    margin:0;
                    padding:0!important;
                }
                table, td, tr{
                    border:none;
                    padding:0;
                }
                
                table td {
                    font-family: Helvetica, sans-serif;
                    font-size: 16px;
                    vertical-align: top;
                }
                /* -------------------------------------
                BODY & CONTAINER
                ------------------------------------- */
                
                body {
                    background-color: #f4f5f6;
                    margin: 0;
                    padding: 0;
                }
                
                .body {
                    background-color: #f4f5f6;
                    width: 100%;
                }
                
                .container-email {
                    margin: 0 auto !important;
                    max-width: 600px;
                    padding: 0;
                    padding-top: 24px;
                    width: 600px;
                }
                
                .content {
                    box-sizing: border-box;
                    display: block;
                    margin: 0 auto;
                    max-width: 600px;
                    padding: 0;
                }
                .message{
                    padding-bottom: 24px!important;
                }
                .container-email img{
                    max-width:50%;
                }
                /* -------------------------------------
                HEADER, FOOTER, MAIN
                ------------------------------------- */
                
                .main {
                    background: #ffffff;
                    border: 1px solid #eaebed;
                    border-radius: 16px;
                    width: 100%;
                }
                
                .wrapper-email {
                    box-sizing: border-box;
                    padding: 24px!important;
                }
                
                .footer, .header-email {
                    clear: both;
                    padding: 24px 0;
                    text-align: center;
                    width: 100%;
                }
                
                .footer td,
                .footer p,
                .footer span,
                .footer a {
                    color: #9a9ea6;
                    text-align: center;
                }
                .powered-by{
                    padding-top: 10px;
                }
                /* -------------------------------------
                TYPOGRAPHY
                ------------------------------------- */
                
                p {
                    font-family: Helvetica, sans-serif;
                    font-size: 16px;
                    font-weight: normal;
                    margin: 0;
                    margin-bottom: 16px;
                }
                
                a {
                    color: ' . $primary . ';
                    text-decoration: none;
                }
                /* -------------------------------------
                BUTTONS
                ------------------------------------- */
                
                .btn-email {
                    box-sizing: border-box;
                    min-width: 100% !important;
                    width: 100%;
                }
                
                .btn-email > tbody > tr > td {
                    padding-bottom: 16px;
                }
                
                .btn-email table {
                    width: auto;
                }
                
                .btn-email table td {
                    background-color: #ffffff;
                    border-radius: 4px;
                    text-align: center;
                }
                
                .btn-email a {
                    background-color: #ffffff;
                    border: solid 2px ' . $primary . ';
                    border-radius: 4px;
                    box-sizing: border-box;
                    color: #0867ec;
                    cursor: pointer;
                    display: inline-block;
                    font-size: 16px;
                    font-weight: bold;
                    margin: 0;
                    padding: 12px 24px;
                    text-decoration: none;
                    text-transform: capitalize;
                }
                
                .btn-primary-email table td {
                    background-color: ' . $primary . ';
                }
                
                .btn-primary-email a {
                    background-color: ' . $primary . ';
                    border-color: ' . $primary . ';
                    color: #ffffff;
                }
                
                @media all {
                    .btn-primary-email table td:hover {
                        background-color: ' . $primary . ' !important;
                    }
                    .btn-primary-email a:hover {
                        background-color: ' . $primary . ' !important;
                        border-color: ' . $primary . ' !important;
                    }
                }
                
                /* -------------------------------------
                RESPONSIVE AND MOBILE FRIENDLY STYLES
                ------------------------------------- */
                
                @media only screen and (max-width: 640px) {
                    .main p,
                    .main td,
                    .main span {
                        font-size: 16px !important;
                    }
                    .wrapper-email {
                        padding: 8px !important;
                    }
                    .content {
                        padding: 0 !important;
                    }
                    .container-email {
                        padding: 0 !important;
                        padding-top: 8px !important;
                        width: 100% !important;
                    }
                    .main {
                        border-left-width: 0 !important;
                        border-radius: 0 !important;
                        border-right-width: 0 !important;
                    }
                    .btn-email table {
                        max-width: 100% !important;
                        width: 100% !important;
                    }
                    .btn-email a {
                        font-size: 16px !important;
                        max-width: 100% !important;
                        width: 100% !important;
                    }
                }
    
                /* -------------------------------------
                PRESERVE THESE STYLES IN THE HEAD
                ------------------------------------- */
                
                @media all {
                    .ExternalClass {
                        width: 100%;
                    }
                    .ExternalClass,
                    .ExternalClass p,
                    .ExternalClass span,
                    .ExternalClass font,
                    .ExternalClass td,
                    .ExternalClass div {
                        line-height: 100%;
                    }
                    .apple-link a {
                        color: inherit !important;
                        font-family: inherit !important;
                        font-size: inherit !important;
                        font-weight: inherit !important;
                        line-height: inherit !important;
                        text-decoration: none !important;
                    }
                    #MessageViewBody a {
                        color: inherit;
                        text-decoration: none;
                        font-size: inherit;
                        font-family: inherit;
                        font-weight: inherit;
                        line-height: inherit;
                    }
                }
            </style>
        </head>
        <body>
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
                <tr>
                    <td>&nbsp;</td>
                    <td class="container-email">
                        <div class="content">
                            <!-- START CENTERED WHITE CONTAINER -->
                            <!-- START HEADER -->
                            <div class="header-email">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="content-block">
                                            <!-- Add a place for you to paste a custom image URL to add a logo -->
                                            <img src="https://wpracks.com/wp-content/uploads/2024/01/wp-racks-02-1.png">
                                        </td>
                                    </tr>
                                    
                                </table>
                            </div>
                            <!-- END HEADER -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="main">
                                <!-- START MAIN CONTENT AREA -->
                                <tr>
                                    <td class="wrapper-email">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="message">
                                            <tbody>
                                                <tr>
                                                    <td align="left">
                                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                            <tbody>
                                                                <tr>
                                                                    <td>';
    $email_body .= $message;
    $email_body .= ' 
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn-email btn-primary-email">
                                            <tbody>
                                                <tr>
                                                    <td align="left">
                                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                        <tr>
                                                            <td> <a href="http://htmlemail.io" target="_blank">Call To Action</a> </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="">
                                            <tbody>
                                                <tr>
                                                    <td align="left">
                                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <p>Kind regards,</p>
                                                                        <p><strong>!! Input current warehouse name !!</strong></p>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <!-- END MAIN CONTENT AREA -->
                            </table>
                            <!-- START FOOTER -->
                            <div class="footer">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="content-block">
                                            <span class="apple-link"><strong>!! Input current warehouse name !!</strong></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="content-block">
                                            <span class="apple-link">!! Input current warehouse address !!</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="content-block powered-by">
                                            <small>Powered by <strong><a href="htts://wpracks.com">WP Racks - Warehouse Management</a></strong></small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <!-- END FOOTER -->
                            <!-- END CENTERED WHITE CONTAINER -->
                        </div>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </body>
    </html>
    ';

    echo $email_body;
}

add_shortcode('email_preview', 'send_email_shortcode');


function csv_upload_shortcode()
{
    ob_start();
?>
    <form method="post" enctype="multipart/form-data">
        <input class="btn btn-secondary" type="file" name="csv_file" accept=".csv">
        <input class="btn btn-primary" type="submit" name="upload_csv" value="Upload CSV">
    </form>

    <?php

    if (isset($_POST['upload_csv'])) {
        if (isset($_FILES['csv_file']) && !empty($_FILES['csv_file']['name'])) {
            $file = $_FILES['csv_file']['tmp_name'];

            // Detect the delimiter
            $delimiter = detect_csv_delimiter($file);
            $products_added = 0;

            if (($handle = fopen($file, "r")) !== false) {
                global $wpdb;


                // Flag to skip the first line
                $firstLine = true;

                while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
                    // Skip the first line (header)
                    // Skip the first line (header)

                    if ($firstLine) {
                        $firstLine = false;
                        continue;
                    }

                    // Extract data from CSV row
                    if ($data[1] == "bulk") {
                        $data[1] = 1000;
                    } else {
                        $data[1] = $data[1];
                    }


                    // Extract data from CSV row
                    if ($data[2] == "kg") {
                        $uom = 1;
                    } else if ($data[2] == "ml") {
                        $uom = 2;
                    } else if ($data[2] == "l") {
                        $uom = 3;
                    } else if ($data[2] == "t") {
                        $uom = 4;
                    } else {
                        $uom = 0;
                    }
                    $product_data = array(
                        'post_title'    => isset($data[0]) ? $data[0] : '',
                        'post_type'     => 'warehouse-products',
                        'post_status'   => 'publish',
                        // Add more meta fields here as needed
                        'meta_input'    => array(
                            'pack_size'     => isset($data[1]) ? $data[1] : '',
                            'pack_unit_id'  => isset($uom) ? $uom : '',
                            'inventory_id'  => isset($data[3]) ? $data[3] : ''
                        )
                    );

                    // Insert product as post
                    $post_id = wp_insert_post($product_data);


                    // Increment products added counter
                    $products_added++;
                }
                fclose($handle);

                // Display success message
                echo '<p>CSV file processed successfully. ' . $products_added . ' products added.</p>';
            } else {
                echo '<h2>Error reading the CSV file.</h2>';
            }
        } else {
            echo '<h2>Please choose a CSV file before clicking "Upload CSV."</h2>';
        }
    }
    return ob_get_clean();
}
add_shortcode('csv_upload', 'csv_upload_shortcode');

// Function to detect the CSV delimiter
function detect_csv_delimiter($file_path)
{
    $delimiters = array(';' => 0, ',' => 0);
    $handle = fopen($file_path, 'r');
    $firstLine = fgets($handle);
    fclose($handle);
    foreach ($delimiters as $delimiter => &$count) {
        $count = count(str_getcsv($firstLine, $delimiter));
    }
    return array_search(max($delimiters), $delimiters);
}





// Register shortcode
add_shortcode('upload_products_csv', 'upload_products_csv_shortcode');

// Shortcode callback function
function upload_products_csv_shortcode()
{
    ob_start();
    ?>
    <div class="upload-products-csv">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="products_csv" accept=".csv">
            <input type="submit" name="submit_csv" value="Upload CSV">
        </form>
    </div>
<?php
    $output = ob_get_clean();

    // Check if form submitted
    if (isset($_POST['submit_csv'])) {
        // Check if file uploaded successfully
        if ($_FILES['products_csv']['error'] == UPLOAD_ERR_OK) {
            // Process CSV file
            $csv_file = $_FILES['products_csv']['tmp_name'];
            $csv_data = array_map('str_getcsv', file($csv_file));

            // Counter to limit to 100 products
            $products_added = 0;

            echo "<pre>";
            print_r($csv_data);
            echo "</pre>";
            die();

            // Loop through CSV rows
            foreach ($csv_data as $row) {
                // Check if 100 products added already
                if ($products_added >= 100) {
                    break;
                }

                // Extract data from CSV row
                $product_data = array(
                    'post_title'    => isset($row[0]) ? $row[0] : '',
                    'post_type'     => 'warehouse-products',
                    'post_status'   => 'publish'
                    // Add more meta fields here as needed
                    // 'meta_input'    => array(
                    //     'pack_size'     => isset($row[1]) ? $row[1] : '',
                    //     'pack_unit_id'  => isset($row[2]) ? $row[2] : '',
                    //     'inventory_id'  => isset($row[3]) ? $row[3] : ''
                    // )
                );

                // Insert product as post
                $post_id = wp_insert_post($product_data);

                // Example of adding custom meta fields
                // Uncomment and modify as needed
                // if (!is_wp_error($post_id)) {
                //     update_post_meta($post_id, 'pack_size', isset($row[1]) ? $row[1] : '');
                //     update_post_meta($post_id, 'pack_unit_id', isset($row[2]) ? $row[2] : '');
                //     update_post_meta($post_id, 'inventory_id', isset($row[3]) ? $row[3] : '');
                // }

                // Increment products added counter
                $products_added++;
            }

            // Display success message
            echo '<p>CSV file processed successfully. ' . $products_added . ' products added.</p>';
        } else {
            // Display error message if file upload failed
            echo '<p>Error uploading CSV file.</p>';
        }
    }

    return $output;
}

//______________________________________________________________________________
// All About Updates

//  Begin Version Control | Auto Update Checker
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
// ***IMPORTANT*** Update this path to New Github Repository Master Branch Path
	'https://github.com/maldersIO/warehouse-manager/', // This is the URL of the Plugin Repo, Update it to reflect your plugin's URL
	__FILE__,
// ***IMPORTANT*** Update this to New Repository Master Branch Path
	'warehouse-manager' // This is the master branch path, just pull the end of your URL, so if https://github.com/maldersIO/warehouse-manager/, the master path is just warehouse-manager
);
//Enable Releases
$myUpdateChecker->getVcsApi()->enableReleaseAssets();
//Optional: If you're using a private repository, specify the access token like this:
//
//
//Future Update Note: Comment in these sections and add token and branch information once private git established
//
//
//$myUpdateChecker->setAuthentication('your-token-here');
//
//Optional: Set the branch that contains the stable release.
//$myUpdateChecker->setBranch('stable-branch-name');

//______________________________________________________________________________
//End Distinct Warehouse Manager