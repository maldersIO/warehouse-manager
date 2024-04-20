<?php

add_action('wp_ajax_get_bin_details', 'get_bin_details');
add_action('wp_ajax_get_search_svg', 'get_search_svg');
add_action('wp_ajax_move_bin', 'move_bin');
add_action('wp_ajax_complete_move', 'complete_move');
add_action('wp_ajax_cancel_move', 'cancel_move');
add_action('wp_ajax_movement_list', 'movement_list');
add_action('wp_ajax_add_bin_to_picking_list', 'add_bin_to_picking_list');
add_action('wp_ajax_remove_bin_from_picking_list', 'remove_bin_from_picking_list');


function get_bin_details()
{
    global $wpdb;

    $bin_id = isset($_POST['bin_id']) ? sanitize_text_field($_POST['bin_id']) : null;
    $warehouse_id = isset($_POST['warehouse_id']) ? sanitize_text_field($_POST['warehouse_id']) : null;

    $table_name = $wpdb->prefix . 'dwm_goods_received';
    $posts = $wpdb->prefix . 'posts';
    $sql = $wpdb->prepare("SELECT goods.*,posts.post_title
    FROM $table_name goods 
    LEFT JOIN $posts posts ON goods.product_name = posts.ID 
    WHERE bin_id = %s AND warehouse_id = $warehouse_id", $bin_id);

    $row = $wpdb->get_row($sql);

    if ($row) {
        $response = array(
            'status' => 'success',
            'bin_status' => $row->bin_status,
            'message' => array(
                'id' => $row->id,
                'product_name' => $row->post_title,
                'batch_number' => $row->custom_input,
                'expiry_date' => $row->expiry_date,
                'pallet_id' => $row->custom_input_2,
                'quantity' => $row->amount_of_bags,
                'sql' => $sql
            )
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => "Bin is empty"
        );
    }

    wp_send_json($response);
}

function move_bin()
{

    $bin_id = isset($_POST['bin_id']) ? sanitize_text_field($_POST['bin_id']) : null;
    $warehouse_id = isset($_POST['warehouse_id']) ? sanitize_text_field($_POST['warehouse_id']) : null;

    global $wpdb;
    $table_name = $wpdb->prefix . 'dwm_goods_received';

    $racks = get_post_meta($warehouse_id, 'racks', true);
    $levels_per_rack = get_post_meta($warehouse_id, 'levels_per_rack', true);
    $capacities = get_post_meta($warehouse_id, 'capacities', true);

    $sql = "SELECT * FROM $table_name WHERE warehouse_id = $warehouse_id;";
    $results = $wpdb->get_results($sql);

    $binArr = array();

    foreach ($results as $res) {
        array_push($binArr, $res->bin_id);
    }

    $bin_ids = implode(",", $binArr);

    ob_start();

    $bin_sql = "SELECT * FROM $table_name WHERE warehouse_id = $warehouse_id AND bin_id = $bin_id;";
    $bin_results = $wpdb->get_row($bin_sql);



    $form = '<form method="post" id="goods-form">
        <!-- Product details -->

        <h2 class="fs-title">Product Details</h2>

        <div class="row">
            <div class="col">
                <div class="form-floating">
                    <label for="product_name" class="form-label">Product name:</label>
                    <input type="number" id="product_name" name="product_name" class="form-control" value="" required disabled>
                </div>
            </div>
        </div>
        <input type="hidden" id="wh_racks" name="wh_racks" value="' . $racks . '">
        <input type="hidden" id="wh_levels_per_rack" name="wh_levels_per_rack" value="' . $levels_per_rack . '">
        <input type="hidden" id="wh_capacities" name="wh_capacities" value="' . $capacities . '">
        <input type="hidden" id="bin_ids" name="bin_ids" value="' . $bin_ids . '">
        <div class="row">
            <div class="col">
                <div class="form-floating">
                    <label for="batch_number" class="form-label">Batch number:</label>
                    <input type="text" id="batch_number" name="batch_number" class="form-control" placeholder="Batch number" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-floating">
                    <label for="expiry_date" class="form-label">Expiry date:</label>
                    <input type="date" id="expiry_date" name="expiry_date" class="form-control" required>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col">
                <div class="form-floating">
                    <label for="quantity" class="form-label">Total Quantity:</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col">
                <div class="form-floating">
                    <label for="pallet_id" class="form-label">Pallet ID:</label>
                    <input type="text" id="pallet_id" name="pallet_id" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-floating">
                    <label for="amount_of_pallets" class="form-label">Amount of pallets:</label>
                    <input type="number" id="amount_of_pallets" name="amount_of_pallets" class="form-control" required>
                </div>
            </div>
            <div class="col">
                <div class="form-floating">
                    <label for="rack_all" class="form-label">Rack:</label>
                    <select id="rack_all" name="rack_all" class="form-control" required></select>
                </div>
            </div>
            <div class="col">
                <div class="form-floating">
                    <label for="level_all" class="form-label">Level:</label>
                    <select id="level_all" name="level_all" class="form-control" required></select>
                </div>
            </div>
        </div>

        <!-- Assign bins -->
        <!-- <h2 class="fs-title">Assign Bins</h2>
        <h3 class="fs-subtitle">Assign a bin for each pallet in the warehouse</h3> -->
        <div id="dynamic-rows-container">
            <!-- Rows will be generated here -->
        </div>
        <input type="hidden" name="warehouse_id" value="<?php echo get_the_ID(); ?>">
        <input type="submit" name="submit_good_receiving" class="submit action-button" value="Submit" />
    </form>';

    if ($form) {
        $response = array(
            'status' => 'success',
            'form' => $form,
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => "No product form in the 'wp_dwm_goods_received' table.",
        );
    }


    wp_send_json($response);
}

function movement_list()
{

    $movement_list_id = isset($_POST['movement_list_id']) ? sanitize_text_field($_POST['movement_list_id']) : null;
    $picking_list_id = isset($_POST['picking_list_id']) ? sanitize_text_field($_POST['picking_list_id']) : null;
    $warehouse_id = isset($_POST['warehouse_id']) ? sanitize_text_field($_POST['warehouse_id']) : null;

    global $wpdb;
    $table_name = $wpdb->prefix . 'dwm_movement_list';
    $table_goods = $wpdb->prefix . 'dwm_goods_received';
    $table_picking_list = $wpdb->prefix . 'dwm_picking_list';
    $table_bays = $wpdb->prefix . 'dwm_bays';
    $posts = $wpdb->prefix . 'posts';
    $users = $wpdb->prefix . 'users';

    if ($picking_list_id) {

        $sql = "SELECT *,
        (SELECT display_name FROM $users WHERE ID = ml.created_by) AS created_by,
        (SELECT display_name FROM $users WHERE ID = ml.confirmed_by) AS confirmed_by
            FROM $table_picking_list pl
            LEFT JOIN $table_name ml ON ml.movement_list_id = pl.movement_list_id
            WHERE pl.movement_list_id = '$movement_list_id' AND ml.warehouse_id= '$warehouse_id';";

        $rows = $wpdb->get_results($sql);

        $pickingListArr = array();

        $sql_picking_list = "SELECT * FROM $table_picking_list WHERE picking_list_id = '$picking_list_id';";
        $db_result = $wpdb->get_row($sql_picking_list);
        $picking_list = unserialize($db_result->picking_list);

        foreach ($picking_list as $list) {
            $test = $list;

            $bay_id = $list['bay_id'];
            $goods_received_id = $list['goods_received_id'];
            $sql_bays = "SELECT *, (SELECT name FROM $table_bays WHERE id = $bay_id) as bay_name, (SELECT post_title FROM wp_posts WHERE ID = goods.product_name) as product_name_text
            FROM $table_goods goods
            WHERE goods.id = $goods_received_id";
            $bay_result = $wpdb->get_row($sql_bays);

            array_push($pickingListArr, array($list, "goods_info" => $bay_result));
        }
    } else {
        $sql = "SELECT *,
        goods.bin_id as bin_id, 
        (SELECT display_name FROM $users WHERE ID = ml.created_by) AS created_by,
        (SELECT display_name FROM $users WHERE ID = ml.confirmed_by) AS confirmed_by,
        (SELECT name FROM $table_bays WHERE id = goods.bay_id) AS bay_name
            FROM $table_goods goods
            LEFT JOIN $posts posts ON posts.ID = goods.product_name
            LEFT JOIN $table_name ml ON ml.movement_list_id = goods.movement_list_id
            WHERE goods.movement_list_id = '$movement_list_id' AND goods.warehouse_id= '$warehouse_id';";

        $rows = $wpdb->get_results($sql);
    }


    $goodArr = array();

    foreach ($rows as $res) {
        array_push($goodArr, $res);
    }

    if ($rows === false) {
        $response = array(
            'status' => 'error',
            'message' => "Error occured trying to fetch movement list",
        );
    } else {
        $response = array(
            'status' => 'success',
            'list_items' => json_encode($goodArr),
            'picking_list_items' => json_encode($pickingListArr),
            'sql' => $sql_bays
        );
    }


    wp_send_json($response);
}

function complete_move()
{

    $movement_list_id = isset($_POST['movement_list_id']) ? sanitize_text_field($_POST['movement_list_id']) : null;
    $picking_list_id = isset($_POST['picking_list_id']) ? sanitize_text_field($_POST['picking_list_id']) : null;
    $user_id = isset($_POST['current_user']) ? sanitize_text_field($_POST['current_user']) : null;
    $warehouse_administrator = get_post_meta(get_the_ID(), 'warehouse_administrator', true);

    global $wpdb;
    $table_name = $wpdb->prefix . 'dwm_movement_list';
    $table_goods = $wpdb->prefix . 'dwm_goods_received';
    $table_movement_list_items = $wpdb->prefix . 'dwm_movement_list_items';
    $table_picking_list_id = $wpdb->prefix . 'dwm_picking_list';


    $result = $wpdb->update($table_name, array('movement_status' => 3, 'confirmed_by' => $user_id), array('movement_list_id' => $movement_list_id));
    if ($picking_list_id != 0) {
        $result_picking_tbl = $wpdb->update($table_picking_list_id, array('picking_list_status' => 2), array('picking_list_id' => $picking_list_id));

        $sql_picking_list = "SELECT * FROM $table_picking_list_id WHERE picking_list_id = '$picking_list_id';";

        $db_result = $wpdb->get_row($sql_picking_list);
        $picking_list = unserialize($db_result->picking_list);
    }

    $sql = "SELECT * FROM $table_movement_list_items WHERE movement_list_id = '$movement_list_id';";
    $rows = $wpdb->get_results($sql);


    if ($picking_list) {
        foreach ($picking_list as $item) {
            $id = $item['goods_received_id'];
            $sql_goods = "SELECT * from $table_goods WHERE id = $id";

            $row_goods = $wpdb->get_row($sql_goods);
            $bags = $row_goods->amount_of_bags;

            $amount = $bags - $item['quantity'];

            if ($amount <= 0) {
                $wpdb->update(
                    $table_goods, // The table to update.
                    array('bin_status' => 4, 'amount_of_bags' => $amount), // The column to update and the value to set.
                    array(
                        'id' => $id
                    )
                );
            } else {
                $wpdb->update(
                    $table_goods, // The table to update.
                    array('bin_status' => 1, 'amount_of_bags' => $amount), // The column to update and the value to set.
                    array(
                        'id' => $id
                    )
                );
            }
        }
    } else {
        foreach ($rows as $res) {
            $wpdb->update(
                $table_goods, // The table to update.
                array('bin_status' => 1), // The column to update and the value to set.
                array(
                    'bin_id' => $res->bin_id, // Condition: which bin_id to update.
                )
            );
        }
    }


    if ($result === false) {
        if ($picking_list_id != 0) {
            if ($result_picking_tbl === false) {
                $response = array(
                    'status' => 'error',
                    'message' => "Error occured during picking confirmation please contact admin",
                );
            }
        } else {
            $response = array(
                'status' => 'error',
                'message' => "Error occured during movement please contact admin",
            );
        }
    } else {
        if ($picking_list_id != 0) {
            $response = array(
                'status' => 'success',
                'message' => `$picking_list_id has been completed`,
                'picking_list' => $amount
            );
        } else {
            $response = array(
                'status' => 'success',
                'message' => `$movement_list_id has been completed and moved into warehouse`
            );
        }

        if (!empty($warehouse_administrator)) {
            $user = get_userdata($warehouse_administrator);
            $to = $user->user_email;
        } else {
            $to = 'tevinhendricks16@gmail.com';
        }

        if ($picking_list_id != 0) {
            $subject = "Picking list and movement has been completed";
            $message = "{$picking_list_id} has been completed";
        } else {
            $subject = "Move has been completed";
            $message = "{$movement_list_id} has been completed and moved into warehouse";
        }

        my_plugin_send_email($to, $subject, $message, "", "");
    }


    wp_send_json($response);
}

function cancel_move()
{

    $movement_list_id = isset($_POST['movement_list_id']) ? sanitize_text_field($_POST['movement_list_id']) : null;
    $reason = isset($_POST['reason']) ? sanitize_text_field($_POST['reason']) : null;
    $user_id = isset($_POST['current_user']) ? sanitize_text_field($_POST['current_user']) : null;
    $warehouse_administrator = get_post_meta(get_the_ID(), 'warehouse_administrator', true);

    global $wpdb;
    $table_name = $wpdb->prefix . 'dwm_movement_list';
    $table_goods = $wpdb->prefix . 'dwm_goods_received';
    $table_picking_list_id = $wpdb->prefix . 'dwm_picking_list';


    $result = $wpdb->update($table_name, array('movement_status' => 2, 'confirmed_by' => $user_id, 'reason' => $reason), array('movement_list_id' => $movement_list_id));
    $wpdb->update($table_goods, array('bin_status' => 4), array('movement_list_id' => $movement_list_id));

   

    $sql_picking_list = "SELECT * FROM $table_picking_list_id WHERE movement_list_id = '$movement_list_id';";

    $db_result = $wpdb->get_row($sql_picking_list);
    $picking_list = unserialize($db_result->picking_list);

    if($picking_list){
        foreach ($picking_list as $item) {
            $id = $item['goods_received_id'];


            $wpdb->update(
                $table_goods, // The table to update.
                array('bin_status' => 1), // The column to update and the value to set.
                array(
                    'id' => $id
                )
            );
        }
    }


    if ($result === false) {
        $response = array(
            'status' => 'error',
            'message' => "Error occured during movement please contact admin",
        );
    } else {
        $response = array(
            'status' => 'success'
        );

        if (!empty($warehouse_administrator)) {
            $user = get_userdata($warehouse_administrator);
            $to = $user->user_email;
        } else {
            $to = 'tevinhendricks16@gmail.com';
        }

        $subject = "Move has been cancelled";
        $message = "{$movement_list_id} has been cancelled.";
        my_plugin_send_email($to, $subject, $message, "", "");
    }


    wp_send_json($response);
}

function handle_edit_bay()
{
    global $wpdb;
    $table_bays = $wpdb->prefix . 'dwm_bays';

    check_ajax_referer('edit_bay_nonce');

    // Get POST data
    $bay_name = $_POST['bay_name'];
    $bay_color = $_POST['bay_color'];
    $bay_type = $_POST['bay_type'];

    $result = $wpdb->update($table_bays, array('name' => $bay_name), array('color' => $bay_color), array('type' => $bay_type));

    if ($result === false) {
        $response = array(
            'status' => 'error',
            'message' => "Error occured during movement please contact admin",
        );
    } else {
        $response = array(
            'status' => 'Bay updated successfully'
        );
    }

    wp_send_json($response);
}

function add_bin_to_picking_list()
{
    // Check for the bin ID, quantity, and goods_received_id in the POST request
    $bin_id = isset($_POST['bin_id']) ? sanitize_text_field($_POST['bin_id']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0; // Make sure quantity is treated as an integer
    $good_received_id = isset($_POST['good_received_id']) ? sanitize_text_field($_POST['good_received_id']) : null;

    // Get the current user's ID
    $user_id = get_current_user_id();

    // Get the current picking list from user meta, or initialize as an empty array if it doesn't exist
    $picking_list = get_user_meta($user_id, 'picking_list', true);
    if (empty($picking_list)) {
        $picking_list = array(); // Initialize as an empty array if there's no existing list
    }

    // Create a unique identifier for each bin, quantity, and goods_received_id triplet
    $bin_entry = array('bin_id' => $bin_id, 'goods_received_id' => $good_received_id);

    // Check if this exact bin and quantity combination already exists in the list to prevent duplicates
    $update_needed = true;
    foreach ($picking_list as $index => $entry) {
        if ($entry['bin_id'] === $bin_id && $entry['goods_received_id'] === $good_received_id) {
            // Update the quantity for the existing bin ID and goods_received_id
            $picking_list[$index]['quantity'] += $quantity;
            $update_needed = false;
            break;
        }
    }

    // If the bin ID and goods_received_id do not exist in the list, add them
    if ($update_needed) {
        $picking_list[] = $bin_entry;
    }

    // Update the picking list in the user meta
    update_user_meta($user_id, 'picking_list', $picking_list);

    // Prepare the response
    if (!isset($bin_id) || isset($goods_received_id)) {
        $response = array(
            'status' => 'error',
            'message' => 'Bin ID or Goods Received ID not provided',
            'pickingList' => $picking_list
        );
    } else {
        $response = array(
            'status' => 'success',
            'message' => 'Item added to the picking list',
            'pickingList' => $picking_list // Return the updated picking list
        );
    }

    // Send the JSON response
    wp_send_json($response);
}

function remove_bin_from_picking_list()
{

    $bin_id = isset($_POST['bin_id']) ? sanitize_text_field($_POST['bin_id']) : '';
    $user_id = get_current_user_id();
    $picking_list = get_user_meta($user_id, 'picking_list', true);
    $picking_list = maybe_unserialize($picking_list);

    if (!empty($picking_list)) {
        foreach ($picking_list as $index => $item) {
            if ($item['bin_id'] == $bin_id) {
                unset($picking_list[$index]);
                break; // Exit the loop once the item is found and removed
            }
        }

        update_user_meta($user_id, 'picking_list', $picking_list); // Update the modified array back to the user meta

        $response = array(
            'status' => 'success',
            'message' => 'Item removed from picking list',
            'pickingList' => $picking_list // Return the updated picking list
        );
        // Send the JSON response
        wp_send_json($response);
    } else {
        wp_send_json_error(['message' => 'Picking list not found or already empty']);
    }
}
add_action('wp_ajax_remove_bin_from_picking_list', 'remove_bin_from_picking_list');
