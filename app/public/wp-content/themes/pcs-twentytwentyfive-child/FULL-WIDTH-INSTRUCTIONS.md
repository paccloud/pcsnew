# Full-Width Header and Footer Instructions - Pacific Cloud Seafoods

## 🚀 **QUICK FIX (Recommended)**

### Automated Full-Width Fix
1. Go to WordPress Admin: `http://pcsnew.local/wp-admin/`
2. Navigate to **Tools → PCS Site Fixer**
3. Scroll down to **"Fix Full-Width Header & Footer"** section
4. Click **"Fix Full-Width Layout"** button
5. Confirm the action when prompted
6. Check your site - header and footer should now stretch full width!

## ✅ **Changes Made Successfully**

### 1. Updated CSS Styles
- **File**: `style.css` (updated to v1.5)
- **Purpose**: Added CSS rules to force header and footer to stretch full screen width
- **Key Changes**: 
  - Uses `100vw` width and viewport calculations
  - Overrides constrained layout restrictions
  - Adds proper padding for content

### 2. Updated Header Template Part
- **File**: `/parts/header.html`
- **Purpose**: Modified to use full-width layout instead of constrained
- **Changes**: 
  - Removed nested constrained layout containers
  - Added `alignfull` classes for full-width spanning
  - Added proper padding for content spacing

### 3. Updated Footer Template Part
- **File**: `/parts/footer.html`
- **Purpose**: Modified to use full-width layout instead of constrained
- **Changes**: 
  - Removed nested constrained layout containers
  - Added `alignfull` classes for full-width spanning
  - Added proper padding for content spacing

### 4. Cache Clearing System
- **File**: `fix-full-width.php` (new)
- **Purpose**: Clears theme caches and forces refresh
- **Functions**: Clears theme mods, transients, and template caches

## 🔧 **Technical Details**

### CSS Solution Explanation
The full-width solution uses several CSS techniques:

1. **Viewport Width**: Uses `100vw` to span the entire viewport width
2. **Negative Margins**: Uses `calc(-50vw + 50%)` to break out of container constraints
3. **Override Constraints**: Uses `!important` to override theme's constrained layout system
4. **Proper Padding**: Maintains content spacing with theme's spacing variables

### Template Structure Changes
- **Before**: Header/Footer → Constrained Group → Wide Group → Content
- **After**: Header/Footer → Full Group → Content (with padding)

### WordPress Block Layout System
WordPress block themes use a layout system with:
- **contentSize**: 645px (normal content width)
- **wideSize**: 1340px (wide content width)
- **alignfull**: Full viewport width (what we want)

## 🎯 **Before vs. After**

### Before (Constrained)
- Header and footer limited to 1340px maximum width
- Content centered with margins on wide screens
- Visible gaps on sides of header/footer

### After (Full-Width)
- Header and footer span entire screen width
- Content properly padded but background extends full width
- No gaps on sides, professional full-width appearance

## 📋 **Verification Steps**

1. **Visual Check**: 
   - Open site in browser
   - Header should span full width with no side gaps
   - Footer should span full width with no side gaps

2. **Responsive Check**:
   - Test on different screen sizes
   - Header/footer should always span full width
   - Content should remain properly spaced

3. **Developer Tools Check**:
   - Inspect header/footer elements
   - Should see `alignfull` classes
   - Should see full-width CSS applied

## 🚨 **Important Notes**

### WordPress Block Theme Considerations
- Block themes use JSON-based layout systems
- Template parts must use proper alignment classes
- CSS overrides may be needed for complex layouts

### Cache Clearing
- WordPress caches template parts and theme data
- Always clear caches after template changes
- Use the automated fix tool for proper cache clearing

### Responsive Design
- Full-width layouts work on all screen sizes
- Content padding ensures readability on mobile
- Uses theme's responsive spacing variables

## 📊 **Current Layout Settings**

### Theme Layout Configuration
- **Content Size**: 645px (normal content)
- **Wide Size**: 1340px (wide content)
- **Full Size**: 100vw (full viewport - what we use)

### Spacing Variables Used
- `var(--wp--preset--spacing--50)` for content padding
- `var(--wp--preset--spacing--30)` for header/footer internal spacing
- `var(--wp--preset--spacing--60)` for footer top padding

## 🔄 **Rollback Instructions**

If you need to revert to constrained layout:

1. **Restore Original Templates**:
   - Copy templates from parent theme
   - Remove custom CSS from style.css

2. **Clear Caches**:
   - Use PCS Site Fixer tool
   - Or manually clear theme caches

3. **Alternative**: Use Site Editor to manually adjust template parts

## 🛠 **Manual Implementation**

If you prefer manual implementation:

1. **Update CSS**: Add full-width styles to style.css
2. **Update Templates**: Modify header.html and footer.html
3. **Clear Caches**: Use fix-full-width.php or admin tools
4. **Test**: Verify full-width appearance on all devices

The automated fix handles all these steps automatically and safely.
