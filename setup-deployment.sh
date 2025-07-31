#!/bin/bash

# Pacific Cloud Seafood - Deployment Setup Script
# This script helps set up the deployment workflow

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

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
    if [ ! -d ".git" ]; then
        log_error "Not in a git repository. Please run this from your project root."
        exit 1
    fi

    if [ ! -f "deploy.sh" ]; then
        log_error "deploy.sh not found. Please ensure all deployment files are present."
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
    else
        log_success "Local by Flywheel WordPress site detected"
    fi
}

# Check git configuration
check_git_config() {
    log_info "Checking git configuration..."
    
    if ! git config user.name > /dev/null; then
        log_warning "Git user.name not set. Setting up..."
        read -p "Enter your name for git commits: " git_name
        git config user.name "$git_name"
    fi
    
    if ! git config user.email > /dev/null; then
        log_warning "Git user.email not set. Setting up..."
        read -p "Enter your email for git commits: " git_email
        git config user.email "$git_email"
    fi
    
    log_success "Git configuration verified"
}

# Check GitHub remote
check_github_remote() {
    log_info "Checking GitHub remote..."
    
    if ! git remote get-url origin > /dev/null 2>&1; then
        log_error "No origin remote found. Please add your GitHub repository:"
        echo "git remote add origin https://github.com/paccloud/pcsnew.git"
        exit 1
    fi
    
    origin_url=$(git remote get-url origin)
    log_success "GitHub remote configured: $origin_url"
}

# Test GitHub connection
test_github_connection() {
    log_info "Testing GitHub connection..."
    
    if git ls-remote origin > /dev/null 2>&1; then
        log_success "GitHub connection successful"
    else
        log_error "Cannot connect to GitHub. Please check your credentials and network connection."
        log_info "You may need to set up SSH keys or use a personal access token."
        exit 1
    fi
}

# Create .gitignore if it doesn't exist
create_gitignore() {
    if [ ! -f ".gitignore" ]; then
        log_info "Creating .gitignore file..."
        cat > .gitignore << 'EOF'
# WordPress
wp-config.php
wp-content/uploads/
wp-content/cache/
wp-content/backup-db/
wp-content/backups/
wp-content/blogs.dir/
wp-content/upgrade/
wp-content/uploads/
wp-content/wp-cache-config.php
wp-content/plugins/hello.php

# Local development
.DS_Store
.vscode/
.idea/
*.log
*.tmp
*.temp

# Node modules
node_modules/
npm-debug.log*
yarn-debug.log*
yarn-error.log*

# Environment files
.env
.env.local
.env.development.local
.env.test.local
.env.production.local

# Backup files
*.bak
*.backup
backups/

# Local development files
logs/
conf/
Local Sites/

# Deployment
deploy/
*.tar.gz
*.zip
EOF
        log_success "Created .gitignore file"
    else
        log_info ".gitignore already exists"
    fi
}

# Make scripts executable
make_scripts_executable() {
    log_info "Making scripts executable..."
    chmod +x deploy.sh
    chmod +x setup-deployment.sh
    log_success "Scripts are now executable"
}

# Show next steps
show_next_steps() {
    log_success "Deployment setup completed!"
    echo
    log_info "Next steps:"
    echo "1. Ensure Local by Flywheel is running:"
    echo "   - Open Local by Flywheel application"
    echo "   - Start the 'pcsnew' site (should show green indicator)"
    echo "   - Test site at http://pcsnew.local"
    echo
    echo "2. Set up GitHub repository secrets for Hostinger deployment:"
    echo "   - Go to https://github.com/paccloud/pcsnew/settings/secrets/actions"
    echo "   - Add these secrets:"
    echo "     * FTP_SERVER (your Hostinger FTP server)"
    echo "     * FTP_USERNAME (your FTP username)"
    echo "     * FTP_PASSWORD (your FTP password)"
    echo "     * WEBSITE_URL (your live website URL)"
    echo
    echo "3. Test the deployment script:"
    echo "   ./deploy.sh --help"
    echo
    echo "4. Run your first deployment:"
    echo "   ./deploy.sh"
    echo
    echo "5. Monitor the GitHub Actions workflow:"
    echo "   https://github.com/paccloud/pcsnew/actions"
    echo
    log_info "For detailed instructions, see:"
    echo "- DEPLOYMENT-WORKFLOW.md"
    echo "- hostinger-config.md"
    echo "- memory-bank/local-flywheel-setup.md"
}

# Main setup function
main() {
    log_info "Setting up Pacific Cloud Seafood deployment workflow..."
    
    check_directory
    check_git_config
    check_github_remote
    test_github_connection
    create_gitignore
    make_scripts_executable
    show_next_steps
}

# Show help
show_help() {
    echo "Pacific Cloud Seafood - Deployment Setup"
    echo
    echo "This script sets up the deployment workflow for your WordPress site."
    echo
    echo "Usage: $0 [options]"
    echo
    echo "Options:"
    echo "  -h, --help     Show this help message"
    echo "  -c, --check    Check current configuration only"
    echo
    echo "What this script does:"
    echo "1. Verifies git configuration"
    echo "2. Checks GitHub remote connection"
    echo "3. Creates .gitignore if needed"
    echo "4. Makes deployment scripts executable"
    echo "5. Shows next steps for complete setup"
}

# Check configuration only
check_only() {
    log_info "Checking current deployment configuration..."
    
    check_directory
    
    # Check git config
    if git config user.name > /dev/null && git config user.email > /dev/null; then
        log_success "Git user configuration: $(git config user.name) <$(git config user.email)>"
    else
        log_warning "Git user configuration incomplete"
    fi
    
    # Check remote
    if git remote get-url origin > /dev/null 2>&1; then
        log_success "GitHub remote: $(git remote get-url origin)"
    else
        log_error "No GitHub remote configured"
    fi
    
    # Check files
    if [ -f ".gitignore" ]; then
        log_success ".gitignore exists"
    else
        log_warning ".gitignore missing"
    fi
    
    if [ -x "deploy.sh" ]; then
        log_success "deploy.sh is executable"
    else
        log_warning "deploy.sh is not executable"
    fi
    
    if [ -f ".github/workflows/deploy.yml" ]; then
        log_success "GitHub Actions workflow configured"
    else
        log_warning "GitHub Actions workflow missing"
    fi
}

# Parse command line arguments
case "${1:-}" in
    -h|--help)
        show_help
        exit 0
        ;;
    -c|--check)
        check_only
        ;;
    "")
        main
        ;;
    *)
        log_error "Unknown option: $1"
        show_help
        exit 1
        ;;
esac
