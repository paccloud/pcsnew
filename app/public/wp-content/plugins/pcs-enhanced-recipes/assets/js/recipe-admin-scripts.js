/**
 * Recipe Admin Scripts
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        // Initialize ingredient fields
        initIngredientFields();
        
        // Initialize instruction fields
        initInstructionFields();
    });
    
    /**
     * Initialize ingredient fields
     */
    function initIngredientFields() {
        const $ingredientContainer = $('#pcs-recipe-ingredients');
        
        if (!$ingredientContainer.length) {
            return;
        }
        
        const $addIngredientBtn = $('#pcs-add-ingredient');
        
        // Add ingredient
        $addIngredientBtn.on('click', function(e) {
            e.preventDefault();
            
            const index = $ingredientContainer.find('.pcs-recipe-ingredient-row').length;
            const $newRow = $('<div class="pcs-recipe-ingredient-row"></div>');
            
            $newRow.append('<input type="text" name="pcs_ingredients[' + index + '][amount]" placeholder="Amount" />');
            $newRow.append('<input type="text" name="pcs_ingredients[' + index + '][unit]" placeholder="Unit" />');
            $newRow.append('<input type="text" name="pcs_ingredients[' + index + '][name]" placeholder="Ingredient" style="width: 50%;" />');
            $newRow.append('<button type="button" class="button pcs-remove-ingredient">Remove</button>');
            
            $ingredientContainer.append($newRow);
        });
        
        // Remove ingredient
        $ingredientContainer.on('click', '.pcs-remove-ingredient', function() {
            $(this).closest('.pcs-recipe-ingredient-row').remove();
        });
    }
    
    /**
     * Initialize instruction fields
     */
    function initInstructionFields() {
        const $instructionContainer = $('#pcs-recipe-instructions');
        
        if (!$instructionContainer.length) {
            return;
        }
        
        const $addInstructionBtn = $('#pcs-add-instruction');
        
        // Add instruction
        $addInstructionBtn.on('click', function(e) {
            e.preventDefault();
            
            const index = $instructionContainer.find('.pcs-recipe-instruction-row').length;
            const $newRow = $('<div class="pcs-recipe-instruction-row"></div>');
            
            $newRow.append('<textarea name="pcs_instructions[' + index + ']" rows="3" style="width: 90%;"></textarea>');
            $newRow.append('<button type="button" class="button pcs-remove-instruction">Remove</button>');
            
            $instructionContainer.append($newRow);
        });
        
        // Remove instruction
        $instructionContainer.on('click', '.pcs-remove-instruction', function() {
            $(this).closest('.pcs-recipe-instruction-row').remove();
        });
    }
    
})(jQuery);
