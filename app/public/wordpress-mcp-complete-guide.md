# WordPress MCP Installation Guide

## Step 1: Install WordPress MCP Plugin

1. Download the latest `wordpress-mcp.zip` from:
   https://github.com/Automattic/wordpress-mcp/releases

2. Install the plugin:
   - Extract the zip file
   - Copy the `wordpress-mcp` folder to:
     `/Users/ryanhorwath/Local Sites/pcsnew/app/public/wp-content/plugins/`
   
   OR via WordPress Admin:
   - Go to Plugins > Add New > Upload Plugin
   - Upload the zip file

3. Activate the plugin in WordPress Admin

4. Go to Settings > WordPress MCP and enable MCP functionality

## Step 2: Create WordPress Application Password

1. Log into WordPress Admin
2. Go to Users > Your Profile
3. Scroll down to "Application Passwords"
4. Enter a name like "Claude MCP"
5. Click "Add New Application Password"
6. Save the generated password (it won't be shown again!)

## Step 3: Configure Claude Desktop

1. Open the Claude Desktop configuration file:
   ```
   ~/Library/Application Support/Claude/claude_desktop_config.json
   ```

2. Add the WordPress MCP server configuration:

```json
{
  "mcpServers": {
    "wordpress-mcp": {
      "command": "npx",
      "args": ["@automattic/mcp-wordpress-remote"],
      "env": {
        "WP_API_URL": "http://pacific-cloud-seafood.local",
        "WP_API_USERNAME": "coolguy99.",
        "WP_API_PASSWORD": "YOUR_APPLICATION_PASSWORD_HERE",
        "LOG_FILE": "/Users/ryanhorwath/wordpress-mcp.log"
      }
    }
  }
}
```

3. Replace `YOUR_APPLICATION_PASSWORD_HERE` with the application password you generated

4. If you already have other MCP servers configured, just add the "wordpress-mcp" section to your existing configuration

5. Restart Claude Desktop for the changes to take effect

## Step 4: Test the Connection

After restarting Claude, you should see the WordPress MCP server in the available tools. The WordPress plugin will expose various tools for:
- Reading and creating posts
- Managing pages
- Working with media
- Custom post types (like recipes!)
- And more

## Troubleshooting

- Check the log file at `/Users/ryanhorwath/wordpress-mcp.log` for any errors
- Ensure your Local site is running
- Verify the plugin is activated in WordPress
- Make sure MCP functionality is enabled in WordPress Settings > WordPress MCP
