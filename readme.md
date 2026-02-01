# WP Search Any

WP Search Any is a comprehensive WordPress search plugin that extends the default WordPress search functionality to search across all post types, pages, custom post types, custom fields, and taxonomies with advanced features like AJAX live search and search history tracking.

## Features

### Enhanced Search Capabilities
- Search across all post types (posts, pages, custom post types)
- Include custom fields in search results
- Search taxonomy terms (categories, tags, custom taxonomies)
- Exclude attachments from search results

### Smart Search Features
- AJAX live search with real-time suggestions
- Search history tracking with admin dashboard

### Customizable Display
- Shortcode support for easy placement
- Custom search results template
- Responsive design for all devices

### Admin Features
- Search history log with filtering
- Clear search history functionality
- AJAX search toggle (enable/disable)
- Clean admin interface

### Technical Features
- Rewrite rules for clean URLs (`/search/`)
- Custom query modifications
- Pagination support
- SEO-friendly search URLs

## Installation

### Method: WordPress Admin (Recommended)
1. Go to Plugins → Add New
2. Search for "WP Search Any"
3. Click Install Now
4. Click Activate


### Shortcodes
```
[wp_search_any]
```

### Search URLs
- Search page: `/search/?keyword=your+search+term`
- Pagination: `/search/?keyword=your+search+term&page=2`

## Configuration

### Admin Settings
1. Go to WP Search Any → Settings
2. Enable AJAX Search: Toggle live search suggestions
3. Save changes

### Search History
1. Go to WP Search Any → Search History
2. View all search queries made on your site
3. Clear history if needed

## Search Results Template

The plugin includes a custom search results template that displays:
- Search term
- Number of results found
- Paginated results with thumbnails
- Post excerpts
- Clean pagination

You can customize the template by editing:  
`wp-content/plugins/wp-search-any/templates/search-results.php`

## Frequently Asked Questions

### Q: Does this replace WordPress default search?
A: Yes, it extends and enhances the default search functionality to be more comprehensive.

### Q: Can I customize the search results layout?
A: Yes, you can modify the `search-results.php` template file in the plugin's templates folder.

### Q: How do I enable AJAX live search?
A: Go to WP Search Any → Settings and check "Enable AJAX Search".

### Q: Where are search logs stored?
A: Search logs are stored in a custom database table: `wp_wp_search_any_history`.

### Q: Can I clear all search history?
A: Yes, go to WP Search Any → Settings and click "Clear All Search History".


## 🧑‍💻 Who Should Use This Theme?

- WordPress theme developers
- PHP developers entering WordPress
- Freelancers building custom client themes
- Developers who want full control

## 📜 License

Personal and educational use.
Free to modify and extend.

---

## 🤝 Author

Shimanta Das  
WordPress & PHP Developer

---

Happy coding 🚀

