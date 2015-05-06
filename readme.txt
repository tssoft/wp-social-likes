=== Social Likes ===
Contributors: tssoft
Tags: facebook, twitter, vk.com, vkontakte, google+, pinterest, odnoklassniki, mail.ru, social links, share buttons, social, social buttons, jquery
Requires at least: 3.0
Tested Up To: 4.2.1
Stable tag: 5.5.7
License: MIT
License URI: https://raw.github.com/tssoft/wp-social-likes/master/license.md

Little, easy to use plugin that adds single-style buttons with fast like counters for: Facebook, Twitter, Google+, Pinterest, VK.com and others.

== Description ==

Little, easy to use plugin that adds social networks sharing buttons to WordPress pages and posts.

Supported social networks:

 * Facebook
 * Twitter
 * Google+
 * Pinterest
 * LiveJournal
 * VK.com and other popular Russian social networks

Features:

 * Easy to install
 * All buttons in a single style
 * Four different skins give 11 different looks for social buttons 
 * Won't explode your page's layout
 * Much faster than proprietary social buttons
 * Likes counters can be shown for each social network
 * Buttons can be reordered easily
 * Horizontal or vertical buttons layout or single button mode with popup
 * Interactive preview of buttons look on the plugin settings page
 * Control appearance of buttons for each page and post
 * Additional options for Twitter and Pinterest
 
Check screenshots page or try live preview to pick buttons look that best fits theme design of your site.
 
Translation currently available in English and Russian. 
Any help with translating Social Likes to other languages will be greatly appreciated! (i18n with gettext)

Based on the [Social Likes library](http://sapegin.me/projects/social-likes) by Artem Sapegin.


== Frequently Asked Questions ==

= 1. Is it possible to place social buttons above the content of the page/post? =

Yes. By default plugin places social buttons after page/post content. We find it reasonable to view content before sharing it with others. 
But you could place buttons before or even before-and-after page/post content using hidden feature:

1. Go to options list of your WordPress site: /wp-admin/options.php
2. Find 'sociallikes_placement' option
3. Assign one of these values to it: before, after, before-after
4. Don't forget to apply changes with 'Save Changes' button

Also you could insert buttons in an excerpt, see question 4 for details.

= 2. Is it possible to use different language (locale) for plugin than Wordpress uses? =

Yes. By default plugin uses the same language (locale) as Wordpress does. 
If there is no translation for this locale, plugin would use English as a default language. We find it suitable for most cases. 
But you could change plugin language (locale) using hidden feature:

1. First of all check whether plugin has translation to your language or not. If it does, go further. If not, contribute translation! :)
2. Go to options list of your WordPress site: /wp-admin/options.php
3. Find 'sociallikes_customlocale' option
4. Assign proper locale to it. (See [list of correct locales](http://codex.wordpress.org/WordPress_in_Your_Language))
5. Don't forget to apply changes with 'Save Changes' button

= 3. Is there any shortcode for placing buttons anywhere inside page/post content? =

Yes. In order to use Social Likes shortcodes on your blog, you should first enable this hidden feature on the WordPress advanced options page. 

1. Go to options list of your WordPress site: /wp-admin/options.php
2. Find 'sociallikes_shortcode' option
3. Change its value to 'enabled'. (You can then switch it back to 'disabled' if you wish to turn the shortcodes off)
4. Don't forget to apply changes with 'Save Changes' button

Then, to insert Social Likes button on any position in a post, paste the following code there: [wp-social-likes]

= 4. Is it possible to show Social Likes buttons for post excerpts, not only for full posts? =

Yes. You should just enable this hidden feature on the WordPress advanced options page.

1. Go to options list of your WordPress site: /wp-admin/options.php
2. Find 'sociallikes_excerpts' option
3. Change its value to 'enabled'
4. Don't forget to apply changes with 'Save Changes' button

= 5. Is there a function to display Social Likes buttons anywhere in a theme template? =

Yes, after the Social Likes plugin is activated, you can use these two functions in your themes:

 * *social_likes( $post_id )* - displays Social Likes buttons
 * *get_social_likes( $post_id )* - returns buttons as string

$post_id is an optional argument. You can use it to get buttons for a specific post.


== Screenshots ==

1. Settings page allows you to customize list of website buttons and how they appear on page
2. Option in editor allows to control appearance of buttons for every post or page 
3. Button style customization with 4 skins, "Icons only" and "Single button" modes


== Changelog ==

= 5.5.7 =
 * Fixed bug with social buttons appearing inside third party recent posts widgets (Reported by rollo3000)
 * Fixed: if sociallikes_customlocale is set to en_US, plugin still uses default locale of WordPress
 * Added Tatar translation (Thanks to Albert Fazli)

= 5.2.5 =
 * Added custom LinkedIn button
 * Added new vector icon for the LiveJournal button
 * Fixed bug with rectangular areas appearing under social likes buttons

= 5.1.26 =
 * Added button for sharing via E-mail
 * Added social_likes() and get_social_likes() functions for using social likes buttons in WordPress themes
 * Fixed bug with plugin options not getting updated on multilingual sites
 * Social Likes library updated to version 3.0.12 (Released 19.01.2015)

= 1.11 =
 * Added "With zeroes" mode to preview
 * Fixed LiveJournal button appearance for all 4 skins
 * Fixed bug with counters disappearing when LiveJournal button is on (Reported by yegorka)
 * Transition to the more optimal way of storing plugin settings
 * *Hidden feature:* Social Likes buttons now can be added to post excerpts (Requested by johnmontfx, see question 4 in FAQ)

= 1.10 =
 * Improved compatibility with Wordpress 3.9+
 * Added custom LiveJournal button
 * Fixed bug with getting first image for the Pinterest automatically (Reported by Dilmaghani Graphics Department)
 * Social Likes library updated to version 3.0.4 (Released 13.05.2014)
 * *Hidden feature:* Shortcode for placing buttons anywhere inside page/post content (See question 3 in FAQ)

= 1.9 =
 * Reminder on unsaved plugin settings
 * Social Likes library updated to version 3.0.2
 * *Hidden feature:* Now it is possible to place buttons before page/post content (See question 1 in FAQ)
 * *Hidden feature:* Now it is possible to use different language (locale) for plugin than Wordpress uses (See question 2 in FAQ)

= 1.8 =
 * Fixed bug with interactive preview reset

= 1.7 =
 * Translations: English, Russian
 * Improved buttons markup to remove side effects caused by theme styles (Reported by Alexander Sarychev)

= 1.6 =
 * Button style customization with 4 skins: Classic, Flat (standard & light) and Birman
 * Icons only mode
 * Option whether to show zero counters or not
 * LiveJournal button removed
 * Social Likes library updated to version 3.0.1
 * Plugin is translation-ready. Any help with translation to your language is appreciated!

= 1.5 =
 * Fixed bug with appearance of websites on settings page for English version of WordPress

= 1.4 =
 * Option to place first image in the post/page to the Image URL field (required by Pinterest)
 * Russian social network buttons now available for English version of WordPress

= 1.3 =
 * Default message text based on the title of page/post being shared (Feedback from Yevgen Timashov)

= 1.2 =
 * Smarter appearance of buttons: if post doesn't contain "more" tag, they will appear both on single post page and on page with multiple posts
 * English and Russian tooltips for share buttons depending on language of current page/post

= 1.1 =
 * Commands for adding buttons to existing posts and/or pages

= 1.0 =
 * First release