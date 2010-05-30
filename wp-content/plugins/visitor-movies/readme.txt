=== Visitor Movies ===
Contributors: nkuttler
Author URI: http://www.wordpress-dienstleistungen.de/
Plugin URI: http://www.wordpress-dienstleistungen.de/visitor-movies/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=11041772
Tags: admin, plugin, usability, user tracking, marketing, analytics, clicktracking, clixpy, clicktale, JavaScript, jQuery, JSON,i18n, internationalized

Requires at least: 2.9
Tested up to: 3.0
Stable tag: 0.3.1.2

Did you ever want to know what exactly your visitors are doing on your site? Watch them!

== Description ==

A client of mine wanted a plugin to log what users type into forms. I kind of didn't see the point at first. But a few days later I was a little annoyed that there was apparently no free alternative to websites like clixpy, clicktale and similar services.

It occured to me that recording movies of visits wasn't really hard to do. And so i started a new plugin, loosely based on the unpublished form input tracking plugin.

I am very pleased with the results so far. Of course it's no match for the professional services mentioned above. But it is good enough to gain a few insights into what can be improved on your website.

The recording script is pretty lightweight but has a rather hefty dependeny: jQuery. It also loads json2.js. I'm sure the logging could be rewritten without the jQuery dependency but that really wasn't a priority for the first public release.

This plugin probably needs WordPress 2.8 and PHP 5. I didn't bother to test older versions and won't support them in the future.

Here is a sample movie of what a recorded session can look like. As you can see mouse movements, clicks, form input, select boxes, checkboxes and radio buttons are recorded.

http://www.youtube.com/watch?v=OTv8loMWDPU

<strong>Important</strong>: For the playback you should always use the same browser as the visitor. If you don't, the mouse position will probably be very wrong. The plugin also doesn't save a page's state at the time of recording. This means if comments are added or the content changes, you will see the old movements on a new website.

= The daily report =

On the plugin's options page you can see a list of daily reports. Click on one to see the defailed report.

* Time: When the user visited the site, server time
* URL: Which page he was on
* Referrer: Where he came from
* IP: Visitor's IP
* Browser: Rendering engine and version
* Duration: How much time the visitor spent on the page, in seconds
* Events: How many events were recorded (clicks, mouse movements, etc.)
* Form: Number of data entry events in forms
* Window Sie: Browser size
* Playback: Two buttons. The first one is the standard, the second one for browsers that don't support resize, like Safari and Chrome.

= Support =
Visit the [plugin's home page](http://www.nkuttler.de/2010/05/21/record-movies-of-visitors/) to leave comments, ask questions, etc.

= My plugins =

[Visitor Movies for WordPress](http://www.nkuttler.de/2010/05/21/record-movies-of-visitors/): Did you ever want to know what your visitors are really doing on your site? Watch them!

[Custom Avatars For Comments](http://www.nkuttler.de/wordpress/custom-avatars-for-comments/): Your visitors will be able to choose from the avatars you upload to your website for each and every comment they make.

[Better tag cloud](http://www.nkuttler.de/wordpress/nktagcloud/): I was pretty unhappy with the default WordPress tag cloud widget. This one is more powerful and offers a list HTML markup that is consistent with most other widgets.

[Theme switch](http://www.nkuttler.de/wordpress/nkthemeswitch/): I like to tweak my main theme that I use on a variety of blogs. If you have ever done this you know how annoying it can be to break things for visitors of your blog. This plugin allows you to use a different theme than the one used for your visitors when you are logged in.

[Zero Conf Mail](http://www.nkuttler.de/wordpress/zero-conf-mail/): Simple mail contact form, the way I like it. No ajax, no bloat. No configuration necessary, but possible.

[Move WordPress comments](http://www.nkuttler.de/wordpress/nkmovecomments/): This plugin adds a small form to every comment on your blog. The form is only added for admins and allows you to [move comments](http://www.nkuttler.de/nkmovecomments/) to a different post/page and to fix comment threading.

[Delete Pending Comments](http://www.nkuttler.de/wordpress/delete-pending-comments): This is a plugin that lets you delete all pending comments at once. Useful for spam victims.

[Snow and more](http://www.nkuttler.de/wordpress/nksnow/): This one lets you see snowflakes, leaves, raindrops, balloons or custom images fall down or float upwards on your blog.

[Fireworks](http://www.nkuttler.de/wordpress/nkfireworks/): The name says it all, see fireworks on your blog!

[Rhyming widget](http://www.rhymebox.de/blog/rhymebox-widget/): I wrote a little online [rhyming dictionary](http://www.rhymebox.com/). This is a widget to search it directly from one of your sidebars.

== Installation ==
Unzip, upload to your plugin directory, enable the plugin and configure it as needed.

== Screenshots ==
1. The configuration options.
2. The daily report table.

== Frequently Asked Questions ==
Q: Why are there no logs?<br />
A: Check if your theme has the wp_footer() call, see the [theme development checklist(http://codex.wordpress.org/Theme_Development_Checklist). Check also the percentage option and if there were any visitors.

Q: How does the logging work?<br />
A: A variety of browser events are logged with the help of jQuery and sent to your server via Ajax. The JavaScript could be modified to work with other CMSes or custom apps.

Q: How does the playback work?<br />
A: The browser requests the logged data via Ajax and uses jQuery to display the logged events. However, no infomation is saved on what the website looked like when the user visited it, you will see the user's actions projected on the **current** page.
You will also see the visitor's action in **your browser**. This could mean that the playback is inaccurate as different browser enginges tend to display things slighty differently. This is less of a problem if your theme was build properly.

Q: Couldn't you add feature X, improve Y, etc?<br />
A: Sure, you can leave a comment on the plugin's home page, but the plugin is good enough for me at the moment. If you really want a feature contact me for [professional WordPress services](http://www.nkuttler.de/contact/).

Q: Is this secure?<br />
A: No. An attacker could fill your harddisk with logfiles.

== Changelog ==
= 0.3.1.2 ( 2010-05-24 ) =
 * Sort the log overview by date
 * Use the same domain for plugin and author homepage
 * More doc updates
= 0.3.1.1 ( 2010-05-22 ) =
 * Fix bug introduced in 0.3.0.5: incorrect log paths before 10am
 * Don't chmod directories at all.
= 0.3.1 ( 2010-05-22 ) =
 * Make the path to the log dir configurable
 * Code cleanup
= 0.3.0.5 ( 2010-05-22 ) =
 * Make sure the logger paths are numbers
= 0.3.0.4 ( 2010-05-21 ) =
 * Fix for servers that can't chmod log files and directories, reported by Frank.
= 0.3.0.3 ( 2010-05-21 ) =
 * Move log directory so that the logs are preserved on automatic upgrades.
 * Doc updates
= 0.3.0.2 ( 2010-05-21 ) =
 * Fix breakage due to plugin dir rename
 * Doc updates
 * Add a pot file
= 0.3.0 ( 2010-05-21 ) =
 * Public release
= 0.2.2 ( 2010-05-19 ) =
 * New feature: Log only a percentage of visitors.
= 0.2.1 ( 2010-05-15 ) =
 * New feature: log table sorting
 * Log by server time.
 * New icons
 * Various other improvements.
= 0.2.0.2 ( 2010-05-14 ) =
 * More bugfixes and a small logging improvement.
= 0.2.0.1 ( 2010-05-14 ) =
 * Some small bugfixes, and add the missing VisitorMovies class file.
= 0.2 ( 2010-05-13 ) =
 * Use JSON, separate app logic from WordPress logic, and many other improvements
= 0.1.1.2 ( 2010-05-11 ) =
 * Fix flashwin
= 0.1.1.1 ( 2010-05-11 ) =
 * Fix logging and playback
= 0.1.1 ( 2010-05-11 ) =
 * Split WordPress and VisitorMovies logic
= 0.1 ( 2010-05-10 ) =
 * Non-public release

== Upgrade Notice ==
= 0.3.1 =
 * You should probably move your log directory outside of the web root
= 0.3.0 =
 * First public release
