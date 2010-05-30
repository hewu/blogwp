=== Plugin Name ===
Contributors: maeka
Donate link: http://www.twitter.com/ricardonm/
Tags: scrum, agile, management
Requires at least: 2.0.2
Tested up to: 2.1
Stable tag: 0.1

A simple scrum board that helps you, as a Product Owner, to deal with your stories, sprints and your team members.

== Description ==

Tabagile Scrum Board 0.1 (alpha version), is a simple scrum board that will help you, a Product Owner, to deal with your stories, sprints and your team members.

0.1 alpha features:

1. All members work as a team to manipulate the list under "Manage".
2. Each member can see what's really important.
3. Scrum players are in Sync with WordPress Roles and Capabilities.
4. You can insert entries as Stories, Epics, Themes, Tasks or Project.
5. Tabagile Scrum Board permits you to maintain the relationships between the entryes. That is, an Epic can be a parent category for a lot of stories, for example.
6. Set the stories as "Not-ready" or "Ready" and submit them to sprint. Trac it all.

Next alpha release features:

1. Entries pagination and a "sort by" the table title names.
2. View the entries according by the relationship beetween them(parents and children nodes). 
3. Request for approval using e-mail on changing the entry status.
4. Visualize the graphs for "release burn-down", "sprint burn-down".
5. Attach files and documents to the entries.
6. Setup the title table names that will be shown in the product backlog list   

Tabagile Scrum Board is a variant derived from Abstract Dimension's Todo List Plugin.

== Installation ==

1. Upload `tabagile.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Access the 'Tabagile Scrum Board' button on menu 'tools' in Wordpress 

== Frequently Asked Questions ==

= I have activated the plugin with success but when i add a new entry, nothing happens. = 

Somethimes, depending on your database security options, the Tabagile Plugin can not create a new table in your wordpress database. In this case, you can create it manualy. Just run the following sql statement to create the Tabagile table:

CREATE TABLE wp_tabagile (
  		id bigint(20) NOT NULL auto_increment,
  		idParent int(11) default NULL,
  		sprintNumber int(11) default NULL,
  		points int(11) default NULL,
  		author bigint(20) NOT NULL default '0',
  		att bigint(4) NOT NULL default '0',
  		targetActors bigint(20) NOT NULL default '0',
  		tasktag bigint(4) NOT NULL default '0',
  		status tinyint(1) NOT NULL default '0',
  		priority tinyint(1) NOT NULL default '0',
  		todotext text NOT NULL,
  		created_at datetime NOT NULL default '0000-00-00 00:00:00',
  		starts_in datetime NOT NULL default '0000-00-00 00:00:00',
  		ended_in datetime NOT NULL default '0000-00-00 00:00:00',
  		UNIQUE KEY id (id)
        ); 


== Changelog ==

0.1. On user sign-in, all the Entries where the current user are the author or the scrum master, now will be showed on the dashboard.

== Upgrade Notice ==

== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.png
4. screenshot-4.png
5. screenshot-5.png

== Roles and Capabilities ==

* "subscriber" / role:0 = Client (that user can sugest stories directly in the product backlog, trough a public interface, and can follow entries created by himself)
* "contributor" / role:1 = Team Member(that user can sugest stories directly in the product backlog, trough a public interface, and can follow entries created by himself)
* "author" / role:2,3,4  = Team Member (that user can see your tasks in product backlog)
* "editor" / role:5,6,7 = Scrum Master (can dealing with tasks and team members)
* "administrator" / role:8,9,10 = Product Owner (can dealing with epics, stories, themes, projects and grant access for all team members)

== Table Structure ==

1. id bigint(20) NOT NULL auto_increment: the entry id ( story, epic, theme )	
2. idParent int(11) default NULL: parent id number, if it exists		
3. sprintNumber int(11) default NULL: sprint number that will support the storie		
4. points int(11) default NULL: story points		
5. author bigint(20) NOT NULL default '0': story account id		
6. att bigint(4) NOT NULL default '0': attendant id (scrum-master, p.o, team-member)	
7. targetActors bigint(20) NOT NULL default '0': the target profile that will be 	
8. tasktag bigint(4) NOT NULL default '0': it will show you if the entry is a '0 = story', '1 = 
epic', '2 = theme', '3 = task', '4 = project'
9. status tinyint(1) NOT NULL default '0': this field is abble to show you if your story is  '0 = notset', '1 = notready' '2 = ready', '3 = progress', '4 = has been done'		
10. priority tinyint(1) NOT NULL default '0': '0 = important', '1 = normal', '2 = low'  		
11. todotext text NOT NULL: The full entry description
12. created_at datetime NOT NULL default '0000-00-00 00:00:00': The date that the entry has been created 	
13. starts_in datetime NOT NULL default '0000-00-00 00:00:00': The date that the entry was submited to the sprint		
14. ended_in datetime NOT NULL default '0000-00-00 00:00:00': The date that the entry has been doned





