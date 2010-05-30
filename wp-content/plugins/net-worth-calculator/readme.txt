=== Net Worth Calculator ===
Contributors: Credit Card Finder
Tags: net worth, personal wealth, calculator, personal finance
Requires at least: 2.9
Tested up to: 2.9.2
Stable tag: 1.5.3

The Net Worth Calculator allows you to record information on your monthly assets and liabilities, and graph or display a data table of your net worth.

== Description ==

The Net Worth Calculator plugin helps you keep track of your monthly assets, liabilities, and net worth.

This plugin is developed by the team at [Credit Card Finder](http://www.creditcardfinder.com.au "Australian Credit Card Comparison and Application Service")

== Display Options ==

= Short Code: Data View =

To insert a monthly data view into a post:

[networth month="02-2010" view="data"]

By default, the post author's data view is displayed.

To display another blogger's data, the 'user' parameter can be used:

[networth month="02-2010" view="data" user="fred"]

= Short Core: Graph =

To insert the post author's graph into a post:

[networth month="02-2010" view="graph"]

By default, the post author's data view is displayed.

To display another blogger's data, the 'user' parameter can be used:

[networth month="02-2010" view="graph" user="jeremy"]

Width and height can be altered, as well as the number of months to display on the graph.

[networth month="02-2010" view="graph" width="300" height="300" num_of_months="6"]

= Widget =

A "Net Worth Calculator Widget" can be included in your widget-aware WordPress theme.  This displays the same graph as the Short Code.  Options include adjusting the width, height, and background colour of the grpha.

== Installation ==

This section describes how to install the plugin and get it working.

* Automatic Install *

If your WordPress installation has appropriate permissions, this plugin can be installed directly through the "Add New Plugin" feature in the WordPress admin dashboard.

* Manual Install *

If your WordPress installation does not allow you to downlaad and install plugins directly:

1. Download `net_worth_calculator.zip` from http://wordpress.org/extend/plugins/net-worth-calculator/
1. Unzip `net_worth_calculator.zip` into your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Can I enter an asset or liability which is included in my net worth, but not displayed? =

Yes, you can mark any asset or liability value as private if you do not want it to displayed.

= Can I include the same asset or liability more than once in a given month's data? =

No: asset or liability labels must be unique for a given month.

= Can I display a net worth chart for a specific month? =

Yes, you can specify a specific month, eg [networth month="2009-09"]

= Can I include a net worth chart which always shows my latest net worth? =

Yes, this is the default behaviour if you add a graph or data table without including a "month" selection.

= Where does the plugin store it's data? =

All data is stored within the database for your WordPress installation.


== Screenshots ==

1. Plugin backend where you enter your assets and liabilities
2. Customise the appearance of the graph and table of your net worth
3. Graph display in a wordpress post or page
4. Insert the table of your net worth into a post or page


== Changelog ==

= 1.5.3 =

2010-04-15

- Fixes for AJAX editing

= 1.5.2 = 

2010-04-14

- Updated screenshots

= 1.5.1 =

2010-04-12

- Update SWFObject to v2.2

= 1.5 =

2010-04-09

- Plugin renamed to "Net Worth Calculator" for public release

= 1.4 =

2010-03-18

- Autocomplete for assets/liability labels
- Improved editing support using keyboard navigation

= 1.3 =

2010-03-10

- Add dynamic graph examples onto the data editing and appearance tabs
- Add accordion control for preference setting

= 1.2 =

2010-03-03

- Add short code support for graphs and additional parameters
- Add AJAX editing, colour pickers, and drop downs for appearance options

= 1.1 =

2010-02-18

- Improved inline data editing with implicit save/add
- Added support for Private data
- Basic appearance editing

= 1.0 =

2010-02-11

- Initial release with baseline features: data entry, short code for data table, graph widget


== Upgrade Notice ==

= 1.5.3 =
* Includes some essential AJAX bug fixes that were missing from the tagged release.


== Credits ==

* Plugin developed in collaboration with the marketing guns at [Hive Empire](http://www.hiveempire.com/) and the coding ninjas at [Grox](http://www.grox.com.au/).

* Grid editing based on [jqGrid](http://www.trirand.com/blog/)

* Editing UI (Colour Picker, Accordion, ...) made shiny using [jQueryUI](http://jqueryui.com/)

* Autocomplete thanks to [jQuery plugin: Autocomplete](http://bassistance.de/jquery-plugins/jquery-plugin-autocomplete/)

* Flash detection/embedding using [SWFObject](http://code.google.com/p/swfobject/)
