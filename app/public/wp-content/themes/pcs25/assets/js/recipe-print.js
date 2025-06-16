/**
 * Recipe print functionality
 */
(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        // Add print button functionality
        $('.recipe-print-button').on('click', function(e) {
            e.preventDefault();
            window.print();
        });
    });
    
})(jQuery);
