<?php

function enqueue_bootstrap()
{
    // Enqueue Bootstrap CSS
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');

    // Enqueue FA CSS
    wp_enqueue_style('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css');

    // Swal CSS
    wp_enqueue_style('swal-css', '//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');





    wp_enqueue_script('jquery-not-slim', 'https://code.jquery.com/jquery-3.7.1.min.js', array('jquery'));


    // <!-- Popper JS -->
    wp_enqueue_script('popper-js', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js', array('jquery'));

    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js', array('jquery'));

    wp_enqueue_script('jquery-easing-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js', array('jquery'));


    // <!-- Swal JS -->
    wp_enqueue_script('swal', '//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js', array('jquery'));
}

add_action('wp_enqueue_scripts', 'enqueue_bootstrap');

function enqueue_custom_script()
{
    // Enqueue script
    wp_enqueue_script('qr-code-script', 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js');
    wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js');
    wp_enqueue_script('fontawesome', 'https://kit.fontawesome.com/59e5b41d00.js');
    wp_enqueue_script('custom-script', plugins_url() . '/warehouse-manager/js/script.js');
    wp_enqueue_script('chart-script', plugins_url() . '/warehouse-manager/js/chart.js');
    wp_enqueue_script('jspdf', plugins_url() . '/warehouse-manager/js/jspdf.js');
    wp_enqueue_script('html2pdf', 'https://rawgit.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.min.js');
    wp_enqueue_script('html2canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');


    wp_enqueue_script('datatables-script', plugins_url() . '/warehouse-manager/DataTables/datatables.min.js');
    wp_enqueue_script('js-zip-script', plugins_url() . '/warehouse-manager/js/jszip/jszip.js');
    wp_enqueue_script('pdfmake-script', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.js');
    wp_enqueue_script('pdfmake-fonts-script', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js');

    // Localize the script with new data
    $script_data_array = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
    );
    wp_localize_script('custom-script', 'my_script_data', $script_data_array);

    // Enqueue style
    wp_enqueue_style('custom-style', plugins_url() . '/warehouse-manager/css/style.css');
    wp_enqueue_style('datatables-style', plugins_url() . '/warehouse-manager/DataTables/datatables.min.css');
}

// Hook your function to the 'wp_enqueue_scripts' action
add_action('wp_enqueue_scripts', 'enqueue_custom_script');

function my_admin_enqueue_styles($hook)
{


    global $post;

    global $typenow;
    if ($typenow == 'warehouses' || $typenow == 'warehouse-products' ||  (isset($_GET['page']) && $_GET['page'] == 'manage_warehouse')) {

        // Register the style
        wp_register_style('my_custom_admin_css', plugins_url('css/admin-style.css', __FILE__), false, '1.0.0');
        // Enqueue the style
        wp_enqueue_style('my_custom_admin_css');
        // Enqueue Bootstrap CSS
        wp_enqueue_style('bootstrap-admin-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
        wp_enqueue_style('bootstrapswitch-admin-css', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/css/bootstrap3/bootstrap-switch.min.css');

        // Enqueue Bootstrap JS
        wp_enqueue_script('bootstrap-admin-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);

        wp_enqueue_script('jquery-not-slim', 'https://code.jquery.com/jquery-3.7.1.min.js', array('jquery'));


        // Swal CSS
        wp_enqueue_style('swal-css', '//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css');


        // <!-- Popper JS -->
        wp_enqueue_script('popper-js', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js', array('jquery'));

        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js', array('jquery'));

        wp_enqueue_script('jquery-easing-js', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js', array('jquery'));


        // <!-- Swal JS -->
        wp_enqueue_script('swal', '//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js', array('jquery'));

        // Enqueue script
        wp_enqueue_script('qr-code-script', 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js');
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js');
        wp_enqueue_script('fontawesome', 'https://kit.fontawesome.com/59e5b41d00.js');
        wp_enqueue_script('custom-script', plugins_url() . '/warehouse-manager/js/script.js');
        wp_enqueue_script('chart-script', plugins_url() . '/warehouse-manager/js/chart.js');
        wp_enqueue_script('jspdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js');
        wp_enqueue_script('html2canvas', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js');


        wp_enqueue_script('datatables-script', plugins_url() . '/warehouse-manager/DataTables/datatables.min.js');
        wp_enqueue_script('js-zip-script', plugins_url() . '/warehouse-manager/js/jszip/jszip.js');
        wp_enqueue_script('pdfmake-script', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.js');
        wp_enqueue_script('pdfmake-fonts-script', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js');

        // Localize the script with new data
        $script_data_array = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        );
        wp_localize_script('custom-script', 'my_script_data', $script_data_array);

        // Enqueue style
        wp_enqueue_style('custom-style', plugins_url() . '/warehouse-manager/css/style.css');
        wp_enqueue_style('datatables-style', plugins_url() . '/warehouse-manager/DataTables/datatables.min.css');

        wp_enqueue_style('select2-style', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');


        // <!-- Swal JS -->
        wp_enqueue_script('swal', '//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js', array('jquery'));
    }
}

add_action('admin_enqueue_scripts', 'my_admin_enqueue_styles');
