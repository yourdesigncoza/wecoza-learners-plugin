jQuery(document).ready(function($) {
    const learnerTable = {
        init: function() {
            this.bindEvents();
            this.fetchData();
        },

        bindEvents: function() {
            $(document).on('click', '.view-learner', this.handleViewLearner);
        },

        fetchData: function() {
            $.ajax({
                url: wecozaAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'fetch_learners_data',
                    nonce: wecozaAjax.nonce
                },
                beforeSend: this.showLoader,
                success: this.handleSuccess.bind(this),
                error: this.handleError,
                complete: this.hideLoader
            });
        },

        showLoader: function() {
            $('#wecoza-loader-container').show();
            $('#learners-container').hide();
        },

        hideLoader: function() {
            $('#wecoza-loader-container').hide();
        },

        handleSuccess: function(response) {
            if (response.success) {
                $('#learners-display-data tbody').html(response.data);
                this.initializeTable();
                $('#learners-container').fadeIn();
            } else {
                this.showAlert('error', 'Failed to load data: ' + response.data);
            }
        },

        handleError: function(xhr, status, error) {
            const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred while loading data.';
            learnerTable.showAlert('error', errorMessage);
        },

        initializeTable: function() {
            const $table = $('#learners-display-data');
            
            if ($table.length) {
                if ($table.data('bootstrap.table')) {
                    $table.bootstrapTable('destroy');
                }

                $table.bootstrapTable({
                    pagination: true,
                    search: true,
                    pageSize: 25,
                    pageList: [10, 25, 50, 100],
                    sortable: true,
                    showRefresh: true,
                    // showToggle: true,
                    showColumns: true,
                    exportTypes: ['csv', 'excel', 'pdf'],
                    exportOptions: {
                        fileName: 'learners_data'
                    }
                });

                $('.table-bordered').addClass('borderless-table');
            }
        },

        handleViewLearner: function(e) {
            e.preventDefault();
            const learnerId = $(this).data('id');
            // Handle view learner logic
        },

        showAlert: function(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            $('#alert-container').html(alertHtml);
        }
    };

    // Initialize the learner table
    learnerTable.init();
});