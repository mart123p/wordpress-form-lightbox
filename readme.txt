=== Plugin Name ===
Contributors:      myphpmaster
Plugin Name:       Form Lightbox
Plugin URI:        http://www.myphpmaster.com/form-lightbox/
Tags:              form lightbox,pop-up
Author URI:        http://www.myphpmaster.com/
Author:            myphpmaster
Donate link:       http://www.myphpmaster.com/donate/
Requires at least: 3.0 
Tested up to:      3.7.1
Stable tag:        2.1
Version:           2.1

This plugin will turn the form to lightbox. Support Gravity Form and Contact Form 7 shortcode.

== Description ==
The pop-up or lightbox for your shortcoded form, iframe and any other inline content.


Please visit plugin <a href="http://www.myphpmaster.com/form-lightbox/">site</a>

== Installation ==

1. Upload `form-lightbox` folder to the `/wp-content/plugins/` directory
2. Activate the `Form Lightbox` plugin through the 'Plugins' menu in WordPress
3. Place caller shortcode [formlightbox_call title="{title of the lightbox}" class="{unique_id}"]Click here[/formlightbox_call] and object shortcode
[formlightbox_obj id="{unique_id}" style="" onload="false"][form shortcode here][/formlightbox_obj] in your post/page. {unique_id} can be any text / number without spaces.
You also can use button in text-editor.
4. You can edit plugin setting at Settings > Form Lightbox.

== Frequently Asked Questions ==

= Is it support Gravity Form and Contact Form 7 =

This plugin support shortcode, so it support CF7 and Gravity Form. Please make sure to activate AJAX when using Gravity Form.

= What about custom html form? =

Absolutely yes.

== Screenshots ==
1. Front-end
2. Back-end


== Changelog ==

= 2.1 =
* Updated on 11/26/2013
* Removed Fancybox due to license issue
* Removed /jquery/ folder and all it's content

= 2.0.1 =
* Updated on 11/25/2013
* changed plugin url
* changed admin options default value for the fancybox

= 2.0 =
* Released on 11/20/2013
* update fancybox to v2.1.5
* update colorbox to v1.4.33
* changing admin options to match the fancybox changes
* added new /jquery folder

= 1.1.2 =
* auto updating issue

= 1.1.1 =
* solved onload='false' problem

= 1.1 =
* New admin page to control settings
* Two type of lightbox: Fancybox and Colorbox
* New id structure to avoid conflict
* Option to add lightbox to WP Menu

= 1.0.1.1 =
* Update fancybox.css z-index value.

= 1.0.1 =
* Add new shortcode [formlightbox_call] and [formlightbox_obj]
* Use class for caller which can be called multiple times.