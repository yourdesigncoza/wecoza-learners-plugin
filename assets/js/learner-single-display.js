jQuery(document).ready(function($) {
    const learnerSingle = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Back to learners button
            $('.back-to-learners').on('click', this.handleBackToLearners);
            
            // Edit learner button
            $('.edit-learner-btn').on('click', this.handleEditLearner);
            
            // Delete learner button
            $('.delete-learner-btn').on('click', this.handleDeleteLearner.bind(this));
        },

        handleBackToLearners: function() {
            window.location.href = learnerSingleAjax.homeUrl + '/display-learners/';
        },

        handleEditLearner: function() {
            const learnerId = $(this).data('id');
            window.location.href = learnerSingleAjax.homeUrl + '/update-learners/?learner_id=' + learnerId;
        },

        handleDeleteLearner: function(e) {
            e.preventDefault();
            const learnerId = $(e.currentTarget).data('id');
            
            if (confirm('Are you sure you want to delete this learner? This action cannot be undone.')) {
                $.ajax({
                    url: learnerSingleAjax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'delete_learner',
                        id: learnerId,
                        nonce: learnerSingleAjax.nonce
                    },
                    beforeSend: function() {
                        $(e.currentTarget).prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Deleting...');
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            learnerSingle.showAlert('success', 'Learner deleted successfully. Redirecting...');
                            
                            // Redirect to learners list after 2 seconds
                            setTimeout(function() {
                                window.location.href = learnerSingleAjax.homeUrl + '/display-learners/';
                            }, 2000);
                        } else {
                            learnerSingle.showAlert('error', response.data.message || 'Failed to delete learner');
                            $(e.currentTarget).prop('disabled', false).html('<i class="bi bi-trash me-2"></i>Delete');
                        }
                    },
                    error: function() {
                        learnerSingle.showAlert('error', 'An error occurred while deleting the learner');
                        $(e.currentTarget).prop('disabled', false).html('<i class="bi bi-trash me-2"></i>Delete');
                    }
                });
            }
        },

        showAlert: function(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show mb-3" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Insert alert after the header buttons
            $('.wecoza-single-learner-display > .d-flex').after(alertHtml);
            
            // Auto-dismiss after 5 seconds if not success
            if (type !== 'success') {
                setTimeout(() => {
                    $('.alert').fadeOut();
                }, 5000);
            }
        }
    };

    // Initialize
    learnerSingle.init();
});