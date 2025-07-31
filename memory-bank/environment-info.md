# Environment Information

## Local Development Environment
- **Platform**: macOS
- **Development Tool**: Local by Flywheel (LocalWP)
- **Site Name**: pcsnew
- **Local URL**: http://pcsnew.local
- **SSL URL**: https://pcsnew.local (available via Local)
- **Site Path**: `/Users/ryanhorwath/Local Sites/pcsnew`
- **WordPress Path**: `/Users/ryanhorwath/Local Sites/pcsnew/app/public`
- **WordPress Version**: Latest
- **PHP Version**: 8.2.23
- **Database**: MySQL (managed by Local)
- **Web Server**: Nginx (managed by Local)

## Local by Flywheel Features
- **Automatic Services**: MySQL, Nginx, PHP managed automatically
- **Database Access**: Via Local's Adminer or phpMyAdmin tools
- **SSL Support**: Built-in SSL certificates for HTTPS testing
- **File Access**: Direct file system access to WordPress files
- **Logs**: Available through Local app interface
- **One-Click WordPress**: Easy WordPress installation and management

## Theme Information
- **Active Theme**: Twenty Twenty-Five (parent)
- **Child Theme**: pcs-twentytwentyfive-child
- **Theme Path**: app/public/wp-content/themes/pcs-twentytwentyfive-child/

## Custom Plugins
- pcs-enhanced-recipes (enhanced recipe functionality)
- pcs-recipe-importer (recipe import tools)
- pcs-universal-importer (universal content importer)
- pcs-blog-importer (Shopify blog post importer)

## Plugin Development
- Working on enhancing `pcs-enhanced-recipes` WordPress plugin
- Implementing features to approach parity with WP Recipe Maker plugin
- Completed implementation of Nutrition Facts feature (June 2025)
- Recently fixed JSON file handling in blog importer plugin

## Database Configuration
- **Database Name**: pcsnew
- **Host**: localhost (Local managed)
- **Port**: Local managed port (typically 10004 or similar)
- **Charset**: utf8mb4
- **Access**: Via Local's database tools or direct connection

## Development Workflow
1. **Local Development**: Make changes in Local by Flywheel environment
2. **Local Testing**: Test at http://pcsnew.local using Local's services
3. **Version Control**: Commit changes to Git repository
4. **GitHub**: Push to https://github.com/paccloud/pcsnew.git
5. **Deployment**: Auto-deploy to Hostinger via GitHub Actions

## Testing Guidelines
- Use the Local by Flywheel environment for all local testing
- Access local site through Local's provided URLs (http://pcsnew.local)
- Use Local's database tools for database operations
- Test both HTTP and HTTPS versions when needed
- Verify functionality before committing to Git
