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
    
    <!-- Tab 1: Learner Info -->
    <div class="container-fluid gtab tab-1 border-top border-bottom lh-1 mb-2 active">
        <!-- Row 1 -->
        <div class="row border-start">
            <div class="col border-end p-2 bg-light d-flex align-items-center">Initials</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->initials ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">SA ID No</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->sa_id_no ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Passport Number</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->passport_number ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Alternative Tel Number</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->alternative_tel_number ?? ''); ?></div>
        </div>
        <!-- Row 2 -->
        <div class="row border-top border-start">
            <div class="col border-end p-2 bg-light d-flex align-items-center">Address Line 1</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->address_line_1 ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Address Line 2</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->address_line_2 ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Suburb</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->suburb ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Province/Region</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->province_region_name ?? ''); ?></div>
        </div>
        <!-- Row 3 -->
        <div class="row border-top border-start">
            <div class="col border-end p-2 bg-light d-flex align-items-center">Postal Code</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->postal_code ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Assessment Status</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->assessment_status ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Highest Qualification</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->highest_qualification ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Employer</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->employer_name ?? ''); ?></div>
        </div>
        <!-- Row 4 -->
        <div class="row border-top border-start">
            <div class="col border-end p-2 bg-light d-flex align-items-center">Disability Status</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->disability_status ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Profile Updated At</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->updated_at ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
        </div>
    </div>
    
    <!-- Tab 2: Placement Assessment Information -->
    <div class="container-fluid gtab tab-2 border-top border-bottom lh-1 mb-2">
        <!-- Row 1 -->
        <div class="row border-start">
            <div class="col border-end p-2 bg-light d-flex align-items-center">Placement Assessment Date</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->placement_assessment_date ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Num Placement Level</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->numeracy_level ?? ''); ?></div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Comm Placement Level</div>
            <div class="col border-end p-2 d-flex align-items-center"><?php echo esc_html($learner->communication_level ?? ''); ?></div>
        </div>
        <!-- Row 2 -->
        <div class="row border-top border-start">
            <div class="col border-end p-2 bg-light d-flex align-items-center">Report</div>
            <div class="col border-end p-2 d-flex align-items-center">Uploaded: <?php echo esc_html($learner->created_at ? date('m/d/Y', strtotime($learner->created_at)) : 'N/A'); ?></div>
            <div class="col-9 border-end d-flex align-items-center">
                <a href="<?php echo esc_url($uploadsUrl); ?>/reports/empty.pdf" download class="btn btn-sm btn-outline-discovery">Download</a>
            </div>
        </div>
    </div>
    
    <!-- Tab 3: Current Status / Class Information -->
    <div class="container-fluid gtab tab-3 border-top border-bottom mb-2 lh-1">
        <!-- Row 1 -->
        <div class="row border-start">
            <div class="col-2 border-end p-2 bg-light d-flex align-items-center">Current Level</div>
            <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Previous Levels</div>
            <div class="col border-end py-1 align-items-center">
                <div class="d-grid">
                    <button class="btn btn-sm btn-outline-discovery" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasHistory" aria-controls="offcanvasHistory">History</button>
                </div>
            </div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Class Schedule</div>
            <div class="col border-end py-1 align-items-center">
                <div class="d-grid">
                    <a class="btn btn-sm btn-outline-discovery" href="#">View Schedule</a>
                </div>
            </div>
        </div>
        
        <!-- Row 2 -->
        <div class="row border-top border-start">
            <div class="col-2 border-end p-2 bg-light d-flex align-items-center">Level Start Date</div>
            <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Level End Date</div>
            <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Current Num Level</div>
            <div class="col border-end d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Current Comm Level</div>
            <div class="col border-end d-flex align-items-center">&nbsp;</div>
        </div>
        
        <!-- Row 3 -->
        <div class="row border-top border-start">
            <div class="col-2 border-end p-2 bg-light d-flex align-items-center">Learner Class Status</div>
            <div class="col border-end p-2 d-flex align-items-center">CIC, CTR, DED, DMD, DRO, NRQ, RBE, RET, RSN</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Hours Trained</div>
            <div class="col border-end d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">Attendance's</div>
            <div class="col border-end d-flex align-items-center">&nbsp;</div>
        </div>
        
        <!-- Row 4 -->
        <div class="row border-top border-start">
            <div class="col-2 border-end p-2 bg-light d-flex align-items-center">Progress %</div>
            <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">&nbsp;</div>
            <div class="col border-end d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">&nbsp;</div>
            <div class="col border-end d-flex align-items-center">&nbsp;</div>
        </div>
    </div>
    
    <?php
    // Format portfolio links function
    function formatPortfolioLinks($portfolioDetails, $uploadsUrl) {
        if (empty($portfolioDetails)) {
            return 'No portfolios uploaded';
        }
        
        $portfolios = array_filter(array_map(function($entry) {
            $parts = explode('|', $entry);
            return [
                'path' => trim($parts[0]),
                'date' => isset($parts[1]) ? trim($parts[1]) : null
            ];
        }, explode(',', $portfolioDetails)), function($portfolio) {
            return !empty($portfolio['path']);
        });
        
        usort($portfolios, function($a, $b) {
            if (!empty($a['date']) && !empty($b['date'])) {
                return strtotime($b['date']) - strtotime($a['date']);
            }
            return 0;
        });
        
        $html = '<div class="portfolio-links">';
        foreach ($portfolios as $index => $portfolio) {
            $uploadDate = !empty($portfolio['date']) ? 
                '<span class="text-muted ms-2">(' . date('Y-m-d', strtotime($portfolio['date'])) . ')</span>' : '';
            
            $fullPath = rtrim($uploadsUrl, '/') . '/' . ltrim($portfolio['path'], '/');
            
            $html .= '
                <div class="d-flex align-items-center mb-2">
                    <span class="me-3">Portfolio ' . ($index + 1) . ' ' . $uploadDate . '</span>
                    <a href="' . htmlspecialchars($fullPath) . '" download class="btn btn-sm btn-outline-discovery">Download</a>
                </div>';
        }
        $html .= '</div>';
        
        return $html;
    }
    
    $portfolioDetails = $learner->portfolio_details ?? '';
    ?>
    
    <!-- Tab 4: Progressions / Portfolio of Evidence -->
    <div class="container-fluid gtab tab-4 border-top border-bottom mb-2 lh-1">
        <!-- Start Accordions -->
        <div class="accordion accordion-flush ml-0 mr-2" style="margin:0 -12px" id="accordionFlush">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed btn btn-light border-start border-end" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">ML3 | Bidvest | Class #1313 | 2023/09/26</button>
                </h2>
                <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlush">
                    <div class="accordion-body">
                        <!-- Accordion Content -->
                        <div class="container-fluid">
                            <div class="row border-start border-top">
                                <div class="col border-end p-2 bg-light d-flex align-items-center">Level Started</div>
                                <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
                                <div class="col border-end p-2 bg-light d-flex align-items-center">Level Completed</div>
                                <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
                                <div class="col border-end p-2 bg-light d-flex align-items-center">Result</div>
                                <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
                                <div class="col border-end px-2 py-1 align-items-center">
                                    <div class="d-grid">
                                        <button class="btn btn-sm btn-discovery" type="button">View POE</button>
                                    </div>
                                </div>
                                <div class="col border-end px-2 py-1 align-items-center">
                                    <div class="d-grid">
                                        <button class="btn btn-sm btn-outline-discovery" type="button">View SOR</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Portfolio Section -->
        <div class="row border-start border-top">
            <div class="col border-end p-2 bg-light d-flex align-items-center">
                <button class="btn btn-sm btn-outline-discovery" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasPortfolio" aria-controls="offcanvasPortfolio">Scanned Portfolio</button>
            </div>
            <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 bg-light d-flex align-items-center">&nbsp;</div>
            <div class="col border-end p-2 d-flex align-items-center">&nbsp;</div>
        </div>
    </div>
    
    <!-- Offcanvas Components -->
    
    <!-- History Offcanvas -->
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasHistory" aria-labelledby="offcanvasHistoryLabel">
        <div class="container">
            <div class="offcanvas-header pb-2 pt-2 border-bottom">
                <h5 class="offcanvas-title" id="offcanvasHistoryLabel">Learner History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body px-5 py-7">
                <div class="row px-3">
                    <div class="col">
                        <p class="border-bottom pb-2 mb-2"><span class="ydcoza-strong">Class:</span> ENG 01, <span class="ydcoza-strong">Client:</span> TechCorp B1, <span class="ydcoza-strong">Facilitator:</span> Sarah Lee <span class="ydcoza-strong">Class ID:</span> <a href="#" target="_blank">#482930</a></p>
                        <p class="border-bottom pb-2 mb-2"><span class="ydcoza-strong">Class:</span> SCI 03, <span class="ydcoza-strong">Client:</span> MedLife C3, <span class="ydcoza-strong">Facilitator:</span> John Smith <span class="ydcoza-strong">Class ID:</span> <a href="#" target="_blank">#573481</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Portfolio Offcanvas -->
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasPortfolio" aria-labelledby="offcanvasPortfolioLabel">
        <div class="container">
            <div class="offcanvas-header pb-2 pt-2 border-bottom">
                <h5 class="offcanvas-title" id="offcanvasPortfolioLabel">Scanned Portfolio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="container">
                    <div class="row px-3">
                        <?php echo formatPortfolioLinks($portfolioDetails, $uploadsUrl); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    </div>
</div>