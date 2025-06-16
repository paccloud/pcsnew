# Image Metadata Tool

This tool adds metadata to image files in the uploads directory. It works in chunks and skips duplicates, making it ideal for processing large numbers of images efficiently.

## Features

- Adds copyright, author, title, caption, keywords, and description metadata to images
- Works in chunks to process a manageable number of images at a time
- Skips duplicate images and already processed images
- Can be run from the command line or through the WordPress admin interface
- Intelligently extracts metadata from filenames to populate fields
- Logs processed images to avoid reprocessing
- Supports JPEG and PNG image formats (limited support for PNG)

## Files

- `add_image_metadata.php` - Core functionality for adding metadata to images
- `run_metadata_tool.php` - Command-line interface for the metadata tool
- `admin-integration.php` - WordPress admin integration for the metadata tool
- `README.md` - This documentation file

## Usage

### WordPress Admin

1. Log in to the WordPress admin panel
2. Go to Tools > Image Metadata
3. Enter the directory path to process (defaults to current year/month uploads)
4. Set the limit for how many images to process at once
5. Click "Process Images"

### Command Line

```bash
# Navigate to the WordPress root directory
cd /path/to/wordpress

# Run the tool with default settings (current year/month, 50 images)
php wp-content/themes/pcs25/assets/tools/run_metadata_tool.php

# Run the tool with custom directory and limit
php wp-content/themes/pcs25/assets/tools/run_metadata_tool.php --dir=/path/to/images --limit=100
```

## Metadata Added

The tool adds the following metadata to images:

- **Copyright**: Pacific Cloud Seafood © 2025
- **Author**: Pacific Cloud Seafood
- **Title**: Derived from filename (cleaned and formatted)
- **Caption**: Generated based on detected seafood type
- **Keywords**: Generated based on detected seafood type and location, plus default keywords
- **Description**: Generated based on detected seafood type

## How It Works

1. The tool scans the specified directory for image files
2. It checks if each image has already been processed (using a log file)
3. For new images, it analyzes the filename to extract potential metadata
4. It adds appropriate IPTC metadata to JPEG files
5. For PNG files, it currently just marks them as processed (limited support)
6. It logs each processed image to avoid reprocessing

## Notes

- The tool skips resized images (containing dimensions in filename, e.g., `-300x200`)
- PNG metadata support is limited due to the complexity of the PNG metadata format
- The tool uses the WordPress upload directory structure by default
- Processing is done in chunks to avoid memory issues with large numbers of images
