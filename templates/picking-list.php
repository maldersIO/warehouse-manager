<?php
/*
Template Name: Print Picking List
*/

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
                    <h5 class="card-header">Picking List</h5>
                    <div class="card-body pt-0">
                        <?php
                        $picking_list_id = $_GET['pl'] ?? '';
                        $warehouse_id = $_GET['wh'] ?? '';

                        if (empty($picking_list_id) || empty($warehouse_id)) {
                            $noLabels = true;
                        } else {
                            $noLabels = false;
                            $rows = get_picking_data($picking_list_id, $warehouse_id);
                            $data = unserialize($rows[0]->picking_list);
                        }

                        function get_picking_data($picking_list_id, $warehouse_id)
                        {
                            global $wpdb;

                            $table_name = $wpdb->prefix . 'dwm_picking_list';
                            $table_goods = $wpdb->prefix . 'dwm_goods_received';
                            $table_movement_list_items = $wpdb->prefix . 'dwm_movement_list_items';
                            $posts = $wpdb->prefix . 'posts';

                            $sql = "SELECT *
                                        FROM $table_goods goods
                                        LEFT JOIN $table_movement_list_items mli ON mli.bin_id = goods.bin_id
                                        LEFT JOIN $posts posts ON posts.ID = goods.product_name
                                        LEFT JOIN $table_name pl ON pl.movement_list_id = mli.movement_list_id
                                        WHERE pl.picking_list_id = '$picking_list_id' AND goods.warehouse_id= '$warehouse_id' ";

                            return $wpdb->get_results($sql);
                        }

                        ?>
                        <div class="picking-list">
                            <div id="root">
                                <div class="row">
                                        <div class="col-md-12">
                                            <h1>Pick List</h1>
                                            <?php
                                            $warehouse_name = get_post($rows[0]->warehouse_id)->post_title;
                                            $custom_input_label = get_option('custom_input_label', '');
                                            $custom_input_2_label = get_option('custom_input_2_label', '');
                                            $custom_input_3_label = get_option('custom_input_3_label', '');
                                            $custom_input_4_label = get_option('custom_input_4_label', '');
                                            $custom_input_5_label = get_option('custom_input_5_label', '');
                                            $count = 1;
                                            $total_weight = 0;
                                            $total_bags = 0;
                                            ?>

                                            <h3>Warehouse: <?php echo  $warehouse_name; ?></h3>

                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Location</th>
                                                        <th scope="col">To</th>
                                                        <th scope="col">Item</th>
                                                        <th scope="col">Pallet ID</th>
                                                        <?php if ($custom_input_label != '') { ?>
                                                            <th scope="col"><?php echo get_option('custom_input_label', 'Default Custom Input Label'); ?></th>
                                                        <?php } ?>
                                                        <?php if ($custom_input_2_label != '') { ?>
                                                            <th scope="col"><?php echo get_option('custom_input_2_label', 'Default Custom Input 2 Label'); ?></th>
                                                        <?php } ?>
                                                        <th scope="col">Total Weight (kg)</th>
                                                        <th scope="col">Number of units</th>
                                                        <th scope="col">Bag Size(kg)</th>
                                                        <th scope="col">Number of bags picked</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($rows as $index => $item) { 
                                                         $table_bays = $wpdb->prefix . 'dwm_bays';
                                                         $bay = $data[$index]['bay_id'];
                                                        $sql_bays = "SELECT name FROM $table_bays WHERE id = $bay";
                                                        $bay_result = $wpdb->get_row($sql_bays);
                                                        
                                                        $pack_size = get_post_meta($item->product_name, 'pack_size', true);
                                                        ?>
                                                        <tr>
                                                            <th scope="row"><?php echo $count; ?></th>
                                                            <td><?php echo $item->rack . "-" . $item->level . "-" . $item->position ?></td>
                                                            <td><?php echo $bay_result->name ?></td>
                                                            <td><?php echo $item->post_title ?></td>
                                                            <td><?php echo $item->pallet_id ?></td>
                                                            <?php if ($custom_input_label != '') { ?>
                                                                <td><?php echo $item->custom_input; ?></td>
                                                            <?php } ?>
                                                            <?php if ($custom_input_2_label != '') { ?>
                                                                <td><?php echo $item->custom_input_2; ?></td>
                                                            <?php } ?>
                                                            <td><?php echo $data[$index]['quantity'] * $pack_size ?></td>
                                                            <td><?php echo $data[$index]['quantity'] ?></td>
                                                            <td>BAG <?php echo $pack_size ?></td>
                                                            <td><input type="text"></td>
                                                        </tr>
                                                        <?php $total_weight = $total_weight + $data[$index]['quantity'] * $pack_size; ?>
                                                        <?php $total_bags = $total_bags + $data[$index]['quantity']; ?>
                                                        <?php $count++; ?>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-8">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <td><b>Total Weight(kg)</b>: <?php echo $total_weight ?></td>
                                                    <td><b>Total Bags/Units</b>: <?php echo $total_bags ?></td>
                                                    <td><b>Bags Pulled</b></td>
                                                    <td><input type="text"></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td><b>Bags Inspected</b></td>
                                                    <td><input type="text"></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td><b>Bags Packed</b></td>
                                                    <td><input type="text"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-2 mt-4">
                    <h5 class="card-header">Generate PDF</h5>
                    <div class="card-body pt-0 mt-4">
                        <?php if ($noLabels) : ?>
                            <h1 style="text-align:center">No labels to print</h1>
                        <?php else : ?>
                            <button id="btnPDF" class="btn btn-primary">Generate PDF</button>
                            <?php endif; ?>
                            <button onclick="goBack()" class="btn btn-secondary">Go Back</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
            filename: ml.picking_list_id + '-PICKING-LIST.pdf',
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