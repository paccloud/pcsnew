# Project Brief

## WordPress Recipe Plugin Integration

### Requirements:
1. Create custom post type for recipes
2. Implement recipe details fields:
   - Prep time, cook time, total time
   - Servings
   - Ingredients with unit conversion
   - Instructions
   - Difficulty level
   - Course and cuisine types
3. Create theme-compatible templates:
   - Support both classic and block themes
   - Proper header/footer integration
   - Responsive layout
   - Modern design elements
4. Enhanced functionality:
   - US/Metric unit conversion
   - Serving size adjustment
   - Print and share features
   - Advanced search and filtering

### Constraints:
- Must work with WordPress 6.0+
- Must be compatible with block themes (Twenty Twenty-Five)
- Should be responsive and accessible
- Must maintain consistent layout across theme types
- Must handle template parts properly in block themes

### Technical Requirements:
1. Template System:
   - Unified handling for classic/block themes
   - Proper template hierarchy respect
   - Block theme template part integration
   - Output buffering for content capture
2. Asset Management:
   - Conditional script/style loading
   - Theme-specific data localization
3. Performance:
   - Optimized template loading
   - Minimal DOM manipulation
   - Efficient block theme integration

### Current Status:
- Recipe post type and fields implemented 
- Block theme integration complete 
- Template system unified 
- Unit conversion system working 
- Header/footer display fixed 
- Enhanced layout restored 

### Next Steps:
1. User testing across different theme types
2. Performance optimization
3. Documentation updates
4. Additional feature enhancements
