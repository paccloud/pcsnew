# Hostinger Configuration Guide

## Required Hostinger Settings

### 1. FTP/SFTP Credentials
You'll need to set up these secrets in your GitHub repository:

1. Go to your GitHub repository: `https://github.com/paccloud/pcsnew`
2. Click on "Settings" tab
3. Go to "Secrets and variables" → "Actions"
4. Add the following secrets:

```
FTP_SERVER: your-hostinger-ftp-server.com
FTP_USERNAME: your-ftp-username
FTP_PASSWORD: your-ftp-password
WEBSITE_URL: https://yourdomain.com
```

### 2. Hostinger File Structure
Typical Hostinger WordPress installation structure:
```
/public_html/
├── wp-admin/
├── wp-content/
│   ├── themes/
│   │   └── pcs-twentytwentyfive-child/
│   ├── plugins/
│   │   ├── pcs-blog-importer/
│   │   ├── pcs-enhanced-recipes/
│   │   ├── pcs-recipe-importer/
│   │   └── pcs-universal-importer/
│   └── uploads/
├── wp-includes/
└── wp-config.php
```

### 3. Database Configuration
Update your Hostinger wp-config.php with:

```php
// Hostinger database settings
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASSWORD', 'your_database_password');
define('DB_HOST', 'localhost');

// Security keys (generate new ones for production)
define('AUTH_KEY',         'your-unique-auth-key');
define('SECURE_AUTH_KEY',  'your-unique-secure-auth-key');
define('LOGGED_IN_KEY',    'your-unique-logged-in-key');
define('NONCE_KEY',        'your-unique-nonce-key');
define('AUTH_SALT',        'your-unique-auth-salt');
define('SECURE_AUTH_SALT', 'your-unique-secure-auth-salt');
define('LOGGED_IN_SALT',   'your-unique-logged-in-salt');
define('NONCE_SALT',       'your-unique-nonce-salt');

// Production settings
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);
define('DISALLOW_FILE_EDIT', true);
define('AUTOMATIC_UPDATER_DISABLED', true);

// SSL settings
define('FORCE_SSL_ADMIN', true);
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
```

### 4. File Permissions
Set correct permissions on Hostinger:
```bash
# Directories should be 755
find /public_html/ -type d -exec chmod 755 {} \;

# Files should be 644
find /public_html/ -type f -exec chmod 644 {} \;

# wp-config.php should be 600
chmod 600 /public_html/wp-config.php
```

### 5. .htaccess Configuration
Create/update .htaccess in /public_html/:

```apache
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress

# Security headers
<IfModule mod_headers.c>
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
</IfModule>

# Disable directory browsing
Options -Indexes

# Protect wp-config.php
<files wp-config.php>
order allow,deny
deny from all
</files>
```

## Deployment Checklist

### Before First Deployment
- [ ] Set up Hostinger hosting account
- [ ] Install WordPress on Hostinger
- [ ] Get FTP/SFTP credentials
- [ ] Set up GitHub repository secrets
- [ ] Backup existing Hostinger site
- [ ] Test FTP connection

### For Each Deployment
- [ ] Test changes locally
- [ ] Commit and push to GitHub
- [ ] Monitor GitHub Actions deployment
- [ ] Verify website functionality
- [ ] Test custom plugins
- [ ] Check blog importer
- [ ] Verify recipe functionality
- [ ] Test mobile responsiveness

### Post-Deployment
- [ ] Clear any caching plugins
- [ ] Update WordPress if needed
- [ ] Check for plugin updates
- [ ] Verify SSL certificate
- [ ] Test contact forms
- [ ] Check Google Analytics/tracking

## Troubleshooting

### Common Issues

1. **FTP Connection Failed**
   - Verify FTP credentials in GitHub secrets
   - Check Hostinger FTP server address
   - Ensure FTP is enabled in Hostinger panel

2. **File Permission Errors**
   - Set correct permissions (755 for directories, 644 for files)
   - Check ownership settings

3. **Plugin Not Working**
   - Verify all plugin files were uploaded
   - Check PHP error logs in Hostinger
   - Ensure WordPress version compatibility

4. **Theme Issues**
   - Verify child theme files uploaded correctly
   - Check parent theme is available
   - Clear any caching

5. **Database Issues**
   - Update wp-config.php with correct credentials
   - Import database if needed
   - Update site URLs in database

### Hostinger-Specific Notes

- Hostinger uses cPanel for file management
- PHP version can be changed in Hostinger panel
- Database access via phpMyAdmin
- SSL certificates are usually auto-installed
- Caching may be enabled by default

## Manual Deployment Steps

If automated deployment fails, use manual process:

1. **Download from GitHub**
   ```bash
   git clone https://github.com/paccloud/pcsnew.git
   cd pcsnew
   ```

2. **Prepare files**
   ```bash
   # Create deployment package
   mkdir deploy
   cp -r app/public/wp-content/themes/pcs-twentytwentyfive-child deploy/
   cp -r app/public/wp-content/plugins/pcs-* deploy/
   ```

3. **Upload via FTP**
   - Use FileZilla or similar FTP client
   - Upload to /public_html/wp-content/
   - Maintain directory structure

4. **Activate in WordPress**
   - Login to WordPress admin
   - Activate child theme
   - Activate custom plugins
   - Test functionality

## Backup Strategy

### Automated Backups
- Set up Hostinger automatic backups
- Use WordPress backup plugins
- Regular database exports

### Manual Backups
- Before each deployment
- Before major WordPress updates
- Monthly full site backups

## Security Recommendations

- Use strong passwords
- Enable 2FA where possible
- Keep WordPress and plugins updated
- Regular security scans
- Monitor error logs
- Use security plugins
