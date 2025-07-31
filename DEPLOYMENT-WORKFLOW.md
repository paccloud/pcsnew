# Pacific Cloud Seafood - Deployment Workflow

## Overview
This document outlines the complete workflow for updating the Pacific Cloud Seafood website from local development to GitHub and then to the live Hostinger server.

## Current Setup
- **Local Development**: Local by Flywheel at `/Users/ryanhorwath/Local Sites/pcsnew`
- **Local URL**: http://pcsnew.local (managed by Local)
- **GitHub Repository**: `https://github.com/paccloud/pcsnew.git`
- **Live Server**: Hostinger hosting
- **WordPress Site**: Block theme with custom plugins and child theme
- **Development Environment**: Local by Flywheel handles all services (MySQL, Nginx, PHP)

## Workflow Steps

### 1. Local Development to GitHub

#### Prerequisites: Local by Flywheel Setup
Before starting deployment, ensure your Local by Flywheel environment is running:

1. **Start Local Services**:
   - Open Local by Flywheel application
   - Ensure the "pcsnew" site is running (green indicator)
   - Verify site is accessible at http://pcsnew.local

2. **Test Local Site**:
   - Check homepage loads correctly
   - Test custom plugins (blog importer, recipes)
   - Verify theme functionality
   - Test any recent changes

3. **Database Backup** (Optional but recommended):
   - In Local, go to Database tab
   - Click "Open Adminer" or use Local's backup tools
   - Export current database as backup

#### Step 1: Prepare Local Changes
```bash
# Navigate to your project directory
cd "/Users/ryanhorwath/Local Sites/pcsnew"

# Check current status
git status

# Add all changes (or specific files)
git add .

# Or add specific files only
git add app/public/wp-content/themes/pcs-twentytwentyfive-child/
git add app/public/wp-content/plugins/pcs-blog-importer/
git add memory-bank/

# Commit changes with descriptive message
git commit -m "feat: update blog importer plugin with JSON handling fixes"
```

#### Step 2: Push to GitHub
```bash
# Push to main branch
git push origin main

# Or if working on a feature branch
git checkout -b feature/blog-importer-fixes
git push origin feature/blog-importer-fixes
```

### 2. GitHub to Hostinger Deployment

#### Option A: Manual FTP/SFTP Deployment

**Prerequisites:**
- FTP/SFTP client (FileZilla, Cyberduck, or command line)
- Hostinger FTP credentials

**Steps:**
1. Download/clone from GitHub to a staging directory
2. Upload only the necessary WordPress files to Hostinger
3. Exclude development files and sensitive data

**Files to Upload:**
```
wp-content/themes/pcs-twentytwentyfive-child/
wp-content/plugins/pcs-blog-importer/
wp-content/plugins/pcs-enhanced-recipes/
wp-content/plugins/pcs-recipe-importer/
wp-content/plugins/pcs-universal-importer/
wp-content/uploads/ (media files)
```

**Files to Exclude:**
```
.git/
.gitignore
memory-bank/
logs/
conf/
Local Sites/
node_modules/
*.log
.DS_Store
```

#### Option B: Automated Deployment with GitHub Actions

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Hostinger

on:
  push:
    branches: [ main ]
  workflow_dispatch:

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v3
      
    - name: Setup Node.js
      uses: actions/setup-node@v3
      with:
        node-version: '18'
        
    - name: Deploy to Hostinger via FTP
      uses: SamKirkland/FTP-Deploy-Action@4.3.3
      with:
        server: ${{ secrets.FTP_SERVER }}
        username: ${{ secrets.FTP_USERNAME }}
        password: ${{ secrets.FTP_PASSWORD }}
        local-dir: ./app/public/
        server-dir: /public_html/
        exclude: |
          **/.git*
          **/.git*/**
          **/node_modules/**
          **/logs/**
          **/conf/**
          **/.DS_Store
          **/memory-bank/**
```

### 3. Database Synchronization

#### Export Local Database
```bash
# Using WP-CLI (if available)
wp db export backup-$(date +%Y%m%d).sql

# Or using mysqldump
mysqldump -u root -p pcsnew > backup-$(date +%Y%m%d).sql
```

#### Import to Hostinger
1. Access Hostinger's phpMyAdmin or database management tool
2. Import the SQL file
3. Update WordPress URLs using search/replace:

```sql
UPDATE wp_options SET option_value = 'https://yourdomain.com' WHERE option_name = 'home';
UPDATE wp_options SET option_value = 'https://yourdomain.com' WHERE option_name = 'siteurl';
```

### 4. Environment-Specific Configuration

#### Create Environment-Specific Files

**Local wp-config.php additions:**
```php
// Local development settings
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

**Production wp-config.php additions:**
```php
// Production settings
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('DISALLOW_FILE_EDIT', true);
define('AUTOMATIC_UPDATER_DISABLED', true);
```

### 5. Pre-Deployment Checklist

- [ ] Test all functionality locally
- [ ] Backup current live site
- [ ] Update version numbers in plugin headers
- [ ] Check for any hardcoded local URLs
- [ ] Verify all custom plugins are working
- [ ] Test blog importer functionality
- [ ] Ensure all media files are included
- [ ] Check theme customizations
- [ ] Verify recipe functionality
- [ ] Test contact forms and other interactive elements

### 6. Post-Deployment Verification

- [ ] Check homepage loads correctly
- [ ] Test navigation menu
- [ ] Verify recipe pages display properly
- [ ] Test blog importer plugin
- [ ] Check admin dashboard access
- [ ] Verify SSL certificate is working
- [ ] Test contact forms
- [ ] Check mobile responsiveness
- [ ] Verify search functionality
- [ ] Test plugin functionality

## Quick Commands Reference

### Git Commands
```bash
# Check status
git status

# Add and commit
git add .
git commit -m "your message"

# Push to GitHub
git push origin main

# Create and switch to new branch
git checkout -b feature/new-feature

# Merge branch
git checkout main
git merge feature/new-feature
```

### WordPress Commands (if WP-CLI available)
```bash
# Update WordPress core
wp core update

# Update plugins
wp plugin update --all

# Clear cache
wp cache flush

# Export database
wp db export backup.sql

# Search and replace URLs
wp search-replace 'http://pcsnew.local' 'https://yourdomain.com'
```

## Troubleshooting

### Common Issues

1. **Plugin Errors After Deployment**
   - Check file permissions (755 for directories, 644 for files)
   - Verify all plugin files were uploaded
   - Check PHP error logs

2. **Theme Not Displaying Correctly**
   - Ensure child theme files are uploaded
   - Check CSS file paths
   - Verify parent theme is available

3. **Database Connection Issues**
   - Update wp-config.php with correct database credentials
   - Check database server settings

4. **Missing Media Files**
   - Upload wp-content/uploads/ directory
   - Check file permissions
   - Verify media library in WordPress admin

## Security Considerations

- Never commit sensitive data (passwords, API keys) to GitHub
- Use environment variables for sensitive configuration
- Keep wp-config.php out of version control
- Regularly update WordPress core and plugins
- Use strong passwords and 2FA
- Regular security scans and backups

## Backup Strategy

- **Daily**: Automated database backups
- **Weekly**: Full site backups including files
- **Before Deployment**: Complete backup of live site
- **After Major Changes**: Immediate backup verification

## Next Steps

1. Set up automated deployment pipeline
2. Configure staging environment
3. Implement automated testing
4. Set up monitoring and alerts
5. Create rollback procedures
