<?php
function wecoza_display_learners_shortcode() {
    // Enqueue Bootstrap Table CSS and JS
    wp_enqueue_style('bootstrap-table-css', 'https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.css', array(), '1.21.4');
    wp_enqueue_script('bootstrap-table-js', 'https://unpkg.com/bootstrap-table@1.21.4/dist/bootstrap-table.min.js', array('jquery'), '1.21.4', true);
    
    // Enqueue necessary scripts
    wp_enqueue_script('learners-display-script', WECOZA_CHILD_URL . '/assets/learners/js/learners-display-shortcode.js', array('jquery', 'bootstrap-table-js'), WECOZA_PLUGIN_VERSION, true);

    // Localize script with necessary data
    wp_localize_script('learners-display-script', 'wecozaAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('learners-display-script_nonce')
    ));

    // Start output buffering
    ob_start();
    ?>

    <!-- Alert Container -->
    <div id="alert-container" class="alert-container"></div>
    <!-- Loader -->
    <div id="wecoza-loader-container">
        <button id="wecoza-loader-02" class="btn btn-primary mt-7" type="button">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Loading...
        </button>
    </div>

    <!-- Main Content Container -->
    <div id="learners-container" class="card shadow-none border my-5"style="display: none;">
        <div class="table-responsive">
            <table id="learners-display-data" class="table table-bordered ydcoza-compact-table">
                <thead>
                    <tr>
                        <th data-sortable="true">First Name</th>
                        <th data-sortable="true">Surname</th>
                        <th data-sortable="true">Gender</th>
                        <th data-sortable="true">Race</th>
                        <th data-sortable="true">Tel Number</th>
                        <th data-sortable="true">Email Address</th>
                        <th data-sortable="true">City/Town</th>
                        <th data-sortable="true">Employment Status</th>
                        <th class="text-nowrap text-center ydcoza-width-150">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Main Modal -->
    <div id="learnerModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-scrollable modal-xl modal-fullscreen-xxl-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="modalTitle">Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ydcoza-compact-content" id="modalContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-discovery-subtle btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('wecoza_display_learners', 'wecoza_display_learners_shortcode');