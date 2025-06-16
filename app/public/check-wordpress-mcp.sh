#!/bin/bash
# WordPress MCP Setup Checker

echo "=== WordPress MCP Setup Checker ==="
echo ""

# Check if npm/npx is installed
echo "1. Checking Node.js/npm installation..."
if command -v npx &> /dev/null; then
    echo "✓ npx is installed ($(npx --version))"
else
    echo "✗ npx is not installed. Please install Node.js first."
    echo "  Visit: https://nodejs.org/"
fi
echo ""

# Check if the WordPress site is accessible
echo "2. Checking WordPress site accessibility..."
if curl -s -o /dev/null -w "%{http_code}" "http://pcsnew.local" | grep -q "200\|301\|302"; then
    echo "✓ WordPress site is accessible"
else
    echo "✗ WordPress site is not accessible. Make sure Local is running."
fi
echo ""

# Check if Claude config exists
echo "3. Checking Claude Desktop configuration..."
CLAUDE_CONFIG="$HOME/Library/Application Support/Claude/claude_desktop_config.json"
if [ -f "$CLAUDE_CONFIG" ]; then
    echo "✓ Claude config file exists"
    if grep -q "wordpress-mcp" "$CLAUDE_CONFIG"; then
        echo "✓ WordPress MCP is configured in Claude"
    else
        echo "⚠ WordPress MCP not found in Claude config"
    fi
else
    echo "⚠ Claude config file not found. You may need to create it."
fi
echo ""

echo "=== Setup Instructions ==="
echo "1. Install WordPress MCP plugin from: https://github.com/Automattic/wordpress-mcp/releases"
echo "2. Create an Application Password in WordPress Admin > Users > Your Profile"
echo "3. Add the configuration to Claude Desktop config file"
echo "4. Restart Claude Desktop"
echo ""
echo "Configuration file location:"
echo "$CLAUDE_CONFIG"
