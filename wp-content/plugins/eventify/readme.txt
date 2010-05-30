=== Eventify - Simple Events  ===
Contributors: Designerfoo.com, DouleDesigns
Donate link: http://designerfoo.com/wordpress-plugin-eventify-simple-events-management
Tags: events, event scheduling, events storing, displaying events, event widget, events wordpress, simple events, widget, posts, simple events, page, post, event page, event post
Requires at least: 2.8.0
Tested up to: 2.9
Stable tag: 1.6.e

Eventify makes it extremely simple for you to store, display events either as posts/popups or as a list on a page, simple & easy to use.

== Description ==

[Subscribe to the RSS feed](http://feeds.feedburner.com/Designerfoo) or [subscribe via Email](http://feedburner.google.com/fb/a/mailverify?uri=Designerfoo&loc=en_US), to know what other updates/plugins/themes I am releasing and to keep track of their updates.

With Eventify You can schedule events and choose to either display the events using the widget display in the sidebar **OR** as a list on a wordpress page **OR** both. Create a POST with event details using the plugin. The events come up as links, which once clicked, display events in a popup **OR** open up the POSTs that they were saved as. Basically, You can choose how you want
to display events, either as a lightbox style popup or save and display the events as wordpress posts. You can also let your blog users/authors/contributors/subscribers/administrators add events directly from a widget in the frontend of the website! For more on this have a look a the video here [Eventify Home](http://designerfoo.com/wordpress-plugin-eventify-simple-events-management#contentstartshere)

**Features**

1. One can edit the CSS for the popups to virtually anything! and From 1.6.b onwards, its a cool *jQuery based popup*. 
1. Display next "n" events or Display events happening in the next "n" days.
1. Now you can let your users add events right from the sidebar and/or from a page or post!
1. Bulk upload events into the database using excel csv format. *Please view the [how to video](http://designerfoo.com/wordpress-plugin-eventify-simple-events-management) first - which is a little outdated just note there are 6 columns now in the order of title, date, time, desc, venue, event timezone*.
1. You can show events as popups or as wordpress posts.
1. Use [eventifytag] anywhere in POST/Text Widget/PAGE to display the list of upcoming events.
1. Use [eventifyform] shortcode/tag anywhere in POST/PAGE to allow users/admin/editors/authors to add events.
1. Backup all your events and restore anytime. [How To Video](http://designerfoo.com/wordpress-plugin-eventify-simple-events-management#contentstartshere)



[Subscribe to the RSS feed](http://feeds.feedburner.com/Designerfoo) or [subscribe via Email](http://feedburner.google.com/fb/a/mailverify?uri=Designerfoo&loc=en_US), to know what other updates/plugins/themes I am releasing

**Please note** 

*If you are updating from versions 1.2 or lower, please make asure you delete/uninstall the old plugin first! made modifications to the database!*

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload folder `eventify` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. There would an option available in the settings tab, called 'Eventify', from where you can add/schedule/delete events.
1. There is a bulk upload feature also, but still in beta, to use it correctly please have a look at the video on 
[the plugin page](http://designerfoo.com/wordpress-plugin-eventify-simple-events-management)
1. If you are experiencing any problems after an upgrade, from any previous version to 1.2.b, please deactivate, un-install and re-install from this place.
1. Its highly recommended that you visit [the plugin page](http://designerfoo.com/wordpress-plugin-eventify-simple-events-management) and have a look at [the video tutorial](http://designerfoo.com/wordpress-plugin-eventify-simple-events-management) for using the plugin.

**Tag Usage: To display events as list on a page/post/text widget use the following shortcode**



[eventifytag displaytype="events" displayno="1"] 

displaytype can be either "events" or "days" and displayno has to be a valid number.

1. If you set the displaytype as "events" - Next *n* number of events will be listed. This number is set using the "displayno" parameter.

1. If you set the displaytype as "days" - Events occuring in the next *n* days will be listed. This number is set using the "displayno" parameter.

1. If you still are facing some problems please have a look at the video tutorial on [the plugin page](http://designerfoo.com/wordpress-plugin-eventify-simple-events-management)

**Tag Usage: To allow users/admin/authors/editors to add events using a POST/PAGE**

[eventifyform loggedin="admin" popuponly="1"]

1. You can set loggedin to either "admin" or "editor" or "author" or "user". Setting the loggedin parameter with the value of "user" would let any one using the PAGE/POST to add events. 

1. If you set popuponly as "1", the form will only allow the admin/editor/author/user to enter events that are stored as "popups" instead of showing the admin/editor/author/user an option to choose between storing events either as "popups" or "posts".

== Frequently Asked Questions ==

= Have questions? =

Come on [the plugin page](http://designerfoo.com/wordpress-plugin-eventify-simple-events-management) and ask, I will answer! For now,
since this is my first time, I don't really know what you 
may require!

If you did like me to customize this plugin, [do ask](http://designerfoo.com/wordpress-plugin-eventify-simple-events-management) ! 

[Subscribe to the RSS feed](http://feeds.feedburner.com/Designerfoo) or [subscribe via Email](http://feedburner.google.com/fb/a/mailverify?uri=Designerfoo&loc=en_US), to know what other updates/plugins/themes I am releasing

== Screenshots ==

1. screenshot-1.png
1. screenshot-2.png

== Changelog ==


[Subscribe to the RSS feed](http://feeds.feedburner.com/Designerfoo) or [subscribe via Email](http://feedburner.google.com/fb/a/mailverify?uri=Designerfoo&loc=en_US), or [facebook page](http://www.facebook.com/pages/Eventify/215563946048) to know what other updates/plugins/themes I am releasing.

1.6.e - Fixed bug with deleting events, now events delete gracefully.

1.6.d - Small bug fixes brought up to notice by users.

1.6.c - Major bug fix! Please upgrade to this version. Thanks to DANIELA. :) 

1.6.b - Major upgrade! Added functionality to edit events that have been already added(Thanks to THOMAS for suggesting). Added functionality to set if users can choose between "popups" or "posts" or just allow them to store events as "posts"(Thanks DANIELA to for  suggesting). Added a new short tag that would allow you to add a form for adding new events on any POST/PAGE. Also with this new release, the plugin will now use jQuery for other upcoming features and some features like the POPUP for events has been changed to jQuery.

1.5.a - Some bug fixes and a back up option, for backing up all events in the database. To know more about the backup feature watch the [howto backup video]()

1.4.e  - Minor release with some bug fixes :) Added  a [facebook page](http://www.facebook.com/pages/Eventify/215563946048) too ... if you use it join it :) 

**1.4.c** - Fixed a major issue with new installs, the database table will not be created with 1.4.b, you have to use the 1.4.c for that. That's the reason no events would be added if you were trying to do so. Sorry!

1.4.b - Updated both the front end and back end forms and restructured some code, will be cleaning up code for other files with next releases also. As for the front end and back end forms added **'Time from'** and **'Time to'** fields on request. :)

1.4.a - Added another widget, which is supplied with the plugin to let your users add events, it can be configured based on the roles present in wordpress. For more on this have a look a the video here [Eventify Home](http://designerfoo.com/wordpress-plugin-eventify-simple-events-management#contentstartshere)

1.3.c - Fixed js bug in IE 8 Thanks to Dustin.

1.3.b - Minor bug fix release.

1.3.a - Added a feature to use shortcodes [enventifytag] that allows you to list events in pages/posts/text widgets/etc. New video tutorials uploaded, showing how to use the plugin and its features. Fixed some issues with bulk upload for events. For more have a look at the description tab or visit [the plugin page](http://designerfoo.com/wordpress-plugin-eventify-simple-events-management)

1.2.b - Added some additional date formats for the widget and popup.. post still remains, will be covered in the next update, on request. If you are encountering errors, deactivate, delete and re-install the plugin from wordpress.org.

1.2.a - Added a feature to store events as posts and link them to the widget showing events.

1.1.e - Added international time zones for events, fixed issue with options page.

1.1.d - Added Lightbox type display for events, when events are clicked up in the sidebar widget, also fixed bug with event venue not being showing up on popups.

1.1.c - Added feature to display date on the widget as mm/dd/yyyy or dd/mm/yyyy and added a venue column.

1.1.b - Addressed some issues with deleting the events and bulk upload features.

== Upgrade Notice ==

If you upgrading from 1.1 or 1.2.a to any newer versions please make sure that you deactivate, delete the plugin and re-install it.



