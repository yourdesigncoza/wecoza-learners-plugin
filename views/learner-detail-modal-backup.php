<?php
/**
 * Learner Detail Modal View - Comprehensive Tabbed Interface
 * 
 * This template generates the HTML content for the learner detail modal
 * Used by AJAX handler get_learner_data_by_id
 * 
 * @var object $learner The learner data object
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get uploads directory for portfolio links
$uploads_dir = wp_upload_dir();
$uploadsUrl = $uploads_dir['baseurl'];
?>

<div class="learner-detail-comprehensive">
    <!-- Header Section with Basic Info -->
    <div class="container-fluid lh-1 mb-2">
        <div class="row border">
            <div class="col-1 p-2 text-black fw-medium border-end">First Name</div>
            <div class="col-1 p-2 text-black fw-medium border-end">Surname</div>
            <div class="col-1 p-2 text-black fw-medium border-end">Gender</div>
            <div class="col-1 p-2 text-black fw-medium border-end">Race</div>
            <div class="col-2 p-2 text-black fw-medium border-end">Tel Number</div>
            <div class="col-2 p-2 text-black fw-medium border-end">Email Address</div>
            <div class="col-1 p-2 text-black fw-medium border-end">City/Town</div>
            <div class="col-2 p-2 text-black fw-medium border-end">Employment Status</div>
            <div class="col-1 p-2">&nbsp;</div>
        </div>
        
        <div class="row border border-top-0">
            <div class="col-1 p-2 border-end"><?php echo esc_html($learner->first_name ?? ''); ?></div>
            <div class="col-1 p-2 border-end"><?php echo esc_html($learner->surname ?? ''); ?></div>
            <div class="col-1 p-2 border-end"><?php echo esc_html($learner->gender ?? ''); ?></div>
            <div class="col-1 p-2 border-end"><?php echo esc_html($learner->race ?? ''); ?></div>
            <div class="col-2 p-2 border-end"><?php echo esc_html($learner->tel_number ?? ''); ?></div>
            <div class="col-2 p-2 border-end"><?php echo esc_html($learner->email_address ?? ''); ?></div>
            <div class="col-1 p-2 border-end"><?php echo esc_html($learner->city_town_name ?? ''); ?></div>
            <div class="col-2 p-2 border-end"><?php echo esc_html($learner->employment_status ?? ''); ?></div>
            <div class="col-1 p-1 border-end">
                <button class="btn btn-sm bg-warning-subtle edit-learner-btn" data-id="<?php echo esc_attr($learner->id ?? ''); ?>">Edit</button>
                <button class="btn btn-sm bg-danger-subtle delete-learner-btn" data-id="<?php echo esc_attr($learner->id ?? ''); ?>">Delete</button>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    
    <!-- Tab Navigation -->
    <div class="gtabs ydcoza-tab mb-3">
        <div class="ydcoza-tab-buttons mb-2">
            <button data-toggle="tab" data-tabs=".gtabs.ydcoza-tab" data-tab=".tab-1" class="active">
                <span class="ydcoza-badge">Learner Info.</span>
            </button>
            <button data-toggle="tab" data-tabs=".gtabs.ydcoza-tab" data-tab=".tab-2">
                <span class="ydcoza-badge">Placement Assessment Information</span>
            </button>
            <button data-toggle="tab" data-tabs=".gtabs.ydcoza-tab" data-tab=".tab-3">
                <span class="ydcoza-badge">Current Status</span>
            </button>
            <button data-toggle="tab" data-tabs=".gtabs.ydcoza-tab" data-tab=".tab-4">
                <span class="ydcoza-badge">Progressions</span>
            </button>
        </div>
        <div class="clearfix"></div>
    </div>