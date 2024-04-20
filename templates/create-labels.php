<?php
/*
Template Name: Create Labels
*/
// Rest of your template code...

get_header();

if (!is_user_logged_in()) {
    echo 'Please log in to view this content.';
    return; // Stop further processing if the user is not logged in.
}
?>
<div class="single-warehouse warehouse">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="card mb-2 mt-4">
                    <h5 class="card-header">Preview labels</h5>
                    <div class="card-body pt-0">
                        <?php
                        $movement_list_id = $_GET['ml'] ?? '';
                        $warehouse_id = $_GET['wh'] ?? '';

                        if (empty($movement_list_id) || empty($warehouse_id)) {
                            $noLabels = true;
                        } else {
                            $noLabels = false;
                            $rows = get_label_data($movement_list_id, $warehouse_id);
                        }

                        function get_label_data($movement_list_id, $warehouse_id)
                        {
                            global $wpdb;

                            $table_name = $wpdb->prefix . 'dwm_movement_list';
                            $table_goods = $wpdb->prefix . 'dwm_goods_received';
                            $table_movement_list_items = $wpdb->prefix . 'dwm_movement_list_items';
                            $posts = $wpdb->prefix . 'posts';

                            $sql = "SELECT *
                                        FROM $table_goods goods
                                        LEFT JOIN $table_movement_list_items mli ON mli.bin_id = goods.bin_id
                                        LEFT JOIN $posts posts ON posts.ID = goods.product_name
                                        LEFT JOIN $table_name ml ON ml.movement_list_id = mli.movement_list_id
                                        WHERE ml.movement_list_id = '$movement_list_id' AND goods.warehouse_id= '$warehouse_id' ";

                            return $wpdb->get_results($sql);
                        }

                        ?>
                        <div class="label-preview">
                            <div id="root">
                                <?php foreach ($rows as $i => $label) : ?>
                                    <?php
                                    $date_created = new DateTime($label->created_date);
                                    ?>
                                    <div class="label">
                                        <div class="label-wrapper">
                                            <div class="label-header row">
                                                <div class="col-md-4">
                                                    <div id="qrcode<?php echo $i; ?>" width="50" height="50"></div>
                                                </div>
                                                <div class="col-md-8">
                                                    <span class="head text">Pallet ID</span><br>
                                                    <span class="pallet-id"><?php echo $label->pallet_id; ?></span><br>
                                                    <span class="head text">Warehouse name</span><br>
                                                    <span class="detail text"><?php echo get_the_title($warehouse_id); ?></span><br>
                                                    <span class="head text">Warehouse address</span><br>
                                                    <span class="detail text"><?php echo get_post_meta($warehouse_id, 'warehouse_address', true); ?></span><br>
                                                    <span class="amount"><i class="fa-solid fa-box"></i> <?php echo $i + 1 . " of " . $label->amount_of_pallets; ?></span>
                                                </div>
                                            </div>
                                            <div class="label-body row">
                                                <div class="col-md-12">
                                                    <span class="head text">Product name</span><br>
                                                    <span class="detail text"><?php echo $label->post_title; ?></span><br>
                                                </div>
                                                <?php
                                                $custom_input_label = get_option('custom_input_label', '');
                                                $custom_input_2_label = get_option('custom_input_2_label', '');
                                                $custom_input_3_label = get_option('custom_input_3_label', '');
                                                $custom_input_4_label = get_option('custom_input_4_label', '');
                                                $custom_input_5_label = get_option('custom_input_5_label', '');
                                                ?>
                                                <?php if ($custom_input_label != '') { ?>
                                                    <div class="col-md-6">
                                                        <span class="head text"><?php echo get_option('custom_input_label', 'Default Custom Input Label'); ?></span><br>
                                                        <span class="detail text"><?php echo $label->custom_input; ?></span><br>
                                                    </div>
                                                <?php }
                                                if ($custom_input_2_label != '') { ?>
                                                    <div class="col-md-6">
                                                        <span class="head text"><?php echo get_option('custom_input_2_label', 'Default Custom Input 2 Label'); ?></span><br>
                                                        <span class="detail text"><?php echo $label->custom_input_2; ?></span><br>
                                                    </div>
                                                <?php }
                                                if ($custom_input_3_label != '') { ?>
                                                    <div class="col-md-6">
                                                        <span class="head text"><?php echo get_option('custom_input_3_label', 'Default Custom Input 3 Label'); ?></span><br>
                                                        <span class="detail text"><?php echo $label->custom_input_3; ?></span><br>
                                                    </div>
                                                <?php }
                                                if ($custom_input_4_label != '') { ?>
                                                    <div class="col-md-6">
                                                        <span class="head text"><?php echo get_option('custom_input_4_label', 'Default Custom Input 4 Label'); ?></span><br>
                                                        <span class="detail text"><?php echo $label->custom_input_4; ?></span><br>
                                                    </div>
                                                <?php }
                                                if ($custom_input_5_label != '') { ?>
                                                    <div class="col-md-6">
                                                        <span class="head text"><?php echo get_option('custom_input_5_label', 'Default Custom Input 5 Label'); ?></span><br>
                                                        <span class="detail text"><?php echo $label->custom_input_5; ?></span><br>
                                                    </div>
                                                <?php } ?>
                                                <div class="col-md-6">
                                                    <span class="head text">Expiry date</span><br>
                                                    <span class="detail text"><?php echo $label->expiry_date; ?></span><br>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="head text">Bin ID</span><br>
                                                    <span class="detail text"><?php echo "R0".$label->rack."-L0".$label->level."-P0".$label->position; ?></span><br>
                                                </div>
                                            </div>
                                            <div class="label-footer row">
                                                <div class="col-md-6">
                                                    <span class="head text">Created date</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <span class="detail text"><?php echo $date_created->format('Y-m-d'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="before"></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-2 mt-4">
                    <h5 class="card-header">Generate labels</h5>
                    <div class="card-body pt-0 mt-4">
                        <?php if ($noLabels) : ?>
                            <h1 style="text-align:center">No labels to print</h1>
                        <?php else : ?>
                            <button id="btnPDF" class="btn btn-primary">Generate PDF</button><button id="btnPDF" class="btn btn-primary"><i class="fa fa-print"></i></button>
                        <?php endif; ?>
                        <button onclick="goBack()" class="btn btn-secondary">Go Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        // Assuming $rows is available in JavaScript, otherwise you need to output it from PHP
        var rows = <?php echo json_encode($rows); ?>;
        rows.forEach(function(label, index) {
            generateQRcode('qrcode' + index, label.pallet_id);
        });
    };

    function gen_pdf() {
        // Get the element.
        var ml = <?php echo json_encode($rows[0]); ?>;
        var element = document.getElementById('root');
        // Choose pagebreak options based on mode.
        var mode = 'css';
        var pagebreak = (mode === 'specify') ? {
            mode: '',
            before: '.before',
            after: '.after',
            avoid: '.avoid'
        } : {
            mode: mode
        };
        // Generate the PDF.
        html2pdf().from(element).set({
            filename: ml.movement_list_id + '-labels.pdf',
            pagebreak: pagebreak,
            jsPDF: {
                orientation: 'landscape',
                unit: 'in',
                format: 'letter',
                compressPDF: true
            }
        }).save();
    }

    document.getElementById('btnPDF').onclick = gen_pdf;
</script>