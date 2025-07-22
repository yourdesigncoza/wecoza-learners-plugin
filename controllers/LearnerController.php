<?php
/**
 * LearnerController.php
 * 
 * Controller for handling learner-related operations
 */

namespace WeCoza\Controllers;

class LearnerController {
    /**
     * Constructor
     */
    public function __construct() {
        // Register WordPress hooks
        add_action('init', [$this, 'registerShortcodes']);
    }

    /**
     * Register all learner-related shortcodes
     */
    public function registerShortcodes() {
        add_shortcode('wecoza_learner_capture', [$this, 'captureLearner']);
        add_shortcode('wecoza_learner_display', [$this, 'displayLearner']);
        add_shortcode('wecoza_learner_update', [$this, 'updateLearner']);
    }

    /**
     * Handle learner capture shortcode
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function captureLearner($atts) {
        // Implementation will be added later
        return 'Learner capture form will be displayed here';
    }

    /**
     * Handle learner display shortcode
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function displayLearner($atts) {
        // Implementation will be added later
        return 'Learner information will be displayed here';
    }

    /**
     * Handle learner update shortcode
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function updateLearner($atts) {
        // Implementation will be added later
        return 'Learner update form will be displayed here';
    }
}
