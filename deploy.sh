#!/bin/bash

# Pacific Cloud Seafood - Deployment Script
# This script automates the deployment process from local to GitHub to Hostinger

set -e  # Exit on any error

# Configuration
PROJECT_DIR="/Users/ryanhorwath/Local Sites/pcsnew"
BACKUP_DIR="$PROJECT_DIR/backups"
DATE=$(date +%Y%m%d_%H%M%S)
GITHUB_REPO="https://github.com/paccloud/pcsnew.git"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory and Local environment
check_directory() {
    if [ ! -d "$PROJECT_DIR" ]; then
        log_error "Project directory not found: $PROJECT_DIR"
        exit 1
    fi

    cd "$PROJECT_DIR"

    if [ ! -d ".git" ]; then
        log_error "Not a git repository. Please initialize git first."
        exit 1
    fi

    # Check if this is a Local by Flywheel site
    if [ ! -d "app/public" ]; then
        log_error "This doesn't appear to be a Local by Flywheel site structure."
        log_error "Expected to find 'app/public' directory."
        exit 1
    fi

    # Check if WordPress is present
    if [ ! -f "app/public/wp-config.php" ]; then
        log_warning "wp-config.php not found. Make sure WordPress is properly installed in Local."
    fi

    log_info "Local by Flywheel site structure verified"
}

# Create backup directory
create_backup_dir() {
    if [ ! -d "$BACKUP_DIR" ]; then
        mkdir -p "$BACKUP_DIR"
        log_info "Created backup directory: $BACKUP_DIR"
    fi
}

# Backup current state
backup_current_state() {
    log_info "Creating backup of current state..."

    # Backup database if possible
    if command -v wp &> /dev/null; then
        log_info "Backing up database with WP-CLI..."
        wp db export "$BACKUP_DIR/database_backup_$DATE.sql" --path="$PROJECT_DIR/app/public"
        log_success "Database backup created: database_backup_$DATE.sql"
    else
        log_warning "WP-CLI not found. You can backup database manually via Local by Flywheel:"
        log_info "1. Open Local by Flywheel app"
        log_info "2. Select 'pcsnew' site"
        log_info "3. Go to Database tab"
        log_info "4. Click 'Open Adminer' and export database"
    fi
    
    # Backup important files
    log_info "Backing up important files..."
    tar -czf "$BACKUP_DIR/files_backup_$DATE.tar.gz" \
        app/public/wp-content/themes/pcs-twentytwentyfive-child/ \
        app/public/wp-content/plugins/pcs-blog-importer/ \
        app/public/wp-content/plugins/pcs-enhanced-recipes/ \
        app/public/wp-content/plugins/pcs-recipe-importer/ \
        app/public/wp-content/plugins/pcs-universal-importer/ \
        memory-bank/ 2>/dev/null || log_warning "Some files may not exist for backup"
    
    log_success "Files backup created: files_backup_$DATE.tar.gz"
}

# Check git status and commit changes
commit_changes() {
    log_info "Checking git status..."
    
    if [ -n "$(git status --porcelain)" ]; then
        log_info "Found uncommitted changes. Staging files..."
        
        # Show what will be committed
        echo "Files to be committed:"
        git status --short
        
        # Ask for confirmation
        read -p "Do you want to commit these changes? (y/N): " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            # Ask for commit message
            read -p "Enter commit message: " commit_message
            if [ -z "$commit_message" ]; then
                commit_message="Deploy: automated deployment on $DATE"
            fi
            
            git add .
            git commit -m "$commit_message"
            log_success "Changes committed successfully"
        else
            log_warning "Deployment cancelled by user"
            exit 0
        fi
    else
        log_info "No uncommitted changes found"
    fi
}

# Push to GitHub
push_to_github() {
    log_info "Pushing to GitHub..."
    
    # Get current branch
    current_branch=$(git branch --show-current)
    log_info "Current branch: $current_branch"
    
    # Push to GitHub
    git push origin "$current_branch"
    log_success "Successfully pushed to GitHub"
}

# Prepare deployment package
prepare_deployment() {
    log_info "Preparing deployment package..."
    
    DEPLOY_DIR="$BACKUP_DIR/deploy_$DATE"
    mkdir -p "$DEPLOY_DIR"
    
    # Copy WordPress files (excluding development files)
    log_info "Copying WordPress files..."
    
    # Copy theme files
    if [ -d "app/public/wp-content/themes/pcs-twentytwentyfive-child" ]; then
        cp -r app/public/wp-content/themes/pcs-twentytwentyfive-child "$DEPLOY_DIR/"
        log_info "Copied child theme"
    fi
    
    # Copy custom plugins
    for plugin in pcs-blog-importer pcs-enhanced-recipes pcs-recipe-importer pcs-universal-importer; do
        if [ -d "app/public/wp-content/plugins/$plugin" ]; then
            mkdir -p "$DEPLOY_DIR/plugins"
            cp -r "app/public/wp-content/plugins/$plugin" "$DEPLOY_DIR/plugins/"
            log_info "Copied plugin: $plugin"
        fi
    done
    
    # Copy uploads (media files) - be selective to avoid large files
    if [ -d "app/public/wp-content/uploads" ]; then
        mkdir -p "$DEPLOY_DIR/uploads"
        # Copy only recent uploads or specific directories
        find app/public/wp-content/uploads -name "*.jpg" -o -name "*.png" -o -name "*.gif" -o -name "*.pdf" | head -100 | xargs -I {} cp {} "$DEPLOY_DIR/uploads/" 2>/dev/null || true
        log_info "Copied recent media files"
    fi
    
    # Create deployment instructions
    cat > "$DEPLOY_DIR/DEPLOYMENT_INSTRUCTIONS.txt" << EOF
Pacific Cloud Seafood - Deployment Package
Generated: $DATE

UPLOAD INSTRUCTIONS:
1. Upload pcs-twentytwentyfive-child/ to wp-content/themes/
2. Upload plugins/ contents to wp-content/plugins/
3. Upload uploads/ contents to wp-content/uploads/
4. Activate the child theme in WordPress admin
5. Activate all custom plugins
6. Test functionality

IMPORTANT:
- Backup your live site before uploading
- Test in staging environment first
- Update database URLs if necessary
- Check file permissions after upload

FILES INCLUDED:
- Child theme: pcs-twentytwentyfive-child
- Custom plugins: pcs-blog-importer, pcs-enhanced-recipes, pcs-recipe-importer, pcs-universal-importer
- Recent media files
EOF
    
    log_success "Deployment package prepared in: $DEPLOY_DIR"
    log_info "Package contents:"
    ls -la "$DEPLOY_DIR"
}

# Main deployment function
main() {
    log_info "Starting Pacific Cloud Seafood deployment process..."
    
    # Check prerequisites
    check_directory
    create_backup_dir
    
    # Create backups
    backup_current_state
    
    # Git operations
    commit_changes
    push_to_github
    
    # Prepare deployment
    prepare_deployment
    
    log_success "Deployment process completed successfully!"
    log_info "Next steps:"
    echo "1. Review the deployment package in: $BACKUP_DIR/deploy_$DATE"
    echo "2. Upload files to your Hostinger server"
    echo "3. Test the live site functionality"
    echo "4. Update database URLs if necessary"
    
    # Ask if user wants to open the deployment directory
    read -p "Do you want to open the deployment directory? (y/N): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        open "$BACKUP_DIR/deploy_$DATE"
    fi
}

# Script usage
usage() {
    echo "Usage: $0 [options]"
    echo "Options:"
    echo "  -h, --help     Show this help message"
    echo "  -b, --backup   Create backup only (no git operations)"
    echo "  -g, --git      Git operations only (no deployment package)"
    echo "  -p, --package  Create deployment package only"
}

# Parse command line arguments
case "${1:-}" in
    -h|--help)
        usage
        exit 0
        ;;
    -b|--backup)
        check_directory
        create_backup_dir
        backup_current_state
        ;;
    -g|--git)
        check_directory
        commit_changes
        push_to_github
        ;;
    -p|--package)
        check_directory
        create_backup_dir
        prepare_deployment
        ;;
    "")
        main
        ;;
    *)
        log_error "Unknown option: $1"
        usage
        exit 1
        ;;
esac
