=== Peep This ===
Contributors: Seags
Donate link: none
Tags: gatorpeeps, peeps, social, bookmarking
Requires at least: 1.5
Tested up to: 2.7.1
Stable tag: 1.3

Adds a "Peep This Post" link to every post and page, so your readers can share entries on Gatorpeeps. Shortens URLs in advance.

== Description ==

Adds a "Peep This" link to every post and page. Uses GatorURL to shorten the URL to save those precious characters. Customize under Settings > Peep This. Includes 4 buttons.

NOTE: On WordPress 1.5, you must go to Options > Peep This and click "Save Options" before the Peep This links will appear.

Copyright 2009  Tx Daily ( support : http://txdaily.co.za/wordpress/peep-this/ )
Freely released under Version 2 of the GNU General Public License as published
by the Free Software Foundation, or, at your option, any later version.

== Installation ==

1. Upload the `peep-this` folder to `/wp-content/plugins/`.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. If you're using WordPress MU and want this plugin active for all blogs, move `peep-this.php` to `/wp-content/mu-plugins/` at this point.
4. Peep This icons should automatically appear on every post and page! Go to Settings > Peep This to change stuff.

== Version History ==

1.3: Added the "nofollow" attribute to links and removed URL shortner as Gatorpeeps now takes on that responsibility.

1.02: Minor update

1.01: Fixed display bug.

1.0: 2009-05-04: First release.

== Frequently Asked Questions ==

= How does the cache work? =

Cached short URLs are saved to the postmeta table when a visitor views posts. For any future pageloads, those URLs are loaded, instead of pinging the Th8.us server (or is.gd, TinyURL, etc.). This works, because as long as the post's permalink doesn't change, the short URL from the third-party service doesn't change.

The cache is invalidated by setting the existing short URLs in the postmeta table to "getnew" as needed. By reusing the old fields instead of replacing them, I don't bump up the `meta_id` counter needlessly. When the next person visits that post, the `get_tweet_this_short_url` function in Tweet This sees this and gets a new short URL.

What triggers a cached URL as invalid? When you save a post (including editing and publishing), the cache is invalidated in case you changed the permalink. Secondly, when you change URL services under Settings > Tweet This or change permalink structures under Options > Permalinks, all the cached URLs are set to "getnew". If you move your blog to a different directory or domain name, just change URL services and then change back to trigger a refresh on the cache.

When you deactivate the plugin, all the cached URLs are deleted.

= Why doesn't Tweet This show when I preview a draft post? =

Because I'd have to fetch a short URL if it did, and the permalink for the post isn't set yet. It would be something like /?p=1, which would just waste an entry in GatorURL service's database.

== Screenshots ==

1. Peep This options page.