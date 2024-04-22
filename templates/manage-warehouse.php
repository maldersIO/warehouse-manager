<?php

$post_id = $_GET['post_id'];
$title = get_the_title();
$warehouse_id = get_the_ID();
// Get the custom field values
$racks = get_post_meta($post_id, 'racks', true);
$levels_per_rack = get_post_meta($post_id, 'levels_per_rack', true);
$capacities = get_post_meta($post_id, 'capacities', true);

?>

<div class="single-warehouse warehouse">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php
                if ($message = get_transient('submission_message')) {
                    echo '<div class="alert alert-success" role="alert">' . $message . '</div>';
                    delete_transient('submission_message'); // Clear the transient after showing the message
                }
                ?>
                <h1 class="warehouse-heading"><?php echo $title; ?></h1>
            </div>
            <div class="col-md-12 mb-5">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" aria-current="page" href="#" id="layout" data-bs-toggle="tab" data-bs-target="#layout-pane" role="tab" aria-controls="layout-pane" aria-selected="true">Warehouse layout</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" aria-current="page" href="#" id="receive-goods-tab" data-bs-toggle="tab" data-bs-target="#receive-goods-pane" role="tab" aria-controls="receive-goods-pane" aria-selected="false">Receive goods</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <?php
                        global $wpdb;

                        $table_name = $wpdb->prefix . 'dwm_movement_list';
                        $sql = $wpdb->prepare("SELECT COUNT(ml.id) as total_open
                          FROM $table_name ml
                          WHERE  ml.warehouse_id = $post_id AND movement_status = 1");
                        $row = $wpdb->get_row($sql);
                        ?>
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Movements
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger pb-0"><?php echo $row->total_open; ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" id="all-picking" data-bs-toggle="tab" data-bs-target="#all-movement-list" type="button" role="tab" aria-controls="all-movement-list" aria-selected="false">All movements</a></li>
                            <li><a class="dropdown-item" href="#" id="current-picking" data-bs-toggle="tab" data-bs-target="#current-picking-list" type="button" role="tab" aria-controls="current-picking-list" aria-selected="false">Current picking list</a></li>
                        </ul>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" aria-current="page" href="#" id="search-tab" data-bs-toggle="tab" data-bs-target="#search-pane" role="tab" aria-controls="search-pane" aria-selected="false">Search</a>
                    </li>
                </ul>
                <div class="tab-content px-3 py-0" id="myTabContent">
                    <div class="tab-pane fade show active" id="layout-pane" role="tabpanel" aria-labelledby="layout" tabindex="0">
                        <div class="container">
                            <div class="row py-3">
                                <div class="col">
                                    <h2>Warehouse layout</h2>
                                    <div class="warehouse-layout">
                                        <?php echo warehouse_svg($racks, $levels_per_rack, $capacities, $post_id); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="receive-goods-pane" role="tabpanel" aria-labelledby="receive-goods" tabindex="0">
                        <div class="row py-3">
                            <div class="col">
                                <div class="goods-form">
                                    <?php echo do_shortcode("[dwm_goods_receiving_form racks='$racks' levels_per_rack='$levels_per_rack' capacities='$capacities']"); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="current-picking-list" role="tabpanel" aria-labelledby="current-picking" tabindex="0">
                        <div class="row py-3">
                            <div class="col">
                                <div class="picking-list-form">
                                    <h2>Current picking list</h2>
                                    <?php

                                    // Get the current user's ID
                                    $user_id = get_current_user_id();

                                    // Retrieve the picking list from user meta
                                    $picking_list = get_user_meta($user_id, 'picking_list', true);


                                    // Check if the picking list is not empty or not set
                                    if (!empty($picking_list)) {
                                    ?>

                                        <div class="row" style='margin-bottom:30px;'>
                                            <div class="col-sm-3">
                                                <div class="card selectable-card selected" id="externalCard" onclick="selectCard('external')">
                                                    <div class="card-body">
                                                        <h5 class="card-title">External</h5>
                                                        <p class="card-text">Movements out of the warehouse</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="card selectable-card" id="internalCard" onclick="selectCard('internal')">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Internal</h5>
                                                        <p class="card-text">Movements internally within the warehouse</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div id='external'>
                                            <form method="post" id="picking-list-form">
                                                
                                                <div class="col">
                                                    <div class="form-floating">
                                                        <input type="text" id="reference_number" name="reference_number" class="form-control" required>
                                                        <label for="pallet_id" class="form-label">Refernce number:</label>
                                                    </div>
                                                </div>
                                                <table class="table mb-0 table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Bin</th>
                                                            <th scope="col">Product name</th>
                                                            <th scope="col">Units</th>
                                                            <th scope="col" id='tabl_header_label'>Bay</th>
                                                            <th scope="col">Expiry date</th>
                                                            <th scope="col" class="text-right">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>


                                                        <div id="external">
                                                            <?php
                                                            foreach ($picking_list as $index => $item) {

                                                                global $wpdb;

                                                                $goods_received_id = $item['goods_received_id'];

                                                                $table_name = $wpdb->prefix . 'dwm_goods_received';
                                                                $sql = $wpdb->prepare("SELECT *
                                                                                FROM $table_name
                                                                                WHERE  id = $goods_received_id AND warehouse_id=$post_id;");
                                                                $row = $wpdb->get_row($sql);

                                                                $product_name = get_post($row->product_name)->post_title;

                                                                $pack_size = get_post_meta($row->product_name, 'pack_size', true);


                                                            ?>
                                                                <tr>
                                                                    <th scope="row"><a class="nav-link" onclick="myFunction('<?php echo esc_html($item['bin_id']); ?>')"><?php echo esc_html($item['bin_id']); ?></a></th>
                                                                    <td><?php echo $product_name; ?></td>
                                                                    <td>
                                                                        <div class="row g-3 align-items-center">
                                                                            <div class="col-auto">
                                                                                <input type="number" class="form-control quantity-input" placeholder="Enter units" data-index="<?php echo $index; ?>">
                                                                            </div>
                                                                            <div class="col-auto">
                                                                                <?php echo $row->amount_of_bags; ?> units(<?php echo $row->amount_of_bags * $pack_size; ?>kg) <small> available</small>
                                                                            </div>
                                                                        </div>

                                                                    </td>
                                                                    <td>
                                                                        <select class="form-select" id="bay" name="picking_list[<?php echo $index; ?>][bay_id]" aria-label="Default select example" placeholder="Select a bay" required>
                                                                            <option value="" selected disabled>Select a bay</option>
                                                                            <?php

                                                                            $bays_tbl = $wpdb->prefix . 'dwm_bays';
                                                                            $sql = "SELECT * FROM $bays_tbl WHERE warehouse_id = $post_id AND type_id IN (3)";

                                                                            $bay_results = $wpdb->get_results($sql);

                                                                            if ($bay_results) {
                                                                                foreach ($bay_results as $bay) {
                                                                                    echo '<option value="' . $bay->id . '">' . $bay->name . '</option>';
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>

                                                                    </td>
                                                                    <td><span class="badge rounded-pill text-bg-success"><?php echo $row->expiry_date; ?></span></td>
                                                                    <td class="text-right"><a class="nav-link" data-bs-toggle="tooltip" data-bs-title="Remove" onclick="removeFromPickingList('<?php echo esc_html($item['bin_id']); ?>')"><i class="fa-solid fa-xmark"></i></a></td>
                                                                </tr>
                                                                <input type="hidden" name="picking_list[<?php echo $index; ?>][bin_id]" value="<?php echo esc_html($item['bin_id']); ?>">
                                                                <input type="hidden" name="picking_list[<?php echo $index; ?>][quantity]" class="hidden-quantity" value="<?php $row->amount_of_bags ?>">
                                                                <input type="hidden" name="picking_list[<?php echo $index; ?>][goods_received_id]" value="<?php echo $goods_received_id; ?>">

                                                            <?php } ?>
                                                    </tbody>
                                                </table>
                                                <div class="col">
                                                    <div class="form-floating">
                                                        <input type="textarea" id="note" name="note" class="form-control" required>
                                                        <label for="pallet_id" class="form-label">Note:</label>
                                                    </div>
                                                </div>
                                                <br>
                                                <input type="hidden" name="warehouse_id" value="<?php echo $post_id; ?>">
                                                <input type="submit" name="submit_picking_list" class="btn btn-primary" value="Create picking list" />
                                            </form>
                                        </div>

                                        <div id='internal' style='display: none;'>
                                            <form method="post" id="picking-list-form">
                                                <table class="table mb-0 table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Bin</th>
                                                            <th scope="col">Product name</th>
                                                            <th scope="col">Units</th>
                                                            <th scope="col">Bin</th>
                                                            <th scope="col">Expiry date</th>
                                                            <th scope="col" class="text-right">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <div id="external">
                                                            <?php
                                                            foreach ($picking_list as $index => $item) {

                                                                global $wpdb;

                                                                $goods_received_id = $item['goods_received_id'];

                                                                $table_name = $wpdb->prefix . 'dwm_goods_received';
                                                                $sql = $wpdb->prepare("SELECT *
                                                                                FROM $table_name
                                                                                WHERE  id = $goods_received_id AND warehouse_id=$post_id;");
                                                                $row = $wpdb->get_row($sql);

                                                                $product_name = get_post($row->product_name)->post_title;

                                                                $pack_size = get_post_meta($row->product_name, 'pack_size', true);


                                                            ?>
                                                                <tr>
                                                                    <th scope="row"><a class="nav-link" onclick="myFunction('<?php echo esc_html($item['bin_id']); ?>')"><?php echo esc_html($item['bin_id']); ?></a></th>
                                                                    <td><?php echo $product_name; ?></td>
                                                                    <td>
                                                                        <div class="row g-3 align-items-center">
                                                                            <div class="col-auto">
                                                                                <input type="number" class="form-control quantity-input" placeholder="Enter units" data-index="<?php echo $index; ?>">
                                                                            </div>
                                                                            <div class="col-auto">
                                                                                <?php echo $row->amount_of_bags; ?> units(<?php echo $row->amount_of_bags * $pack_size; ?>kg) <small> available</small>
                                                                            </div>
                                                                        </div>

                                                                    </td>
                                                                    <td>
                                                                        <select class="form-select" id="new_bin_bay" name="picking_list[<?php echo $index; ?>][new_bin_bay]" aria-label="Default select example" placeholder="Select a bin" required>
                                                                            <option value="" selected disabled>Select a bin or bay</option>
                                                                            <?php

                                                                            $goods_tbl = $wpdb->prefix . 'dwm_goods_received';
                                                                            $bays_tbl = $wpdb->prefix . 'dwm_bays';
                                                                            $sql = "SELECT * FROM $goods_tbl WHERE warehouse_id = $post_id AND bin_status NOT IN (4);";

                                                                            $bin_results = $wpdb->get_results($sql);

                                                                            $sql = "SELECT * FROM $goods_tbl WHERE warehouse_id = $post_id AND bin_status NOT IN (4);";
                                                                            $bay_sql = "SELECT * FROM $bays_tbl WHERE warehouse_id = $post_id AND type_id = 2";

                                                                            $bin_results = $wpdb->get_results($sql);
                                                                            $bay_results = $wpdb->get_results($bay_sql);



                                                                            if ($bin_results) {
                                                                                foreach ($bin_results as $bin) {
                                                                                    echo '<option value="' . $bin->bin_id . '">' . $bin->bin_id . '</option>';
                                                                                }
                                                                            }

                                                                            if ($bay_results) {
                                                                                foreach ($bay_results as $bay) {
                                                                                    echo '<option value="' . $bay->id . '">' . $bay->name . '</option>';
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </td>
                                                                    <td><span class="badge rounded-pill text-bg-success"><?php echo $row->expiry_date; ?></span></td>
                                                                    <td class="text-right"><a class="nav-link" data-bs-toggle="tooltip" data-bs-title="Remove" onclick="removeFromPickingList('<?php echo esc_html($item['bin_id']); ?>')"><i class="fa-solid fa-xmark"></i></a></td>
                                                                </tr>
                                                                <input type="hidden" name="picking_list[<?php echo $index; ?>][bin_id]" value="<?php echo esc_html($item['bin_id']); ?>">
                                                                <input type="hidden" name="picking_list[<?php echo $index; ?>][quantity]" class="hidden-quantity" value="<?php $row->amount_of_bags ?>">
                                                                <input type="hidden" name="picking_list[<?php echo $index; ?>][goods_received_id]" value="<?php echo $goods_received_id; ?>">

                                                            <?php } ?>
                                                    </tbody>
                                                </table>
                                                <div class="col">
                                                    <div class="form-floating">
                                                        <input type="text" id="note" name="note" class="form-control" required>
                                                        <label for="pallet_id" class="form-label">Note:</label>
                                                    </div>
                                                </div>
                                                <br>
                                                <input type="hidden" name="warehouse_id" value="<?php echo $post_id; ?>">
                                                <input type="submit" name="submit_picking_list" class="btn btn-primary" value="Create picking list" />
                                            </form>
                                        </div>
                                    <?php  } else {
                                        // Handle case where there is no picking list or it's empty
                                        echo 'Your picking list is empty.';
                                    } ?>
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            // Listen for changes in quantity inputs
                                            var quantityInputs = document.querySelectorAll('.quantity-input');
                                            quantityInputs.forEach(function(input) {
                                                input.addEventListener('change', function() {
                                                    var index = this.getAttribute('data-index');
                                                    var hiddenQuantityInput = document.querySelector('input[name="picking_list[' + index + '][quantity]"]');
                                                    // console.log(hiddenQuantityInput.value);
                                                    if (hiddenQuantityInput) {
                                                        hiddenQuantityInput.value = this.value;

                                                    }
                                                });
                                            });
                                        });
                                    </script>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="all-movement-list" role="tabpanel" aria-labelledby="all-picking" tabindex="0">
                        <div class="row py-3">
                            <div class="col">
                                <div class="picking-list-form">
                                    <h2>All movements</h2>
                                    <table class="table mb-0 table-striped" id="all_movement_list_table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Movement ID</th>
                                                <th scope="col">Created date</th>
                                                <th scope="col">Created by</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Status</th>
                                                <th scope="col" class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            global $wpdb;

                                            $table_name = $wpdb->prefix . 'dwm_movement_list';
                                            $table_bays = $wpdb->prefix . 'dwm_bays';
                                            $table_movement_statuses = $wpdb->prefix . 'dwm_movement_statuses';
                                            $table_picking_list = $wpdb->prefix . 'dwm_picking_list';
                                            $sql = $wpdb->prepare("SELECT ml.*, bays.name as bay_name, ms.name as status_name, pl.picking_list_id
                                             FROM $table_name ml
                                             LEFT JOIN $table_bays bays ON ml.bay = bays.id
                                             LEFT JOIN $table_movement_statuses ms ON ms.id = ml.movement_status
                                             LEFT JOIN $table_picking_list pl ON pl.movement_list_id = ml.movement_list_id
                                             WHERE  ml.warehouse_id = $post_id ORDER BY ml.id DESC");
                                            $rows = $wpdb->get_results($sql);

                                            foreach ($rows as $row) {
                                                $created_by = $row->created_by;
                                                $confirmed_by = $row->confirmed_by;
                                                $created_by_user_data = get_userdata($created_by);
                                                $confirmed_by_user_data = get_userdata($confirmed_by);

                                            ?>
                                                <tr>
                                                    <th scope="row">
                                                        <a class="nav-link" onclick="movementList('<?php echo $row->movement_list_id; ?>', <?php echo $post_id; ?>,'<?php echo $row->picking_list_id; ?>')"><?php echo $row->movement_list_id; ?></a>
                                                        <a class="nav-link" data-bs-toggle="tooltip" data-bs-title="View" onclick="movementList('<?php echo $row->movement_list_id; ?>', <?php echo $post_id; ?>, '<?php echo $row->picking_list_id; ?>')"><i class="fa-regular fa-eye"></i></a>
                                                    </th>
                                                    <td><?php echo $row->created_date; ?></td>
                                                    <?php
                                                    if (empty($created_by_user_data->first_name)) {
                                                    ?>
                                                        <td><?php echo $created_by_user_data->display_name ?></td>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <td><?php echo $created_by_user_data->first_name . ' ' . $created_by_user_data->last_name;; ?></td>
                                                    <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if (!empty($row->picking_list_id)) {
                                                    ?>
                                                        <td><span class="badge rounded-pill text-bg-info">Outbound</td>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <td><span class="badge rounded-pill text-bg-secondary">Receiving</span></td>
                                                    <?php
                                                    }
                                                    ?>
                                                    <?php
                                                    if ($row->movement_status == 3) {
                                                    ?>
                                                        <td><span class="badge rounded-pill text-bg-success"><?php echo $row->status_name; ?></span></td>
                                                    <?php
                                                    } else if ($row->movement_status == 1) {
                                                    ?>
                                                        <td><span class="badge rounded-pill text-bg-warning"><?php echo $row->status_name; ?></span></td>
                                                    <?php
                                                    } else if ($row->movement_status == 2) {
                                                    ?>
                                                        <td><span class="badge rounded-pill text-bg-danger"><?php echo $row->status_name; ?></span></td>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <td><span class="badge rounded-pill text-bg-info"><?php echo $row->status_name; ?></span></td>
                                                    <?php
                                                    }
                                                    ?>
                                                    <td class="text-right">
                                                        <?php
                                                        (!empty($row->picking_list_id)) ? $picking_id = $row->picking_list_id : $picking_id = 0;
                                                        ?>
                                                        <?php if ($row->movement_status == 1) { ?>
                                                            <a class="nav-link" data-bs-toggle="tooltip" data-bs-title="Labels" href="create-labels?ml=<?php echo $row->movement_list_id; ?>&wh=<?php echo $post_id; ?>"><i class="fa-solid fa-tags"></i></a>
                                                            <a class="nav-link" onclick="completeMovement('<?php echo $row->movement_list_id; ?>', <?php echo get_current_user_id() ?>, '<?php echo $picking_id; ?>')" data-bs-toggle="tooltip" data-bs-title="Complete"><i class="fa-regular fa-circle-check"></i></a>
                                                            <!-- <a class="nav-link" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> -->
                                                            <?php if (!empty($row->picking_list_id)) { ?>
                                                                <a class="nav-link" data-bs-toggle="tooltip" data-bs-title="Picking List" href="picking-list?pl=<?php echo $row->picking_list_id; ?>&wh=<?php echo $post_id; ?>"><i class="fa-solid fa-list"></i></a>
                                                            <?php } ?>
                                                            <a class="nav-link" onclick="cancelMovement('<?php echo $row->movement_list_id; ?>', <?php echo get_current_user_id() ?>)" data-bs-toggle="tooltip" data-bs-title="Cancel"><i class="fa-solid fa-xmark"></i></a>

                                                        <?php } else { ?>

                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="search-pane" role="tabpanel" aria-labelledby="stocktake" tabindex="0">
                        <div class="row pb-3">
                            <div class="col">
                                <div class="export-form">
                                    <table class="table mb-0 table-striped" id="table_search">
                                        <thead>
                                            <tr>
                                                <th scope="col">Bin</th>
                                                <th scope="col">Product name</th>
                                                <th scope="col">Units</th>
                                                <th scope="col">Batch</th>
                                                <th scope="col">Pallet ID</th>
                                                <th scope="col">Expiry date</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            global $wpdb;

                                            $table_name = $wpdb->prefix . 'dwm_goods_received';
                                            $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE bin_status = 1 AND warehouse_id = %s", $post_id);
                                            $rows = $wpdb->get_results($sql);

                                            foreach ($rows as $row) {

                                                $product_name = get_post($row->product_name)->post_title;
                                                $pack_size = get_post_meta($row->product_name, 'pack_size', true);
                                            ?>
                                                <tr>
                                                    <th scope="row"><a class="nav-link"><?php echo $row->bin_id ?></a></th>
                                                    <td><?php echo $product_name ?></td>
                                                    <td>
                                                    <div class="row g-3 align-items-center">
                                                        <div class="col-auto">
                                                            <?php echo $pack_size * $row->amount_of_bags ?>kg <small>(<?php echo $row->amount_of_bags ?> units) available </small>
                                                        </div>
                                                    </div>
                                                    </td>
                                                    <td><?php echo $row->custom_input_2 ?></td>
                                                    <td><?php echo $row->pallet_id ?></td>
                                                    <td><?php echo $row->expiry_date ?></td>
                                                    <td class="text-right">
                                                        <a class="nav-link" onclick="addToPickingList('<?php echo $row->bin_id ?>')"><i class="fa-solid fa-plus" data-bs-toggle="tooltip" data-bs-title="Add to picking list"></i></a>
                                                        <a class="nav-link"><i class="fa-solid fa-arrow-up-right-from-square" data-bs-toggle="tooltip" data-bs-title="Move"></i></a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="card mb-2 mt-4">
                            <h5 class="card-header">Expiring products</h5>
                            <div class="card-body pt-0">
                                <table class="table mb-0 table-striped" id="table_expiring_products">
                                    <thead>
                                        <tr>
                                            <th scope="col">Bin</th>
                                            <th scope="col">Product name</th>
                                            <th scope="col">Stock</th>
                                            <th scope="col">Batch</th>
                                            <th scope="col">Expiry date</th>
                                            <th scope="col" class="text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        global $wpdb;

                                        $table_name = $wpdb->prefix . 'dwm_goods_received';
                                        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE  (
                                                                expiry_date <= CURDATE() 
                                                                OR expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                                                            ) AND bin_status = 1 AND warehouse_id = %s", $post_id);
                                        $rows = $wpdb->get_results($sql);

                                        foreach ($rows as $row) {

                                            $product_name = get_post($row->product_name)->post_title;
                                            $pack_size = get_post_meta($row->product_name, 'pack_size', true);
                                            $inventory_id = get_post_meta($row->product_name, 'inventory_id', true);

                                        ?>
                                            <tr>
                                                <th scope="row"><a class="nav-link" onclick="myFunction('<?php echo $row->bin_id ?>')"><?php echo $row->bin_id ?></a></th>
                                                <td><?php echo $product_name . "(" . $inventory_id . ")" ?></td>
                                                <td>
                                                    <div class="row g-3 align-items-center">
                                                        <div class="col-auto">
                                                            <?php echo $pack_size * $row->amount_of_bags ?>kg <small>(<?php echo $row->amount_of_bags ?> units) available </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo $row->custom_input_2 ?></td>
                                                <?php if ($row->expiry_date < date("Y-m-d")) { ?>
                                                    <td><span class="badge rounded-pill text-bg-danger"><?php echo $row->expiry_date ?></span></td>
                                                <?php } else { ?>
                                                    <td><span class="badge rounded-pill text-bg-warning"><?php echo $row->expiry_date ?></span></td>
                                                <?php } ?>
                                                <td class="text-right">
                                                    <a class="nav-link"><i class="fa-solid fa-plus" data-bs-toggle="tooltip" data-bs-title="Add to picking list" onclick="addToPickingList('<?php echo $row->bin_id ?>', <?php echo $row->id ?>)"></i></a>

                                                    <!-- <a class="nav-link"><i class="fa-solid fa-arrow-up-right-from-square" data-bs-toggle="tooltip" data-bs-title="Move"></i></a> -->
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <p class="lh-sm description">Current warning limit set to 30 days</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-5 pt-1">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <h5 class="card-header">Current warehouse capacity</h5>
                            <?php
                            global $wpdb;

                            $table_name = $wpdb->prefix . 'dwm_goods_received';
                            $filled_sql = $wpdb->prepare("SELECT COUNT(*) AS total_filled FROM $table_name WHERE bin_status = 1 AND warehouse_id = %s", $post_id);
                            $filled_row = $wpdb->get_row($filled_sql);
                            $reserved_sql = $wpdb->prepare("SELECT COUNT(*) AS total_filled FROM $table_name WHERE bin_status = 2 AND warehouse_id = %s", $post_id);
                            $reserved_row = $wpdb->get_row($reserved_sql);

                            $total_bins = get_post_meta($post_id, 'total_bins', true);

                            $percentage = (($filled_row->total_filled + $reserved_row->total_filled) / $total_bins) * 100;


                            // Calculate separate percentages
                            $filled_percentage = ($filled_row->total_filled / $total_bins) * 100;
                            $reserved_percentage = ($reserved_row->total_filled / $total_bins) * 100;
                            ?>
                            <div class="card-body pb-0">
                                <div class="container text-center">
                                    <div class="row align-items-center">
                                        <div class="col pb-2">
                                            <h3 class="display-5 mb-0"><i class="fa-solid fa-cubes"></i></h3>
                                            <p class="description"><strong>Total bins: </strong><?php echo $total_bins; ?></p>
                                        </div>
                                        <div class="col pb-2">
                                            <h3 class="display-1 mb-0 fw-bolder"><?php echo round($percentage, 0); ?><small style="font-size: .5em!important;">%</small></h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="progress-stacked">
                                    <div class="progress" role="progressbar" aria-label="Segment one" aria-valuenow="<?php echo $filled_row->total_filled; ?>" aria-valuemin="0" aria-valuemax="<?php echo $total_bins; ?>" style="width: <?php echo round($filled_percentage, 0); ?>%" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $filled_row->total_filled; ?>">
                                        <div class="progress-bar bg-primary"></div>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Segment two" aria-valuenow="<?php echo $reserved_row->total_filled; ?>" aria-valuemin="0" aria-valuemax="<?php echo $total_bins; ?>" style="width: <?php echo round($reserved_percentage, 0); ?>%" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo $reserved_row->total_filled; ?>">
                                        <div class="progress-bar bg-secondary"></div>
                                    </div>
                                    <div class="progress" role="progressbar" aria-label="Segment two" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                        <div class="progress-bar progress-bar-blank"><?php echo $total_bins - ($filled_row->total_filled + $reserved_row->total_filled); ?></div>
                                    </div>
                                </div>
                                <p class="lh-sm mt-2 description">Number of bins filled reserved or empty in the <?php echo $title; ?> warehouse.</p>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <h5 class="card-header">Current product breakdown</h5>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger pb-0">!</span>
                            <div class="card-body pt-0">
                                <div style="height: 200px;">
                                    <canvas id="doughnutChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <h5 class="card-header">Goods movement</h5>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger pb-0">!</span>
                            <div class="card-body pt-0">
                                <div style="height: 200px;">
                                    <canvas id="past28days"></canvas>
                                </div>
                                <div style="height: 200px;">
                                    <canvas id="chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>