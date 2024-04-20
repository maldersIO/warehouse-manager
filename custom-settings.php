<?php
function my_custom_plugin_menu()
{
    add_submenu_page(
        'options-general.php', // Parent menu slug (Settings)
        'WP Racks Settings', // Page title
        'WP Racks Settings', // Menu title
        'manage_options', // Capability required to access
        'wp-racks-settings', // Menu slug
        'custom_plugin_settings_page' // Callback function to display the page content
    );
}
add_action('admin_menu', 'my_custom_plugin_menu');


function custom_plugin_settings_page()
{
    // Check if the 'tab' parameter is set in the URL
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general_settings';

?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <h2 class="nav-tab-wrapper">
            <a href="?page=wp-racks-settings&tab=general_settings" class="nav-tab <?php echo $active_tab == 'general_settings' ? 'nav-tab-active' : ''; ?>">General Settings</a>
            <a href="?page=wp-racks-settings&tab=custom_settings" class="nav-tab <?php echo $active_tab == 'custom_settings' ? 'nav-tab-active' : ''; ?>">Custom Settings</a>
        </h2>

        <form action="options.php" method="post">
            <?php
            if ($active_tab == 'general_settings') {
                settings_fields('my_general_settings_group');
                // Measurement settings form content
                // ...
                $measurement_method = get_option('measurement_method');
                $length = get_option('length');
                $width = get_option('width');
                $height = get_option('height');
                $weight = get_option('weight');

            ?>

                <h2>Measurement Settings</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="measurement_method">How is your place in your warehouse measured?</label>
                        </th>
                        <td>
                            <select name="measurement_method" id="measurement_method" onchange="showDimensionFields(this.value)">
                                <option value="">Select</option>
                                <option value="weight" <?php selected($measurement_method, 'weight'); ?>>Weight</option>
                                <option value="dimensions" <?php selected($measurement_method, 'dimensions'); ?>>Dimensions</option>
                            </select>

                        </td>
                    </tr>
                    <tr id="dimensions_fields" style="<?php echo ($measurement_method == 'dimensions') ? '' : 'display: none;'; ?>">
                        <th scope="row">Dimensions (LxWxH)</th>
                        <td>
                            <input type="text" name="length" placeholder="Length" value="<?php echo esc_attr($length); ?>" />
                            <input type="text" name="width" placeholder="Width" value="<?php echo esc_attr($width); ?>" />
                            <input type="text" name="height" placeholder="Height" value="<?php echo esc_attr($height); ?>" />
                        </td>
                    </tr>
                    <tr id="weight_fields" style="<?php echo ($measurement_method == 'weight') ? '' : 'display: none;'; ?>">
                        <th scope="row"></th>
                        <td>
                            <input type="text" name="weight" placeholder="Enter Max Weight" value="<?php echo esc_attr($weight); ?>" />
                        </td>
                    </tr>


                    <!-- Row for expiry date setting -->
                    <tr>
                        <th scope="row">
                            <label for="enable_expiry_date">Enable Expiry Date:</label>
                        </th>
                        <td>
                            <input type="checkbox" name="enable_expiry_date" id="enable_expiry_date" <?php checked(get_option('enable_expiry_date'), 'on'); ?> />
                        </td>
                    </tr>

                </table>
            <?php
            } else if ($active_tab == 'custom_settings') {
                // Color settings form content
                // ...
                // Output security fields for the registered setting.
                settings_fields('my_custom_settings_group');
                // Output setting sections and their fields.
                do_settings_sections('my_custom_settings');
                // Output save settings button.
                // submit_button('Save Colors');
            ?>
                <h2>Custom Label Settings</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="custom_input_label">Custom Input Label:</label>
                        </th>
                        <td>
                            <input type="text" name="custom_input_label" id="custom_input_label" value="<?php echo esc_attr(get_option('custom_input_label')); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="custom_input_2_label">Custom Input 2 Label:</label>
                        </th>
                        <td>
                            <input type="text" name="custom_input_2_label" id="custom_input_2_label" value="<?php echo esc_attr(get_option('custom_input_2_label')); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="custom_input_3_label">Custom Input 3 Label:</label>
                        </th>
                        <td>
                            <input type="text" name="custom_input_3_label" id="custom_input_3_label" value="<?php echo esc_attr(get_option('custom_input_3_label')); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="custom_input_4_label">Custom Input 4 Label:</label>
                        </th>
                        <td>
                            <input type="text" name="custom_input_4_label" id="custom_input_4_label" value="<?php echo esc_attr(get_option('custom_input_4_label')); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="custom_input_5_label">Custom Input 2 Label:</label>
                        </th>
                        <td>
                            <input type="text" name="custom_input_5_label" id="custom_input_5_label" value="<?php echo esc_attr(get_option('custom_input_5_label')); ?>" />
                        </td>
                    </tr>
                </table>
            <?php
            }
            submit_button('Save Settings');
            ?>
        </form>
        <script>
            function showDimensionFields(value) {
                var dimensionsFields = document.getElementById('dimensions_fields');
                if (value === 'dimensions') {
                    dimensionsFields.style.display = '';
                } else {
                    dimensionsFields.style.display = 'none';
                }

                var dimensionsFields = document.getElementById('weight_fields');
                if (value === 'weight') {
                    dimensionsFields.style.display = '';
                } else {
                    dimensionsFields.style.display = 'none';
                }

                document.getElementById('dimensions_fields').style.display = (value === 'dimensions') ? '' : 'none';
                document.getElementById('weight_fields').style.display = (value === 'weight') ? '' : 'none';
            }
        </script>
    </div>
<?php
}


add_action('admin_init', 'my_custom_settings_init');

function my_custom_settings_init()
{

    register_setting('my_custom_settings_group', 'bs-primary');
    register_setting('my_custom_settings_group', 'bs-primary-hover');
    register_setting('my_custom_settings_group', 'bs-secondary-color');
    register_setting('my_custom_settings_group', 'bs-secondary-hover');

    // Register new settings for custom labels
    register_setting('my_custom_settings_group', 'custom_input_label');
    register_setting('my_custom_settings_group', 'custom_input_2_label');
    register_setting('my_custom_settings_group', 'custom_input_3_label');
    register_setting('my_custom_settings_group', 'custom_input_4_label');
    register_setting('my_custom_settings_group', 'custom_input_5_label');
    register_setting('my_custom_settings_group', 'custom_input_5_label');

    add_settings_section('my_custom_section', 'Custom colours', 'my_custom_section_callback', 'my_custom_settings');

    // Register settings for General Settings
    register_setting('my_general_settings_group', 'measurement_method');
    register_setting('my_general_settings_group', 'length');
    register_setting('my_general_settings_group', 'width');
    register_setting('my_general_settings_group', 'height');
    register_setting('my_general_settings_group', 'weight');
    register_setting('my_general_settings_group', 'enable_expiry_date');

    add_settings_field('bs-primary', 'Primary color', 'bs_primary_color_callback', 'my_custom_settings', 'my_custom_section');
    add_settings_field('bs-primary-hover', 'Primary hover color', 'bs_primary_hover_color_callback', 'my_custom_settings', 'my_custom_section');
    add_settings_field('bs-secondary-color', 'Secondary colour', 'bs_secondary_color_callback', 'my_custom_settings', 'my_custom_section');
    add_settings_field('bs-secondary-hover', 'Secondary hover color', 'bs_secondary_hover_color_callback', 'my_custom_settings', 'my_custom_section');
}


function my_custom_section_callback()
{
    echo 'You can customize the colours of your warehouse places below';
}

function bs_primary_color_callback()
{
    $value = get_option('bs-primary', '');
    echo '<input type="color" name="bs-primary" value="' . esc_attr($value) . '">';
}

function bs_primary_hover_color_callback()
{
    $value = get_option('bs-primary-hover', '');
    echo '<input type="color" name="bs-primary-hover" value="' . esc_attr($value) . '">';
}

function bs_secondary_color_callback()
{
    $value = get_option('bs-secondary-color', '');
    echo '<input type="color" name="bs-secondary-color" value="' . esc_attr($value) . '">';
}


function bs_secondary_hover_color_callback()
{
    $value = get_option('bs-secondary-hover', '');
    echo '<input type="color" name="bs-secondary-hover" value="' . esc_attr($value) . '">';
}

add_action('wp_head', 'add_custom_colors');

// Set default colors for svg
add_option('bs-primary', '#54bfcc');
add_option('bs-primary-hover', '#007d8c');
add_option('bs-secondary-color', '#9c3372');
add_option('bs-secondary-hover', '#590A3A');


function add_custom_colors()
{
    $bs_primary = get_option('bs-primary', '#54bfcc');
    $bs_primary_hover = get_option('bs-primary-hover', '#007d8c');
    $bs_secondary_color = get_option('bs-secondary-color', '#9c3372');
    $bs_secondary_hover = get_option('bs-secondary-hover', '#590A3A');
?>
    <style type="text/css">
        /* Use your actual CSS selectors here */

        :root {
            --bs-primary: <?php echo $bs_primary; ?>;
            --bs-primary-hover: <?php echo $bs_primary_hover; ?>;
            --bs-secondary-color: <?php echo $bs_secondary_color; ?>;
            --bs-secondary-hover: <?php echo $bs_secondary_hover; ?>;
        }
    </style>
<?php
}

function add_custom_admin_colors() {
    $bs_primary = get_option('bs-primary', '#54bfcc');
    $bs_primary_hover = get_option('bs-primary-hover', '#007d8c');
    $bs_secondary_color = get_option('bs-secondary-color', '#9c3372');
    $bs_secondary_hover = get_option('bs-secondary-hover', '#590A3A');

    echo '<style type="text/css">
        :root {
            --bs-primary: ' . $bs_primary . ';
            --bs-primary-hover: ' . $bs_primary_hover . ';
            --bs-secondary-color: ' . $bs_secondary_color . ';
            --bs-secondary-hover: ' . $bs_secondary_hover . ';
        }
    </style>';
}
add_action('admin_head', 'add_custom_admin_colors');
