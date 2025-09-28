/**
 * Saudi Address Admin JavaScript
 *
 * Handles admin functionality and API testing
 *
 * @package Saudi_Address_WooCommerce
 * @since 1.0.0
 */

(function($) {
    'use strict';

    var SaudiAddressAdmin = {
        
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
        },
        
        /**
         * Bind events
         */
        bindEvents: function() {
            $(document).on('click', '#test-api-connection', this.testApiConnection);
        },
        
        /**
         * Test API connection
         */
        testApiConnection: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $result = $('#api-test-result');
            var originalText = $button.text();
            
            // Disable button and show loading
            $button.prop('disabled', true).text('Testing...');
            $result.html('');
            
            $.ajax({
                url: saudi_address_admin_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'saudi_address_test_api',
                    nonce: saudi_address_admin_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $result.html(
                            '<div class="notice notice-success inline">' +
                            '<p><strong>' + response.data.message + '</strong></p>' +
                            '<p>Regions available: ' + response.data.regions_count + '</p>' +
                            '</div>'
                        );
                    } else {
                        $result.html(
                            '<div class="notice notice-error inline">' +
                            '<p><strong>Error:</strong> ' + response.data + '</p>' +
                            '</div>'
                        );
                    }
                },
                error: function() {
                    $result.html(
                        '<div class="notice notice-error inline">' +
                        '<p><strong>Error:</strong> Failed to connect to API</p>' +
                        '</div>'
                    );
                },
                complete: function() {
                    $button.prop('disabled', false).text(originalText);
                }
            });
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        SaudiAddressAdmin.init();
    });
    
})(jQuery);
