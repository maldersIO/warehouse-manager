<?php
// Function to add custom meta boxes for predefined fields
function add_warehouse_meta_boxes()
{
    add_meta_box(
        'warehouse_details',
        __('Warehouse Details'),
        'render_warehouse_details_meta_box',
        'warehouses',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'register_bays_meta_box');

function edit_bay_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'dwm_bays';

    // Check if the bay ID is set in the URL
    if (!isset($_GET['bay_id'])) {
        echo 'Bay ID is required.';
        return;
    }

    $bay_id = intval($_GET['bay_id']);
    $bay = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $bay_id));
    // Fetch bay types
    $bay_types_table = $wpdb->prefix . 'dwm_bay_types';
    $bay_types = $wpdb->get_results("SELECT * FROM {$bay_types_table}");

    // Check if bay exists
    if (!$bay) {
        echo 'Bay not found.';
        return;
    }

    // Display the edit form
?>
    <div class="wrap">
        <h1>Edit Bay</h1>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="update_bay">
            <input type="hidden" name="bay_id" value="<?php echo esc_attr($bay->id); ?>">
            <input type="hidden" name="warehouse_id" value="<?php echo esc_attr($bay->warehouse_id); ?>">
            <input type="hidden" name="post_id" value="<?php echo esc_attr($_GET['post_id']); ?>">
            <?php wp_nonce_field('update_bay_nonce'); ?>
            <!-- Add form fields here, pre-filled with $bay data -->
            <!-- Example: -->
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo esc_attr($bay->name); ?>"><br>
            <label for="color">Color:</label>
            <input type="text" id="color" name="color" value="<?php echo esc_attr($bay->color); ?>"><br>
            <label for="color">Type:</label>
            <select id="bayTypeDropdown" name="wh_type">
                <?php foreach ($bay_types as $type) { ?>
                    <option value="<?php echo esc_html($type->id); ?>" <?php echo ($type->id == $bay->type_id) ? 'selected' : ''; ?>>
                        <?php echo esc_html($type->name); ?>
                    </option>
                <?php } ?>

            </select>
            <!-- Add other fields similarly -->
            <input type="submit" value="Update Bay">
        </form>
    </div>
<?php
}

function render_bays_meta_box($post)
{
    global $wpdb;
    $warehouse_id = $post->ID;
    $bays_table = $wpdb->prefix . 'dwm_bays';
    $bay_types_table = $wpdb->prefix . 'dwm_bay_types';

    // Fetch bay types
    $bay_types = $wpdb->get_results("SELECT * FROM {$bay_types_table}");

    // Form for adding new bays
    echo '<div class="bay-form">';
    echo '<form action="" method="post">';
    echo '<label for="new_bay_name">Add New Bay:</label>';
    echo '<input class="form-control" type="text" id="new_bay_name" name="new_bay_name" placeholder="Enter new bay name">';
    echo '<label for="wh_color">Bay colour:</label>';
    echo '<input class="form-control" type="color" id="wh_color" name="wh_color">';
    // Bay Types Dropdown for editing
    echo '<select id="bayTypeDropdown" name="wh_type">';
    echo '<option value="">Select</option>';
    foreach ($bay_types as $type) {
        echo '<option value="' . esc_attr($type->id) . '">' . esc_html($type->name) . '</option>';
    }
    echo '</select>';
    echo '<input type="submit" name="submit_new_bay" value="Add New Bay">';
    wp_nonce_field('add_new_bay_action', 'add_new_bay_nonce');
    echo '</form>';
    echo '</div>';
    echo '<hr>';

    $bays = $wpdb->get_results("SELECT * FROM {$bays_table} WHERE warehouse_id = {$warehouse_id}");
?>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bays as $bay) : ?>
                <tr>
                    <td><br><span style="background-color:<?php echo $bay->color ?>; padding: 7px; border-radius: 30px; color: #fff;">
                            <?php echo esc_html($bay->name); ?></span><br><br>
                        <!-- Edit Button -->
                        <a href="<?php echo esc_url(admin_url('admin.php?page=edit_bay&bay_id=' . esc_attr($bay->id) . '&post_id=' . $warehouse_id)); ?>">Edit</a>
                        <!-- Delete Button -->
                        <a href="<?php echo esc_url(admin_url('admin-post.php?action=delete_bay&bay_id=' . $bay->id . '&post_id=' . $warehouse_id)) ?>" class="button-delete" style="color:brown;" onclick="return confirm('Are you sure you want to delete this feed?');">Delete</a>
                    </td>
                    <?php $bay_name = $wpdb->get_row("SELECT name FROM {$bay_types_table} WHERE id = $bay->type_id;"); ?>
                    <?php if (isset($bay_name)) {
                        echo '<td>' . esc_html($bay_name->name) . '</td>'; // Display bay type
                    } else {
                        echo '<td></td>'; // Display bay type
                    } ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php
    // Add your branding
    echo '  <div class="branding">
            <p><strong>WP <span>Racks</span></strong> - Warehouse Management</p>
        </div>';

    ?>
<?php
}

function register_bays_meta_box()
{
    add_meta_box('warehouse_bays_meta_box', 'Warehouse Bays', 'render_bays_meta_box', 'warehouses', 'normal', 'high');
}




add_action('admin_post_update_bay', 'handle_update_bay');


function handle_update_bay()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'dwm_bays';

    // Check user permissions and nonce for security
    if (!current_user_can('manage_options') || !check_admin_referer('update_bay_nonce')) {
        wp_die('You are not allowed to perform this action.');
    }



    // Validate and sanitize input data
    $bay_id = isset($_POST['bay_id']) ? intval($_POST['bay_id']) : 0;
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $color = isset($_POST['color']) ? sanitize_text_field($_POST['color']) : '';
    $type_id = isset($_POST['wh_type']) ? intval($_POST['wh_type']) : 0;
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $warehouse_id = isset($_POST['warehouse_id']) ? sanitize_text_field($_POST['warehouse_id']) : '';

    // Check if the bay exists
    $bay = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $bay_id));
    if (!$bay) {
        wp_die('Bay not found.');
    }

    // Update the bay data
    $wpdb->update(
        $table_name,
        array(
            'name' => $name,
            'color' => $color,
            'type_id' => $type_id,
            'warehouse_id' => $warehouse_id
        ),
        array('id' => $bay_id)
    );

    // Redirect back to the edit page
    $redirect_url = admin_url('post.php?post=' . $post_id . '&action=edit');
    wp_redirect($redirect_url);
    exit;
}


add_action('admin_post_delete_bay', 'delete_bay_function');

function delete_bay_function()
{
    // Check user permissions, nonce, and that bay_id is set
    // ...

    global $wpdb;
    $table_name = $wpdb->prefix . 'dwm_bays';
    $bay_id = intval($_GET['bay_id']);
    $wpdb->delete($table_name, array('id' => $bay_id));

    // Redirect back to the edit page
    $redirect_url = admin_url('post.php?post=' . $_GET['post_id'] . '&action=edit');
    wp_redirect($redirect_url);
    exit;
}


// Function to render the content of the custom meta box
function render_warehouse_details_meta_box($post)
{
    // Retrieve the values of predefined fields, if they exist
    $racks = get_post_meta($post->ID, 'racks', true);
    $levels_per_rack = get_post_meta($post->ID, 'levels_per_rack', true);
    $capacities = get_post_meta($post->ID, 'capacities', true);
    $warehouse_address = get_post_meta($post->ID, 'warehouse_address', true);


    // Output the HTML form fields for predefined fields
?>
    <div class="plugin-form">
        <label for="racks"><?php _e('Racks:'); ?></label>
        <input class="form-control" type="number" id="racks" name="racks" value="<?php echo esc_attr($racks); ?>"><br>

        <label for="levels_per_rack"><?php _e('Levels per rack:'); ?></label>
        <input class="form-control" type="text" id="levels_per_rack" name="levels_per_rack" value="<?php echo esc_attr($levels_per_rack); ?>"><br>

        <label for="capacities"><?php _e('Capacities:'); ?></label>
        <input class="form-control" type="text" id="capacities" name="capacities" value="<?php echo esc_attr($capacities); ?>"><br>

        <label for="warehouse_address"><?php _e('Warehouse address:'); ?></label>
        <input class="form-control" type="text" id="warehouse_address" name="warehouse_address" value="<?php echo esc_attr($warehouse_address); ?>"><br>


        <div class="branding">
            <p><strong>WP <span>Racks</span></strong> - Warehouse Management</p>
        </div>
    </div>


<?php
}

add_action('add_meta_boxes', 'add_warehouse_users_meta_box');

function add_warehouse_users_meta_box()
{
    add_meta_box(
        'warehouse_users_meta_box',          // Unique ID
        'Warehouse Users',                   // Box title
        'render_warehouse_users_meta_box',   // Content callback, must be of type callable
        'warehouses',                        // Post type
        'side',                              // Context
        'default'                            // Priority
    );
}

function render_warehouse_users_meta_box($post)
{
    // Fetch WordPress users
    $users = get_users();

    $warehouse_manager = get_post_meta($post->ID, 'warehouse_manager', true);
    $dispatch_clerk = get_post_meta($post->ID, 'dispatch_clerk', true);
    $receiving_clerk = get_post_meta($post->ID, 'receiving_clerk', true);
    $warehouse_administrator = get_post_meta($post->ID, 'warehouse_administrator', true);
    $warehouse_supervisor = get_post_meta($post->ID, 'warehouse_supervisor', true);
    // ... other fields ...

    // Output the HTML form fields for user roles
?>
    <div class="plugin-form">


        <!-- Warehouse Manager -->
        <label for="warehouse_manager"><?php _e('Warehouse manager:'); ?></label>
        <select class="form-control" id="warehouse_manager" name="warehouse_manager">

            <option value="">-- Select</option>
            <?php foreach ($users as $user) {
                echo '<option value="' . esc_attr($user->ID) . '"' . selected($warehouse_manager, $user->ID) . '>' . esc_html($user->display_name) . '</option>';
            } ?>
        </select><br>


        <!-- Warehouse Administrator -->
        <label for="warehouse_administrator"><?php _e('Warehouse administrator:'); ?></label>
        <select class="form-control" id="warehouse_administrator" name="warehouse_administrator" required>
            <option value="">-- Select</option>
            <?php foreach ($users as $user) {
                echo '<option value="' . esc_attr($user->ID) . '"' . selected($warehouse_administrator, $user->ID) . '>' . esc_html($user->display_name) . '</option>';
            } ?>
        </select><br>

        <!-- Warehouse Supervisor -->
        <label for="warehouse_supervisor"><?php _e('Warehouse supervisor:'); ?></label>
        <select class="form-control" id="warehouse_supervisor" name="warehouse_supervisor" required>
            <option value="">-- Select</option>
            <?php foreach ($users as $user) {
                echo '<option value="' . esc_attr($user->ID) . '"' . selected($warehouse_supervisor, $user->ID) . '>' . esc_html($user->display_name) . '</option>';
            } ?>
        </select><br>

        <!-- Dispatch Clerk -->
        <label for="dispatch_clerk"><?php _e('Dispatch clerk:'); ?></label>
        <select class="form-control" id="dispatch_clerk" name="dispatch_clerk" required>
            <option value="">-- Select</option>
            <?php foreach ($users as $user) {
                echo '<option value="' . esc_attr($user->ID) . '"' . selected($dispatch_clerk, $user->ID) . '>' . esc_html($user->display_name) . '</option>';
            } ?>
        </select><br>

        <!-- Recieving Clerk -->
        <label for="receiving_clerk"><?php _e('Receiving clerk:'); ?></label>
        <select class="form-control" id="receiving_clerk" name="receiving_clerk" required>
            <option value="">-- Select</option>
            <?php foreach ($users as $user) {
                echo '<option value="' . esc_attr($user->ID) . '"' . selected($receiving_clerk, $user->ID) . '>' . esc_html($user->display_name) . '</option>';
            } ?>
        </select><br>

        <div class="branding">
            <p><strong>WP <span>Racks</span></strong> - Warehouse Management</p>
        </div>

    </div>
<?php
}


// Function to save the values of predefined fields
function save_warehouse_details_meta($post_id)
{

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Save the values of predefined fields
    $fields = array(
        'racks',
        'levels_per_rack',
        'capacities',
        'warehouse_manager',
        'warehouse_address',
        'dispatch_clerk',
        'receiving_clerk',
        'warehouse_administrator',
        'warehouse_supervisor',
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }


    // Check if our nonce is set and verify it
    if (isset($_POST['add_new_bay_nonce']) && wp_verify_nonce($_POST['add_new_bay_nonce'], 'add_new_bay_action')) {

        if (empty($_POST['new_bay_name'])) {
        } elseif (isset($_POST['new_bay_name']) && !empty($_POST['new_bay_name'])) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'dwm_bays';
            $new_bay_name = sanitize_text_field($_POST['new_bay_name']);
            $wh_color = sanitize_text_field($_POST['wh_color']);
            $wh_type = sanitize_text_field($_POST['wh_type']);
            $wpdb->insert(
                $table_name,
                array(
                    'name' => $new_bay_name,
                    'color' => $wh_color,
                    'type_id' => $wh_type,
                    'warehouse_id' => $post_id,
                    'date_created' => current_time('mysql'),
                ),
                array('%s', '%s', '%d', '%d', '%s')
            );
        }
    }
}

function update_bays_in_database($warehouse_id, $bays)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'dwm_bays';

    // Split the bays string by commas
    $bay_array = explode(',', $bays);

    foreach ($bay_array as $bay) {
        $bay = trim($bay);
        if (!empty($bay)) {
            // Insert or update the bay in the table
            $wpdb->replace(
                $table_name,
                array(
                    'name' => $bay,
                    'warehouse_id' => $warehouse_id,
                    'date_created' => current_time('mysql')
                ),
                array('%s', '%d', '%s')
            );
        }
    }
}


// Hook the meta box functions to WordPress actions
add_action('add_meta_boxes', 'add_warehouse_meta_boxes');
add_action('save_post_warehouses', 'save_warehouse_details_meta');
