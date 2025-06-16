/**
 * Recipe Scripts
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        // Initialize unit switcher
        initUnitSwitcher();
        
        // Initialize serving multiplier
        initServingMultiplier();
        
        // Initialize print button
        initPrintButton();
        
        // Initialize pin button
        initPinButton();
        
        // Initialize collection button
        initCollectionButton();
    });
    
    /**
     * Initialize unit switcher (US/Metric)
     */
    function initUnitSwitcher() {
        const $unitButtons = $('.pcs-unit-button');
        const $ingredientAmounts = $('.pcs-ingredient-amount');
        
        if (!$unitButtons.length) {
            return;
        }
        
        $unitButtons.on('click', function(e) {
            e.preventDefault();
            
            const unit = $(this).data('unit');
            
            // Toggle active class
            $unitButtons.removeClass('active');
            $(this).addClass('active');
            
            // Update ingredient amounts based on unit
            $ingredientAmounts.each(function() {
                const $amount = $(this);
                const usValue = $amount.data('us');
                const metricValue = $amount.data('metric');
                
                if (unit === 'us' && usValue !== undefined) {
                    updateIngredientValue($amount, usValue, true);
                } else if (unit === 'metric' && metricValue !== undefined) {
                    updateIngredientValue($amount, metricValue, false);
                }
            });
        });
        
        // Helper function to update ingredient values with proper unit conversions
        function updateIngredientValue($element, value, isUS) {
            let displayText = value;
            
            // Check if it's a number with a unit
            if (typeof value === 'number' || !isNaN(parseFloat(value))) {
                const numValue = parseFloat(value);
                
                // Handle common unit conversions and formatting
                if (isUS) {
                    // Convert from metric to US display
                    if (numValue >= 1000) {
                        displayText = (numValue / 1000).toFixed(1) + ' lb';
                    } else if (numValue > 30) {
                        displayText = numValue + ' oz';
                    } else {
                        displayText = numValue;
                    }
                } else {
                    // Format metric values
                    if (numValue >= 1000) {
                        displayText = (numValue / 1000).toFixed(1) + ' kg';
                    } else {
                        displayText = numValue + ' g';
                    }
                }
            }
            
            $element.text(displayText);
        }
    }
    
    /**
     * Initialize serving multiplier (1x, 2x, 3x)
     */
    function initServingMultiplier() {
        const $servingButtons = $('.pcs-serving-button');
        const $ingredientAmounts = $('.pcs-ingredient-amount');
        
        if (!$servingButtons.length) {
            return;
        }
        
        // Store original values
        let originalAmounts = [];
        
        $ingredientAmounts.each(function(index) {
            const $amount = $(this);
            const currentUnit = $('.pcs-unit-button.active').data('unit') || 'us';
            originalAmounts[index] = {
                element: $amount,
                us: $amount.data('us'),
                metric: $amount.data('metric'),
                currentUnit: currentUnit
            };
        });
        
        $servingButtons.on('click', function(e) {
            e.preventDefault();
            
            const multiplier = parseInt($(this).data('serving'), 10);
            const currentUnit = $('.pcs-unit-button.active').data('unit') || 'us';
            
            // Toggle active class
            $servingButtons.removeClass('active');
            $(this).addClass('active');
            
            // Update ingredient amounts based on serving multiplier
            for (let i = 0; i < originalAmounts.length; i++) {
                const item = originalAmounts[i];
                const value = (currentUnit === 'us') ? item.us : item.metric;
                
                if (value !== undefined) {
                    // Check if it's a number that can be multiplied
                    if (typeof value === 'number' || !isNaN(parseFloat(value))) {
                        const baseValue = parseFloat(value);
                        const newValue = baseValue * multiplier;
                        
                        // Update the display with the new calculated value
                        let displayText = formatServingAmount(newValue, currentUnit);
                        item.element.text(displayText);
                    }
                }
            }
        });
        
        // Helper function to format amounts with proper units
        function formatServingAmount(value, unit) {
            if (unit === 'us') {
                // Format for US units
                if (value >= 16) {
                    // Convert to pounds if over 16 oz
                    return (value / 16).toFixed(1) + ' lb';
                } else {
                    // Keep as ounces or other original unit
                    return value.toFixed(1).replace(/\.0$/, '') + ' oz';
                }
            } else {
                // Format for metric units
                if (value >= 1000) {
                    return (value / 1000).toFixed(1) + ' kg';
                } else {
                    return value.toFixed(0) + ' g';
                }
            }
        }
    }
    
    /**
     * Initialize print button
     */
    function initPrintButton() {
        $('.pcs-recipe-print-button').on('click', function(e) {
            e.preventDefault();
            window.print();
        });
    }
    
    /**
     * Initialize pin button for Pinterest sharing
     */
    function initPinButton() {
        $('.pcs-recipe-pin-button').on('click', function(e) {
            e.preventDefault();
            
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent($('.pcs-recipe-title').text());
            let media = '';
            
            // Try to get the featured image
            const $image = $('.pcs-recipe-featured-image img');
            if ($image.length) {
                media = encodeURIComponent($image.attr('src'));
            }
            
            const pinUrl = `https://pinterest.com/pin/create/button/?url=${url}&description=${title}&media=${media}`;
            window.open(pinUrl, 'pin', 'width=600,height=600');
        });
    }
    
    /**
     * Initialize collection button (save recipe)
     */
    function initCollectionButton() {
        $('.pcs-recipe-collection-button').on('click', function(e) {
            e.preventDefault();
            
            // Get recipe ID
            const postId = $(this).closest('.pcs-recipe').attr('id').replace('post-', '');
            
            // Simple client-side storage solution
            let savedRecipes = localStorage.getItem('pcs_saved_recipes');
            savedRecipes = savedRecipes ? JSON.parse(savedRecipes) : [];
            
            // Check if already saved
            if (savedRecipes.includes(postId)) {
                alert('Recipe is already in your collection');
            } else {
                savedRecipes.push(postId);
                localStorage.setItem('pcs_saved_recipes', JSON.stringify(savedRecipes));
                alert('Recipe added to your collection');
            }
        });
    }
    
})(jQuery);

// Unit conversion constants
const UNIT_CONVERSIONS = {
    'cup': { metric: 'ml', factor: 236.588 },
    'cups': { metric: 'ml', factor: 236.588 },
    'tablespoon': { metric: 'ml', factor: 14.7868 },
    'tablespoons': { metric: 'ml', factor: 14.7868 },
    'tbsp': { metric: 'ml', factor: 14.7868 },
    'teaspoon': { metric: 'ml', factor: 4.92892 },
    'teaspoons': { metric: 'ml', factor: 4.92892 },
    'tsp': { metric: 'ml', factor: 4.92892 },
    'pound': { metric: 'g', factor: 453.592 },
    'pounds': { metric: 'g', factor: 453.592 },
    'lb': { metric: 'g', factor: 453.592 },
    'lbs': { metric: 'g', factor: 453.592 },
    'ounce': { metric: 'g', factor: 28.3495 },
    'ounces': { metric: 'g', factor: 28.3495 },
    'oz': { metric: 'g', factor: 28.3495 },
    'inch': { metric: 'cm', factor: 2.54 },
    'inches': { metric: 'cm', factor: 2.54 },
    'in': { metric: 'cm', factor: 2.54 },
    'fahrenheit': { metric: '°C', factor: null }, // Special handling for temperature
    'f': { metric: '°C', factor: null }
};

// Temperature conversion functions
function fahrenheitToCelsius(f) {
    return Math.round((f - 32) * 5/9);
}

function celsiusToFahrenheit(c) {
    return Math.round((c * 9/5) + 32);
}

// Convert units in text
function convertUnits(text, toMetric) {
    let converted = text;
    
    // Regular expression to match numbers and units
    const regex = /(\d+(?:\.\d+)?)\s*(cup|cups|tablespoon|tablespoons|tbsp|teaspoon|teaspoons|tsp|pound|pounds|lb|lbs|ounce|ounces|oz|inch|inches|in|fahrenheit|f)s?\b/gi;
    
    converted = converted.replace(regex, function(match, number, unit) {
        const unitLower = unit.toLowerCase();
        const conversion = UNIT_CONVERSIONS[unitLower];
        
        if (!conversion) return match;
        
        if (unitLower === 'fahrenheit' || unitLower === 'f') {
            const celsius = fahrenheitToCelsius(parseFloat(number));
            return `${celsius}°C`;
        }
        
        const converted = toMetric ? 
            parseFloat(number) * conversion.factor :
            parseFloat(number) / conversion.factor;
            
        const roundedValue = Math.round(converted * 10) / 10;
        return `${roundedValue} ${toMetric ? conversion.metric : unit}`;
    });
    
    return converted;
}

// Handle unit toggle button
$('.pcs-recipe-unit-toggle').on('click', function() {
    const $button = $(this);
    const currentUnit = $button.data('current-unit');
    const toMetric = currentUnit === 'us';
    
    // Update button text
    $button.text(toMetric ? 'Switch to US Units' : 'Switch to Metric');
    $button.data('current-unit', toMetric ? 'metric' : 'us');
    
    // Convert ingredients
    $('.pcs-recipe-ingredients-content').each(function() {
        const $content = $(this);
        const originalText = $content.data('original-text') || $content.html();
        
        // Store original text on first conversion
        if (!$content.data('original-text')) {
            $content.data('original-text', originalText);
        }
        
        const convertedText = toMetric ? 
            convertUnits(originalText, true) :
            $content.data('original-text');
            
        $content.html(convertedText);
    });
});

// Handle serving size adjustment
$('.pcs-recipe-servings-adjust button').on('click', function() {
    const $button = $(this);
    const multiplier = parseFloat($button.data('multiply'));
    
    // Update active state
    $('.pcs-recipe-servings-adjust button').removeClass('active');
    $button.addClass('active');
    
    // Update ingredient amounts
    $('.pcs-recipe-ingredients-content').each(function() {
        const $content = $(this);
        const originalText = $content.data('original-text') || $content.html();
        
        // Store original text on first adjustment
        if (!$content.data('original-text')) {
            $content.data('original-text', originalText);
        }
        
        let adjustedText = originalText.replace(/(\d+(?:\.\d+)?)/g, function(match) {
            const number = parseFloat(match);
            const adjusted = number * multiplier;
            return Math.round(adjusted * 10) / 10;
        });
        
        // If metric is active, convert the adjusted text
        if ($('.pcs-recipe-unit-toggle').data('current-unit') === 'metric') {
            adjustedText = convertUnits(adjustedText, true);
        }
        
        $content.html(adjustedText);
    });
    
    // Update servings display
    const $servings = $('.pcs-recipe-servings-value');
    const originalServings = $servings.data('original-value') || $servings.text();
    
    // Store original servings on first adjustment
    if (!$servings.data('original-value')) {
        $servings.data('original-value', originalServings);
    }
    
    const adjustedServings = Math.round(parseFloat(originalServings) * multiplier);
    $servings.text(adjustedServings);
});

// Initialize print functionality
$('.pcs-recipe-print').on('click', function(e) {
    e.preventDefault();
    window.print();
});

// Initialize save recipe functionality
$('.pcs-recipe-save').on('click', function(e) {
    e.preventDefault();
    // TODO: Implement save recipe functionality
    alert('Save recipe feature coming soon!');
});
