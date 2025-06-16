# System Patterns

## Template System
### Block Theme Integration
- Check for required block theme functions before use
- Graceful fallback to classic template when needed
- Verify template parts exist before rendering
- Handle missing template parts gracefully
- Maintain consistent layout across theme types

### Classic Theme Integration
- Serve as fallback when block theme support is missing
- Maintain full functionality in classic themes
- Consistent styling across theme types
- Proper header/footer integration

### Error Handling
- Function existence checks before usage
- Template part validation
- Graceful fallbacks for missing components
- Clear error messaging
- Maintain user experience during failures

### Template Loading
- Priority-based template selection
- Theme type detection
- Template part integration
- Content buffering for clean output
- Exit handling to prevent duplicate content

### Content Rendering
- Consistent recipe layout
- Dynamic unit conversion
- Serving size adjustments
- Print optimization
- Mobile responsiveness

### Asset Management
- Conditional script loading
- Style dependencies
- Icon integration
- Print stylesheet handling
- Mobile-first approach

### Security
- Input validation
- Output escaping
- AJAX nonce verification
- Template file path validation
- Function availability checks

### Performance
- Output buffering
- Conditional loading
- Asset optimization
- Cache considerations
- Minimal DOM manipulation

### Accessibility
- Semantic markup
- ARIA attributes
- Keyboard navigation
- Screen reader support
- Color contrast compliance

## Code Organization
- Separate concerns between template and functionality
- Use class-based architecture
- Follow WordPress coding standards
- Implement proper error handling
- Use defensive coding practices

## Asset Management
- Load scripts and styles conditionally
- Enqueue assets properly
- Localize script data
- Handle dependencies correctly

## Feature Implementation
- Implement unit conversion system
- Add serving size adjustment
- Include print and share functionality
- Support advanced search and filtering
- Maintain responsive design

## Testing
- Test across different theme types
- Verify layout consistency
- Check functionality in various contexts
- Monitor performance
- Validate user experience

## Documentation
- Maintain memory bank files
- Update technical documentation
- Track progress and changes
- Document system patterns
- Keep project brief current
