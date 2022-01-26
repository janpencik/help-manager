=== Help Manager ===
Contributors: bohemiaplugins, janpencik
Donate link: https://bohemiaplugins.com/donate
Tags: help, documentation, client sites, clients, docs
Requires at least: 4.9
Tested up to: 5.9
Stable tag: 1.0.0
Requires PHP: 5.6
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Create documentation for the site's authors, editors, and contributors viewable in the WordPress admin and avoid repeated "how-to" questions.

== Description ==

Site operators can create detailed, hierarchical documentation for the site’s authors, editors, and contributors, viewable in the WordPress admin. Powered by Custom Post Types, you get all the power of WordPress to create, edit, and arrange your documentation. Perfect for customized client sites.

Highly inspired by the [WP Help](https://wordpress.org/plugins/wp-help/) plugin, Help Manager provides the same functionality, up-to-date compatibility with WordPress, and brings some of the most requested features by its users.

### 🎉 Main Features ###
* Gutenberg and Classic Editor support
* Improved drag & drop reordering
* You can now link to other help documents
* WPML support for multilingual documentation
* RTL support
* Easy import/export

### ⚙️ Admin Features ###
* Change admin appearance (menu title, menu icon, menu order)
* Add a dashboard widget
* Add admin bar link for quick help documents access

### 📙 Navigation Features ###
* Floating document navigation + automatic anchor links
* Previous and next document links
* Child documents navigation
* Scroll to the top link

### 📝 Formatting Features ###
* Open linked images in a popup using [Magnific Popup](https://dimsemenov.com/plugins/magnific-popup/)
* Responsive iframes using [Reframe.js](https://dollarshaveclub.github.io/reframe.js/) (e.g., YouTube videos)
* Responsive tables

### 🔐 User Permissions ###
* Specify admin users that can access plugin settings 
* Choose which user roles can add, edit and delete help documents
* Choose which user roles can view help documents
* Custom user roles are supported

### 🎨 Customization Features ###
* Add custom CSS and modify document view to your needs

### 🚀 Roadmap ###
1. create an external hub that will allow you to synchronize documents between all your websites without any limitation
1. show help button conditionally on related admin screens and open help documents directly without the need to leave your current screen (inspired by [HelpScout](https://www.helpscout.com/))

== Installation ==

1. Upload `help-manager` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit 'Publishing Help' in the menu to get started

== Frequently Asked Questions ==

= Can I migrate from WP Help plugin? =

Definitely. Just convert your documents into `Help Documents` post type. You can either use the [Post Type Switcher](https://wordpress.org/plugins/post-type-switcher/) plugin or run this SQL query in your database:
```
UPDATE wp_posts SET post_type = 'help-docs' WHERE post_type = 'wp-help'
```

If you are using the Post Type Switcher, change the post type of your posts to `Help Documents`.

== Screenshots ==

1. The Publishing Help screen, which lists and displays available help documents.

== Changelog ==

= 1.0 =
* Initial release

== Upgrade Notice ==