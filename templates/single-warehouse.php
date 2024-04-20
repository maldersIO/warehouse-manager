<?php
get_header();

$post_id = get_the_ID();
$title = get_the_title();
// Get the custom field values
$racks = get_post_meta(get_the_ID(), 'racks', true);
$levels_per_rack = get_post_meta(get_the_ID(), 'levels_per_rack', true);
$capacities = get_post_meta(get_the_ID(), 'capacities', true);
$warehouse = get_post_meta(get_the_ID(), 'warehouse_svg', true);

if (!is_user_logged_in()) {
    echo 'Please log in to view this content.';
    return; // Stop further processing if the user is not logged in.
}
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
            <div class="col-md-9 mb-5">
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
                                    <table class="table mb-0 table-striped">
                                        <thead>
                                            <tr>
                                                <th scope="col">Bin</th>
                                                <th scope="col">Product name</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">Batch</th>
                                                <th scope="col">Expiry date</th>
                                                <th scope="col" class="text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row"><a class="nav-link" onclick="myFunction('12-3-1')">12-3-1</a></th>
                                                <td>AvailaZn-13320</td>
                                                <td>
                                                    <div class="row g-3 align-items-center">
                                                        <div class="col-auto">
                                                            <input type="number" class="form-control" id="exampleFormControlInput1" placeholder="Enter quantity" value="1000">
                                                        </div>
                                                        <div class="col-auto">
                                                            1000kg <small>(40 units)</small>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td>11345</td>
                                                <td><span class="badge rounded-pill text-bg-success">12/12/2024</span></td>
                                                <td class="text-right"><a class="nav-link" data-bs-toggle="tooltip" data-bs-title="Remove"><i class="fa-solid fa-xmark"></i></a></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><a class="nav-link" onclick="myFunction('10-5-2')">10-5-2</a></th>
                                                <td>AvailaZn-120</td>
                                                <td>
                                                    <div class="row g-3 align-items-center">
                                                        <div class="col-auto">
                                                            <input type="number" class="form-control" id="exampleFormControlInput1" placeholder="Enter quantity" value="1000">
                                                        </div>
                                                        <div class="col-auto">
                                                            1000kg <small>(40 units)</small>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td>11345</td>
                                                <td><span class="badge rounded-pill text-bg-warning">12/12/2023</span></td>
                                                <td class="text-right"><a class="nav-link" data-bs-toggle="tooltip" data-bs-title="Remove"><i class="fa-solid fa-xmark"></i></a></td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><a class="nav-link" onclick="myFunction('3-1-2')">3-1-2</a></th>
                                                <td>AvailaZn-120</td>
                                                <td>
                                                    <div class="row g-3 align-items-center">
                                                        <div class="col-auto">
                                                            <input type="number" class="form-control" id="exampleFormControlInput1" placeholder="Enter quantity" value="1000">
                                                        </div>
                                                        <div class="col-auto">
                                                            1000kg <small>(40 units)</small>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td>11345</td>
                                                <td><span class="badge rounded-pill text-bg-warning">12/12/2023</span></td>
                                                <td class="text-right"><a class="nav-link" data-bs-toggle="tooltip" data-bs-title="Remove"><i class="fa-solid fa-xmark"></i></a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <p class="lh-sm description">Current warning limit set to 30 days</p>
                                    <a class="btn btn-primary" href="#">Create picking list</a>
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
                                                <th scope="col">Bay</th>
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
                                            $sql = $wpdb->prepare("SELECT ml.*, bays.name as bay_name, ms.name as status_name
                                             FROM $table_name ml
                                             LEFT JOIN $table_bays bays ON ml.bay = bays.id
                                             LEFT JOIN $table_movement_statuses ms ON ms.id = ml.movement_status
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
                                                        <a class="nav-link" onclick="movementList('<?php echo $row->movement_list_id; ?>', <?php echo $post_id; ?>)"><?php echo $row->movement_list_id; ?></a>
                                                        <a class="nav-link" data-bs-toggle="tooltip" data-bs-title="View" onclick="movementList('<?php echo $row->movement_list_id; ?>', <?php echo $post_id; ?>)"><i class="fa-regular fa-eye"></i></a>
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
                                                    <td><span class="badge rounded-pill text-bg-primary"><?php echo $row->bay_name; ?></span></td>
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
                                                        <?php if ($row->movement_status == 1) { ?>
                                                            <a class="nav-link" data-bs-toggle="tooltip" data-bs-title="Labels" href="create-labels?ml=<?php echo $row->movement_list_id; ?>&wh=<?php echo $post_id; ?>"><i class="fa-solid fa-tags"></i></a>
                                                            <a class="nav-link" onclick="completeMovement('<?php echo $row->movement_list_id; ?>', <?php echo get_current_user_id() ?>)" data-bs-toggle="tooltip" data-bs-title="Complete"><i class="fa-regular fa-circle-check"></i></a>
                                                            <!-- <a class="nav-link" data-bs-toggle="tooltip" data-bs-title="Edit"><i class="fa-regular fa-pen-to-square"></i></a> -->
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
                                    <!-- <h2>Export inventory</h2>
                                    <p>Export entire <strong><?php echo $title; ?></strong> warehouse inventory to excel.</p>
                                    <a class="btn btn-primary" href="<?php echo plugins_url('warehouse-manager/export-to-excel.php?wh=' . $post_id . ''); ?>">Export now</a> -->

                                    <table class="table mb-0 table-striped" id="table_search">
                                        <thead>
                                            <tr>
                                                <th scope="col">Bin</th>
                                                <th scope="col">Product name</th>
                                                <th scope="col">Quantity</th>
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

                                            // echo "<pre>";
                                            //   print_r($rows);
                                            // echo "</pre>";

                                            foreach ($rows as $row) {

                                                $product_name = get_post($row->product_name)->post_title;
                                                $pack_size = get_post_meta($row->product_name, 'pack_size', true);
                                            ?>
                                                <tr>
                                                    <th scope="row"><a class="nav-link" onclick="myFunction('<?php echo $row->bin_id ?>')"><?php echo $row->bin_id ?></a></th>
                                                    <td><?php echo $product_name ?></td>
                                                    <td><?php echo $pack_size * $row->amount_of_bags ?>kg <small>(<?php echo $row->amount_of_bags ?> units)</small></td>
                                                    <td><?php echo $row->custom_input_2 ?></td>
                                                    <td><?php echo $row->pallet_id ?></td>
                                                    <td><?php echo $row->expiry_date ?></td>
                                                    <td class="text-right">
                                                        <a class="nav-link"><i class="fa-solid fa-plus" data-bs-toggle="tooltip" data-bs-title="Add to picking list"></i></a>
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
                <div class="card mb-2 mt-4">
                    <h5 class="card-header">Expiring products</h5>
                    <div class="card-body pt-0">
                        <table class="table mb-0 table-striped" id="table_expiring_products">
                            <thead>
                                <tr>
                                    <th scope="col">Bin</th>
                                    <th scope="col">Product name</th>
                                    <th scope="col">Quantity</th>
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
                                ?>
                                    <tr>
                                        <th scope="row"><a class="nav-link" onclick="myFunction('<?php echo $row->bin_id ?>')"><?php echo $row->bin_id ?></a></th>
                                        <td><?php echo $product_name ?></td>
                                        <td><?php echo $pack_size * $row->amount_of_bags ?>kg <small>(<?php echo $row->amount_of_bags ?> units)</small></td>
                                        <td><?php echo $row->custom_input_2 ?></td>
                                        <?php if ($row->expiry_date < date("Y-m-d")) { ?>
                                            <td><span class="badge rounded-pill text-bg-danger"><?php echo $row->expiry_date ?></span></td>
                                        <?php } else { ?>
                                            <td><span class="badge rounded-pill text-bg-warning"><?php echo $row->expiry_date ?></span></td>
                                        <?php } ?>
                                        <td class="text-right">
                                            <a class="nav-link"><i class="fa-solid fa-plus" data-bs-toggle="tooltip" data-bs-title="Add to picking list"></i></a>
                                            <!-- <a class="nav-link" onclick="moveBin('<?php echo $row->bin_id ?>')"><i class="fa-solid fa-arrow-up-right-from-square" data-bs-toggle="tooltip" data-bs-title="Move"></i></a> -->
                                            <a class="nav-link"><i class="fa-solid fa-arrow-up-right-from-square" data-bs-toggle="tooltip" data-bs-title="Move"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <p class="lh-sm description">Current warning limit set to 30 days</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-5 pt-1">
                <div class="card mb-4">
                    <h5 class="card-header">Current warehouse capacity</h5>
                    <?php
                    global $wpdb;

                    $table_name = $wpdb->prefix . 'dwm_goods_received';
                    $filled_sql = $wpdb->prepare("SELECT COUNT(*) AS total_filled FROM $table_name WHERE bin_status = 1 AND warehouse_id = %s", $post_id);
                    $filled_row = $wpdb->get_row($filled_sql);
                    $reserved_sql = $wpdb->prepare("SELECT COUNT(*) AS total_filled FROM $table_name WHERE bin_status = 2 AND warehouse_id = %s", $post_id);
                    $reserved_row = $wpdb->get_row($reserved_sql);

                    $total_bins = get_post_meta(get_the_ID(), 'total_bins', true);

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
        </div>
    </div>
</div>