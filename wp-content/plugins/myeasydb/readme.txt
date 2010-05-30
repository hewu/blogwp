=== myEASYdb ===
Contributors: camaleo
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9VCM6JTADXFY6
Tags: plugin, admin, administration, data management, mysql, database, tables
Requires at least: 2.9.1
Tested up to: 2.9.2
Stable tag: 0.0.6

Edit and manage your MySQL tables in the easiest and quickest way.

== Description ==
myEASYdb is a WordPress plugin created to help managing your MySQL tables in the easiest and quickest way.

This version of myEASYdb let's you browse and edit your exhisting tables by simply setting the name of the database you want to edit.
You can also define relations between fields. Relations can be defined when a field includes a value that is the key of another table.
By setting a relation you will be able to choose the field value by selecting the description of the related table in a drow down menu.

Future releases will include a wide range of operations on your MySQL tables like, for example:
tables design, fields validation setup, tables maintenance, print/export to PDF and CSV (comma separated text files), etc.

If you like to see myEASYdb in action <a href="http://myeasydb.com/tutorials/intro/" title="myEASYdb Plugin for WordPress Intro" target="_blank">check the Intro video</a>.

Related Links:

* <a href="http://myeasydb.com/" title="myEASYdb Plugin for WordPress">Plugin Homepage</a>

*Please note that this is my first WordPress plugin thus the minimum WordPress required version is 2.9.1 &mdash; the latest available version when I started to code the plugin.
If you are successfully using this plugin with an older WordPress version, [please let me know](http://myeasydb.com/#contact)!*

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the full directory into your `wp-content/plugins` directory
1. Activate the plugin through the 'Plugins' menu in the WordPress Administration page
1. Open the plugin configuration page, which is located under `Settings -> myEASYdb` and fill up the options. If the myEasyDB Database you choose does not contains the myEASYdb own tables you will get the option to create them.
1. You can access to all the tables included in the selected Database trough the myEASYdb menu
1. To access the plugin tools open the `Tools -> myEASYdb menu`

== Frequently Asked Questions ==

= On which browsers do you test myEASYdb? =

I develop and test the plugin on Mozilla Firefox and Google Chrome on Ubuntu Linux.
I do regulary upgrade the browsers to the latest versions as soon as they became available.

Everynow and then I do also test on IE8 in a VirtualBox environment.

= What languages are available? =

At the moment I am working on the English version, however I am following the WordPress guidelines to make it easily translable once it will become 'mature' enough.
If you like to help with the translation please [fill the contact form](http://myeasydb.com/#contact) on the myEASYdb home page.

== Screenshots ==

1. Browsing a table
2. Editing a record
3. Setting up a relation

== Changelog ==

= 0.0.6 (23 February 2010) =
* On Windows servers the main configuration was not loaded due to an issue when defining the installation path.
* Added an option to show debug info on screen.
* Added a check to be sure the MySQL user has sufficient privileges to create myEASYdb own tables.
* Fixed some issues when creating myEASYdb own tables.
* Tested (and working) on an [HostGator](http://secure.hostgator.com/cgi-bin/affiliates/clickthru.cgi?id=maraja) hosted account.
* When a reference is deleted the screen is now properly refreshed.
* NEW! Define which fields you like to filter and filter the table contents list accordingly.

= 0.0.5 (17 February 2010) =
* Fixed an issue on a fresh installation preventing to load the Settings page.
* Minor changes in the Settings page layout to fit a 1024 pixels width screen.
* Contents list page:
	* corrected the fields alignment for referenced fields.
	* when you change the value in the 'records per page' drop down menu the page is resetted to the top.
* Table data was not removed everytime it was necessary.
* Added lables descriptions to butons.

= 0.0.4 (16 February 2010) =
* Added support for tables that does not use a numeric/autoincremented field for the key.
In this case the key used to get/update the record is created using ALL the fields defined as keys.
* Added an indication on the screen about which fields are used as key.
* If it is not possible to determine the table key the code dies (for the moment) showing an info message.
* Headers/data in the table list are now aligned on the right if the field is numeric.
* Every table field is now shown on the Edit Record page: reserved and yet-to-be-handled fields types are represented as readonly input fields.
* The 'records per page' drop down menu is now functional; its original value (defined in the Settings page) is restored when you reload the page.

= 0.0.3 (15 February 2010) =
* Sorry, I have messed up with SVN

= 0.0.2 (15 February 2010) =
* When updating a table, its ID field (the autoincremented one):
	* is now choosen as the first auto-increment field in the table
	* is not overwritten anymore
* Backslashes added by `mysql_real_escape()` are now properly removed before presenting the data on the screen

= 0.0.1 (15 February 2010) =
* This is the first release!

== Upgrade Notice ==

= 0.0.6 =
The plugin did not work on Windows and hosted servers.

= 0.0.5 =
The Settings page was not showed on fresh installations... my fault not to having tested the plugin on a fresh installation :(

= 0.0.4 =
If you need to edit multi-keys records you must update

= 0.0.3 =
* Sorry, I have messed up with SVN while uploading version 0.0.2, this one should be fine

= 0.0.2 =
You are now able to edit tables data

= 0.0.1 =
This is the first release thus it does not yet include all the features I am planning... however it would be nice to give it a try :)
