# Instagram Carousel Generator

A powerful web-based tool that transforms blog content into engaging 10-slide Instagram carousel posts using AI. Built with PHP, JavaScript, and Google Gemini AI.

## Features

- **AI-Powered Content Analysis**: Automatically structures blog content into 10 engaging carousel slides
- **Customizable Design**: Choose colors, fonts, layouts, and styling options
- **Real-time Preview**: See your carousel slides as you customize them
- **High-Quality Export**: Download all 10 slides as PNG images in a ZIP file
- **Mobile Responsive**: Works seamlessly on desktop, tablet, and mobile devices
- **cPanel Compatible**: Easy deployment on any cPanel hosting

## Tech Stack

### Frontend
- HTML5, CSS3, Vanilla JavaScript
- Bootstrap 5 for responsive UI
- Fabric.js for canvas manipulation
- Google Fonts for typography

### Backend
- PHP 8.x
- Google Gemini AI API
- Session-based rate limiting
- No database required

## Installation

### Prerequisites
- PHP 8.0 or higher
- cPanel hosting or Apache server
- Google Gemini API key (free tier available)

### Step 1: Upload Files

Upload the entire project to your web server:

```
/your-domain/
  ├── config.php
  ├── index.php
  ├── generate.php
  ├── export.php
  ├── download.php
  ├── cleanup.php
  ├── .htaccess
  ├── assets/
  │   ├── css/
  │   ├── js/
  │   └── templates/
  └── output/
```

### Step 2: Configure API Key

1. Get your free Google Gemini API key from [Google AI Studio](https://makersuite.google.com/app/apikey)

2. Edit `config.php` and add your API key:

```php
define('GEMINI_API_KEY', 'your-actual-api-key-here');
```

### Step 3: Set Permissions

Set the correct permissions via FTP or File Manager:

```bash
# Directories: 755
/assets/
/assets/css/
/assets/js/
/assets/templates/
/output/

# Files: 644
*.php
*.js
*.css

# Output directory: 777 (for write access)
/output/
```

### Step 4: Set Up Cron Job (Optional)

Add a cron job to automatically clean up old files:

```
0 * * * * php /path/to/your/project/cleanup.php
```

This runs every hour and deletes files older than 1 hour.

### Step 5: Access the Application

Navigate to:
```
https://yourdomain.com/
```

Or if in a subfolder:
```
https://yourdomain.com/your-folder/
```

## Usage

### 1. Input Content
- Paste your blog article (1000-3000 words recommended)
- Select content niche and tone
- Enter your Instagram username
- Click "Analyze Content with AI"

### 2. Review Slides
- AI generates 10 carousel slides
- Review and edit each slide's content
- Click "Continue to Design"

### 3. Customize Design
- Choose colors (primary, accent, background)
- Select fonts (heading and body)
- Adjust padding and text size
- Preview each slide in real-time

### 4. Generate & Download
- Click "Generate & Download"
- Wait for image generation (15-30 seconds)
- Download ZIP file with all 10 slides
- Upload to Instagram!

## File Structure

```
/
├── config.php                          # API configuration
├── index.php                           # Main application
├── generate.php                        # AI API handler
├── export.php                          # Image export handler
├── download.php                        # ZIP download handler
├── cleanup.php                         # Cleanup script
├── .htaccess                           # Apache configuration
├── assets/
│   ├── css/
│   │   └── style.css                   # Custom styles
│   ├── js/
│   │   ├── app.js                      # Main app logic
│   │   └── canvas-generator.js         # Canvas rendering
│   ├── fonts/                          # Custom fonts (optional)
│   └── templates/
│       ├── template1.json              # Design template 1
│       └── template2.json              # Design template 2
└── output/                             # Temporary file storage
```

## Configuration Options

### API Settings (config.php)

```php
// API Configuration
define('GEMINI_API_KEY', 'your-key');
define('MAX_CONTENT_LENGTH', 10000);        // Max input characters
define('MAX_GENERATIONS_PER_SESSION', 10);  // Rate limit
define('TEMP_FILE_LIFETIME', 3600);         // 1 hour in seconds

// Image Settings
define('CANVAS_WIDTH', 1080);
define('CANVAS_HEIGHT', 1350);
define('IMAGE_QUALITY', 90);
```

### Customization

Edit `assets/css/style.css` to customize:
- Color schemes
- Font sizes
- Spacing and layouts
- Animations

## Troubleshooting

### Issue: "API key not configured"
**Solution**: Edit `config.php` and add your Gemini API key

### Issue: "Rate limit exceeded"
**Solution**: Wait 1 hour or increase `MAX_GENERATIONS_PER_SESSION` in config.php

### Issue: "Failed to save images"
**Solution**: Check that `/output/` directory has write permissions (777)

### Issue: Images not generating
**Solution**:
- Check browser console for JavaScript errors
- Ensure Fabric.js is loading correctly
- Verify PHP version is 8.0+

### Issue: ZIP download not working
**Solution**:
- Check PHP ZipArchive extension is installed
- Verify file permissions on output directory

## Security Considerations

- **API Key**: Keep `config.php` above `public_html` directory
- **Rate Limiting**: Configured to prevent abuse
- **Input Sanitization**: All user inputs are sanitized
- **File Cleanup**: Automatic deletion of temporary files
- **HTTPS**: Enable HTTPS in .htaccess for production

## Performance Optimization

1. **CDN**: All external libraries loaded from CDN
2. **Caching**: Browser caching enabled via .htaccess
3. **Compression**: GZIP compression enabled
4. **Image Quality**: Optimized at 90% quality
5. **Lazy Loading**: Fabric.js loaded only when needed

## Browser Support

- Chrome/Edge: ✅ Full support
- Firefox: ✅ Full support
- Safari: ✅ Full support
- Mobile browsers: ✅ Responsive design

## API Limits

### Google Gemini Free Tier
- 60 requests per minute
- Generous monthly quota
- No credit card required

**Tip**: If you hit limits, upgrade to paid tier or implement longer rate limiting.

## Future Enhancements

- [ ] URL scraper to fetch blog content directly
- [ ] Multi-language support
- [ ] Animation preview (carousel swipe effect)
- [ ] Direct Instagram posting via API
- [ ] Template marketplace
- [ ] Analytics dashboard
- [ ] Batch processing

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review configuration settings
3. Check browser console for errors
4. Verify API key is valid

## License

This project is provided as-is for personal and commercial use.

## Credits

- **Google Gemini AI** for content analysis
- **Fabric.js** for canvas rendering
- **Bootstrap 5** for UI framework
- **Google Fonts** for typography

## Version

**v1.0.0** - Initial Release

---

**Made with ❤️ for content creators**
