=== Tiny Table Of Contents - TinyTOC ===
Contributors: zlikavac32
Tags: content, table, tiny, tinytoc, toc, bookmark, simple, easy
Donate link: http://amzn.com/w/38P1LI34GI7NV
Requires at least: 2.0.2
Tested up to: 2.9
Stable tag: 0.12.31

Plugin that enables you to create table of contents in your posts and pages. It's very simple to use from your editor.

== Description ==

Plugin that enables you to create table of contents in your posts and pages. You can choose what it will parse, and what
it will not parse. It's very simple to use from your editor
so you do not have to manualy enter tags. Now it's nothing special, but later it will have much more features including
some predefined styles and more styling control.

== Changelog ==

= Ver 0.12.31 (released 2009-12-31) =
-------------------------------------------------------------------
* Fixed bugs reported by [Navjot Singh](http://nspeaks.com/)

= Ver 0.8.30 (released 2009-08-30) =
-------------------------------------------------------------------
* You can now position TOC by inserting `[tinytoc]` in your code

= Ver 0.8.12 (released 2009-08-12) =
-------------------------------------------------------------------
* Fixed custom styling on each level
* Updated regular expression to make it faster

= Ver 0.7.18 (released 2009-07-18) =
-------------------------------------------------------------------
* Fixed some minor bugs
* Added custom styling for each level in TOC list
* Improved speed

= Ver 0.7 (released 2009-06-27) =
-------------------------------------------------------------------
* Fixed problem when parsing home page (<!--more--> tag)
* Fixed some small parsing bugs
* Optimized script
* Fixed creating of TOC problem (nested loops problem)
* Fixed chapter parsing in TOC (now all styling is removed)
* Added "Remove when not used" feature
* Added "TOC on all pages" feature

= Ver 0.3 (released 2009-05-21) =
-------------------------------------------------------------------
* Plugin first version

== Upgrade Notice ==

= Ver 0.12.31 (released 2009-12-31) =
-------------------------------------------------------------------
This version fixes problems with custom position of Table Of Contents. Note that I tested it with 10 different sites and plugin passed. If you still have problems, please report.

== Installation ==

1. Upload folder `tiny-table-of-contents-tinytoc` to your `/wp-content/plugins` directory
2. Activate the plugin through the `Plugins` menu in WordPress

== Frequently Asked Questions ==

= Now do I use this plugin? =
When you go to your post editor you will see drop down menu "TOC Levels". Select you chapter and then chose one of the levels. Or you can manualy wrap your chapter in [tinytoc level="lv"]cont[/tinytoc] tags where `lv` is your level (number) and `cont` is you chapter.

= How to use image as "Back to top" button? =
You have to add `<img />` tag to your text with path to that image as `src` attribute value.

= Can I add text before TOC? =
If you add `[tinytoc]` after that text, yes.

= Can I chose TOC position? =
Yes, by placing `[tinytoc]` in your text.

= How can I remove `Header` and still be able to use `Back to top` feature? =
In order to use `Backt to top` you need to have some tag with some ID. Just add `style="display: none;"` to your `Html before title` field in tag. If you have default example `<h3 id="tyinTOC">` then you would have `<h3 id="tyinTOC" style="display: none;">`. That will remove Title before table of contents and still allow you to use `Back to top` feature.

= How do I know what each of these fields mean? =
Visit [documentation](http://php4every1.com/scripts/tiny-table-of-contents-wordpress-plugin/#Documentation-1).

== Screenshots ==

1. Part of `Plugin summary` page
2. An other part of `Plugin summary` page
3. Part of `Plugin settings` page
4. An other part of `Plugin settings` page
5. Also a part of `Plugin settings` page


== Planned Features ==

* Custom TOC tag (so you dont have to use `[tinytoc lev="lv"]cont[/tinytoc]`)

== Source SVN ==

* svn checkout http://svn.wp-plugins.org/tiny-table-of-contents-tinytoc/trunk/ tiny-table-of-contents-tinytoc

== Support ==

* Twitter: http://www.twitter.com/php4every1
* Facebook: http://www.new.facebook.com/profile.php?id=1296304925&ref=mf
* Plugin home: http://php4every1.com/scripts/tiny-table-of-contents-wordpress-plugin/
* Documentation: http://php4every1.com/scripts/tiny-table-of-contents-wordpress-plugin/#Documentation-1