=== Plugin Name ===
Contributors: elusivelight
Donate link: http://cleverness.org/plugins/to-do-list/
Tags: to-do, to do list, to-do list, list, assign tasks, tasks, admin
Requires at least: 2.8
Tested up to: 3.0
Stable tag: trunk

Manage to-do list items on a individual or group basis with customizable settings.

== Description ==

This plugin provides users with a to-do list feature.

You can configure the plugin to have private to-do lists for each user or for all users to share a to-do list. The shared to-do list has a variety of settings available. You can assign tasks to a specific user (includes a setting to email a new task to its' assigned user) and have only those tasks assigned viewable to a user. You can also assign different permission levels using capabilities. There are also settings to show deadline and progress fields.

A page is added under the Tools menu to manage items and they are also listed on a dashboard widget. You can manage the settings from under the Settings menu.

A sidebar widget is available as well as a shortcode to display the to-do list items on your site.

== Installation ==

1. Upload the folder /cleverness-to-do-list/ to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the settings under the Settings menu
4. Visit To-Do List under the Tools menu

== License ==

This file is part of Cleverness To-Do List.

Cleverness To-Do List is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

Cleverness To-Do List is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this plugin. If not, see <http://www.gnu.org/licenses/>.

== Frequently Asked Questions ==

= I can't mark items as completed =
Please visit the To-Do List settings page and click on Save Changes. There was a typo in a previous version in the default settings.

= What is the shortcode to display items in a post or page? =
[todolist]

Several options are available:

* **title** - default is no title.
* **type** - you can chose *list* or *table* view. Default is *list*.
* **priorities** - default is *show*. Use a blank value to hide (only applies to table view).
* **assigned** - default is *show*. Use a blank value to hide).
* **deadline** - default is *show*. Use a blank value to hide.
* **progress** - default is *show*. Use a blank value to hide.
* **addedby** - default is *show*. Use a blank value to hide.
* **completed** - default is blank. Set to *show* to display completed items.
* **completed_title** - default is no title.
* **list_type** - default is *ol* (ordered list). Use *ul* to show an unordered list.

Example:

Table view with the title of Upcoming Articles and showing the progress and who the item was assigned to.

[todolist title="Upcoming Articles" type="table" priorities="" deadline="" addedby=""]

= Can you explain the permissions in more detail? =

* **View To-Do Item Capability** - This allows the selected capability to view to-do items in the dashboard widget and on the To-Do List page under Tools.
* **Complete To-Do Item Capability** - This allows the selected capability to mark to-do items as completed or uncompleted.
* **Add To-Do Item Capability** - This allows the selected capability to add new to-do items.
* **Edit To-Do Item Capability** - This allows the selected capability to edit existing to-do items.
* **Assign To-Do Item Capability** - This allows the selected capability to assign to-do items to individual users.
* **View All Assigned Tasks Capability** - This allows the selected capability to view all tasks even if *Show Each User Only Their Assigned Tasks* is set to *Yes*.
* **Delete To-Do Item Capability** - This allows the selected capability to delete individual to-do items.
* **Purge To-Do Items Capability** - This allows the selected capability to purge all the completed to-do items.

= What should I do if I find a bug? =

Visit [the plugin website](http://cleverness.org/plugins/to-do-list/) and [leave a comment](http://cleverness.org/plugins/to-do-list/#respond) or [contact me](http://cleverness.org/contact/).

== Screenshots ==

1. Dashboard Widget - Individual Setting
2. Dashboard Widget - Group Setting with Assign Tasks on
3. To-Do List Page - Group Setting with Assign Tasks on
4. To-Do List Page - Group Setting with a minimum permission user, only viewing their assigned items.
5. Editing an Item - Assign Tasks On
6. Settings Page

== Changelog ==

= 2.0.4 =
* Added German translation by Ascobol
* Added Japanese translation by [Takemi Tasaki](http://route58.org)

= 2.0.3 =
* Moved a nonce check to the correct function and added some additional code

= 2.0.2 =
* Removed require_once for pluggable.php from main body of plugin into functions

= 2.0.l =
* Fixed bug where users could not edit or delete other user's item when they had the ability

= 2.0 =
* Changed backend code for better error control and improved performance
* Compatible with WordPress 3.0
* Minor bug fixes
* The page is no longer redirected to the main To-Do List page when marking at item on the dashboard as completed
* Russian translation added

= 1.5.2 =
* Changed the url in the location variable again to work when WP is placed outside the root directory

= 1.5.1 =
* Fixed a problem with the install function
* Changed the url in the location variable

= 1.5 =
* Changed the way CSS is added to the admin pages
* Added more shortcode options
* Changed the way users are selected for the dropdown list
* Added option to show completed date and an option to format the date

= 1.4.1 =
* Bug fix affecting updating table and viewing items

= 1.4 =
* Added progress field
* Added sidebar widget
* Added post/page shortcode to display list
* Added ability to email users a new to-do item
* Removed permission check on install (may help fix WPMU issue)

= 1.3.4 =
* Added Spanish translation (contributed by [Ricardo](http://yabocs.avytes.com/))

= 1.3.3 =
* Fixed a typo in the default options that caused items to be unable to be marked as completed. Please visit the To-Do List settings page and click on Save Changes if you are having difficult marking items as completed

= 1.3.2 =
* Fixed a bug where "assigned by" would show on the dashboard widget when empty
* Renamed functions
* Added a check to prevent blank to-do items

= 1.3.1 =
* Fixed an incompability with PHP 4
* Added a call to the userdata global in the complete function

= 1.3 =
* Added a deadline field and settings
* Only shows users above Subscribers in the Assign To dropdown

= 1.2.1 =
* Removed a div tag from the dashboard widget that did not belong there

= 1.2 =
* Added ability to check off items from dashboard
* Added uninstall function
* Added group support
* Added settings page
* Added permissions based on capabilities
* Cleaned up code some more
* Added ability to set custom priorities
* Improved security
* Added translation support

= 1.1 =
* Enabled the plugin to work from inside a directory

= 1.0 =
* Improved the security of the plugin
* Updated the formatting to match the admin interface
* Cleaned up the code
* Fixed to work in WordPress 2.8

== Upgrade Notice ==

= 2.0.4 =
Two new translations added

= 2.0.3 =
Bug fix

= 2.0.2 =
Bug fix

= 2.0.1 =
Bug fix

= 2.0 =
Backend code changes, bug fixes

= 1.5.2 =
Bug fix

= 1.5.1 =
Bug fix

= 1.5 =
New features added

= 1.4.1 =
Bug fix

= 1.4 =
Added several new features and settings, added new field to database table

= 1.3.4 =
Spanish translation added

= 1.3.3 =
Bug fix, Go to the To-Do List settings page and click on Save Changes if unable to mark items as completed

= 1.3.2 =
Bug fixes
Changed function names

= 1.3.1 =
Bug fixes

= 1.3 =
Added features, changed database structure. Be sure to deactivate and activate plugin.

= 1.2.1
Bug fix

= 1.2 =
Major changes to plugin

== Credits ==

This plugin was originally from Abstract Dimensions (site no longer available) with a patch to display the list in the dashboard by WordPress by Example (site also no longer available). It was abandoned prior to WordPress 2.7.

Spanish translation by [Ricardo](http://yabocs.avytes.com/)

Russian translation by [Almaz](http://alm.net.ru)

German translation by Ascobol

Japanese translation by [Takemi Tasaki](http://route58.org)