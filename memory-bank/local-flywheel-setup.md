# Local by Flywheel Setup - Pacific Cloud Seafood

## Overview
The Pacific Cloud Seafood WordPress site is developed using Local by Flywheel, a local WordPress development environment that provides an easy way to create and manage WordPress sites locally.

## Site Configuration

### Basic Information
- **Site Name**: pcsnew
- **Local URL**: http://pcsnew.local
- **SSL URL**: https://pcsnew.local (available via Local)
- **Site Path**: `/Users/ryanhorwath/Local Sites/pcsnew`
- **WordPress Path**: `/Users/ryanhorwath/Local Sites/pcsnew/app/public`

### Environment Details
- **PHP Version**: 8.2.23
- **Web Server**: Nginx (managed by Local)
- **Database**: MySQL (managed by Local)
- **WordPress Version**: Latest
- **Environment**: Local development

## Directory Structure
```
/Users/ryanhorwath/Local Sites/pcsnew/
├── app/
│   ├── public/                    # WordPress root
│   │   ├── wp-admin/
│   │   ├── wp-content/
│   │   │   ├── themes/
│   │   │   │   └── pcs-twentytwentyfive-child/
│   │   │   ├── plugins/
│   │   │   │   ├── pcs-blog-importer/
│   │   │   │   ├── pcs-enhanced-recipes/
│   │   │   │   ├── pcs-recipe-importer/
│   │   │   │   └── pcs-universal-importer/
│   │   │   └── uploads/
│   │   ├── wp-includes/
│   │   └── wp-config.php
│   └── sql/                       # Database files
├── conf/                          # Server configuration
│   ├── nginx/
│   ├── php/
│   └── mysql/
├── logs/                          # Server logs
│   ├── nginx/
│   ├── php/
│   └── mysql/
├── memory-bank/                   # Project documentation
├── .git/                          # Git repository
├── deploy.sh                      # Deployment script
└── DEPLOYMENT-WORKFLOW.md         # Deployment documentation
```

## Local by Flywheel Features Used

### 1. Automatic Service Management
- **MySQL**: Automatically started/stopped with site
- **Nginx**: Configured for WordPress with proper rewrites
- **PHP**: Version 8.2.23 with WordPress optimizations
- **SSL**: Built-in SSL certificates for HTTPS testing

### 2. Database Management
- **Access**: Via Local's Adminer tool
- **Backup**: Built-in export functionality
- **Import**: Easy SQL file import
- **Connection**: Direct connection details available

### 3. File Management
- **Direct Access**: Full file system access to WordPress files
- **Live Reload**: Changes reflected immediately
- **Permissions**: Proper file permissions maintained
- **Logs**: Easy access to error logs

### 4. Development Tools
- **WP-CLI**: Available if installed separately
- **Debugging**: Easy PHP error log access
- **SSL Testing**: Test HTTPS functionality locally
- **Mail Catcher**: Built-in email testing (if enabled)

## Development Workflow with Local

### Daily Development Process
1. **Start Local**: Open Local by Flywheel app
2. **Start Site**: Ensure "pcsnew" site is running (green indicator)
3. **Access Site**: Navigate to http://pcsnew.local
4. **Make Changes**: Edit theme/plugin files directly
5. **Test Changes**: Refresh browser to see changes
6. **Commit Changes**: Use Git for version control

### Testing Checklist
- [ ] Homepage loads correctly at http://pcsnew.local
- [ ] Admin dashboard accessible at http://pcsnew.local/wp-admin
- [ ] Custom theme displays properly
- [ ] Recipe functionality works
- [ ] Blog importer plugin functions
- [ ] All custom plugins active and working
- [ ] No PHP errors in Local's logs

### Database Operations
1. **Access Database**:
   - Open Local by Flywheel
   - Select "pcsnew" site
   - Click "Database" tab
   - Click "Open Adminer"

2. **Backup Database**:
   - In Adminer, click "Export"
   - Select "SQL" format
   - Download backup file

3. **Import Database**:
   - In Adminer, click "Import"
   - Select SQL file
   - Execute import

## Integration with Deployment Workflow

### Local to GitHub
1. **Test Locally**: Verify all functionality in Local environment
2. **Commit Changes**: Use Git to commit tested changes
3. **Push to GitHub**: Deploy to repository

### Local to Production
1. **Local Testing**: Complete testing in Local environment
2. **GitHub Staging**: Push to GitHub repository
3. **Automated Deployment**: GitHub Actions deploys to Hostinger
4. **Production Verification**: Test live site functionality

## Troubleshooting Local Issues

### Common Problems

1. **Site Won't Start**
   - Check Local app for error messages
   - Restart Local application
   - Check available disk space
   - Verify port conflicts

2. **Database Connection Issues**
   - Restart site in Local
   - Check database service status
   - Verify wp-config.php settings

3. **File Permission Issues**
   - Local usually handles permissions automatically
   - Check file ownership if issues persist
   - Restart Local if permissions seem wrong

4. **SSL Certificate Issues**
   - Regenerate SSL certificate in Local
   - Clear browser cache
   - Check Local's SSL settings

### Performance Optimization
- **PHP Memory**: Increase if needed via Local settings
- **Database**: Regular optimization via Adminer
- **File Cleanup**: Remove unnecessary files/plugins
- **Cache**: Clear any caching plugins during development

## Backup Strategy

### Local Backups
- **Database**: Regular exports via Adminer
- **Files**: Git repository serves as file backup
- **Complete Site**: Local's built-in backup features
- **Before Changes**: Always backup before major changes

### Backup Schedule
- **Daily**: Automatic Git commits for file changes
- **Weekly**: Database export backup
- **Before Deployment**: Complete site backup
- **Before Updates**: WordPress/plugin update backups

## Security Considerations

### Local Environment Security
- **Network Access**: Local sites not accessible from network by default
- **Database**: Protected by Local's security
- **File Access**: Standard macOS file permissions
- **SSL**: Valid certificates for HTTPS testing

### Development Best Practices
- **Separate Environment**: Keep local separate from production
- **Test Data**: Use test data, not production data
- **Plugin Testing**: Test plugins thoroughly before deployment
- **Version Control**: Always use Git for change tracking

## Integration with Other Tools

### Git Integration
- Repository root: `/Users/ryanhorwath/Local Sites/pcsnew`
- Excludes: Local-specific files via .gitignore
- Includes: WordPress theme, plugins, documentation

### Deployment Integration
- Local serves as development environment
- Changes tested locally before Git commit
- Automated deployment from GitHub to Hostinger
- Local environment mirrors production structure

### Development Tools
- **Code Editor**: Any editor can access Local files
- **Browser**: Standard web browser for testing
- **Database Tools**: Adminer via Local interface
- **Command Line**: Terminal access to Local files

## Next Steps for Local Development

1. **Optimize Local Setup**:
   - Configure PHP settings for development
   - Set up debugging tools
   - Install WP-CLI if needed

2. **Enhance Workflow**:
   - Set up automated testing
   - Configure code linting
   - Add development plugins

3. **Backup Automation**:
   - Automate database backups
   - Set up file sync if needed
   - Configure backup verification

4. **Performance Monitoring**:
   - Monitor Local performance
   - Optimize database queries
   - Profile plugin performance
