=== Database Table Manager ===
Contributors: juliancl
Donate link: http://wp-plugins.clark-lowes.info/introduction/
Tags: database, tables, programme, events, stop press, recent events, create, manage, news, lookup tables, left joins
Requires at least: 2.0
Tested up to: 2.9.2
Stable tag: 0.0.2

Want to add and manage custom database tables from the admin area? This plugin contains several easily modified examples.

== Description ==

This plugin folder contains 2 suites of plugins. The Club Manager Plugin contains 4 Plugins - Programme (of Events), Stop Press, Recent Events and News. 
The Specialists Plugin contains a more generic example of the management of a many to many link table between a number of "specialists" and their "specialisms". 
It also demonstrates the use of left joins and lookup tables.  Code to sort data by clicking on the table head and to filter the selection set has also been added.
They are based upon a more generic system which allows creating and adding data to custom database tables from the wordpress control panel.
These plugins can be used as a template for creating different types of table and display or you can just use them as they are if they do what you want to do.


== Installation ==

Choose which of the plugins you wish to use and upload its folder to the plugins folder on your wordpress installation and then Activate it. You should see a new menu item with the name of the plugin just under the Comments entry.
If you choose to download the entire plugin and install it using the plugins Add New function, when you click activate now the plugin seems to fail.  However simply reload the installed plugins page and activate the plugin from there.  It works fine... I will try to fix this in due course.

== Screenshots ==
1. A view of the programme plugin in use. You can select an entry to edit or delete.
2. The plugin contains the prototype of a simple forms engine.  This supports the tinyMCE editor allowing for rich content to be added.
3. This can then be displayed on the public facing site; visit www.dunstablebogtrotters.co.uk to see the output from all 4 plugins.  Currently this page has been converted from an older non-wp site. I am busy adding wp widgit support to the plugins and in future content will be displayed using widgits.

== Changelog ==

= 0.0.1 =

* First release. A bit rough and ready but lets see if I can make svn work.  Once I've got it up there I'll start tidying some loose ends.

= 0.0.2 =

* Code generally refactored, two example suites of plugins now enclosed, Club Manager and Specialists.
* Specialists demonstrates some limited use of left joins and lookup tables.
* code added to sort data by clicking on the column head and filetreing selection set.

