/**
 * Recipe Filter JavaScript
 * 
 * Handles recipe filtering and Select2 initialization
 */
(function($) {
    'use strict';
    
    // Initialize Select2 for all filter dropdowns
    function initSelect2() {
        $('.pcs-select2').select2({
            placeholder: 'Select options',
            allowClear: true,
            closeOnSelect: false,
            width: '100%'
        });
    }
    
    // Handle form reset
    function handleFormReset() {
        $('#pcs-filter-reset').on('click', function(e) {
            e.preventDefault();
            
            // Clear all form fields
            $('#pcs-recipe-filter-form')[0].reset();
            
            // Reset Select2 dropdowns
            $('.pcs-select2').val(null).trigger('change');
            
            // Submit the form
            setTimeout(function() {
                $('#pcs-recipe-filter-form').submit();
            }, 100);
        });
    }
    
    // Handle form submission with animation
    function handleFormSubmit() {
        $('#pcs-recipe-filter-form').on('submit', function() {
            $('.pcs-recipe-grid').fadeOut(200);
            return true;
        });
    }
    
    // Initialize functionality when document is ready
    $(document).ready(function() {
        initSelect2();
        handleFormReset();
        handleFormSubmit();
        
        // Fade in grid on page load
        $('.pcs-recipe-grid').hide().fadeIn(500);
    });
    
})(jQuery);
