# WP Search Any

A lightweight, powerful WordPress search plugin that searches **across all public post types**, pages, posts, custom post types (CPTs), post meta, and taxonomy terms — with optional AJAX live results and modal popup support.


## Features

- Global site search (posts, pages, CPTs, media titles, etc.)
- Searches in **title**, **content**, **custom fields (meta)**, and **taxonomy terms**
- Clean custom search results URL: `/search/content/?keys=your-query`
- Two shortcode variants:
  - Classic search form: `[wp_search_any]`
- Optional **AJAX live search** (instant results dropdown while typing)
- Search history logging in admin (with WP_List_Table interface)
- Simple settings page to toggle AJAX
- No page builders or heavy dependencies — just core WordPress

## Installation

1. Download and unzip the plugin
2. Upload the `wp-search-any` folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** screen in WordPress
4. (Optional) Go to **WP Search Any → Settings** and enable AJAX search
5. Place the shortcode anywhere:
   - `[wp_search_any]` → simple form

After activation, flush permalinks (Settings → Permalinks → Save Changes) if the search page doesn't work immediately.

## Shortcode Examples

```html
<!-- Basic search form -->
[wp_search_any]
```