A simple scrum board that helps you, as a Product Owner, to dealing with your stories, sprints and your team members. 

Features:

1) All members work as a team to manipulate the list under "Manage". 
2) Each member can see something what´s really important.
3) WordPress Roles and Capabilities are in sinc with Scrum players:
3.1) "subscriber" = Team Member
3.2) "contributor" = Team Member
3.3) "author" = Team Member
3.4) "editor" = Scrum Master
3.5) "administrator" = Product Owner
4) You can insert entryes as Stories, Epics, Themes, Tasks or Project
5) Tabagile Scrum Board permits you to maintain the relationships between the entryes. That is, an Epic can be a parent category for a lot of stories, for example.
6) Set the stories as "Not-ready" or "Ready" and submit them to sprint. Trac it all. 

Tabagile Scrum Board is a variant derived from Abstract Dimension's Todo List Plugin. - Version: 0.1 (alpha)




Campos da tabela do plugin ScrumBoard   		


1) id bigint(20) NOT NULL auto_increment,
   identificador numérico da entrada ( story, epic, theme )
  		
2) idParent int(11) default NULL,
   identificador numérico da story/epic/theme pai, caso existir		

3) sprintNumber int(11) default NULL,
   número do sprint em que a story será desenvolvida		

4) points int(11) default NULL,
   pontos estimados para a estória		

5) author bigint(20) NOT NULL default '0',
   criador da tarefa no sistema		

6) att bigint(4) NOT NULL default '0',
   atual responsável pela tarefa	

7) targetActors bigint(20) NOT NULL default '0',
   atores que vão se beneficiar da tarefa	

8) tasktag bigint(4) NOT NULL default '0',
   Indica se a entrada é uma story, um epic ou um theme	
   0 = story
   1 = epic
   2 = theme	

9) status tinyint(1) NOT NULL default '0',
   Indica se story está "progress/ready" em fase de backlog ou "progress/done", em fase de sprint    switch ($status)
  {
    case OTD_NOTREADY:
      $where = ' WHERE status = 0 ';
      break;
    case OTD_READY:
      $where = ' WHERE status = 1 ';
      break;
    case OTD_INCOMING:
      $where = ' WHERE status = 2 ';
      break;
    case OTD_DONE:
      $where = ' WHERE status = 3 ';
      break;
    case OTD_ALL:
    default:
      $where = '';
      break;
  }  

		
10) priority tinyint(1) NOT NULL default '0',
   0 = important
   1 = normal
   2 = low
  		
11) todotext text NOT NULL,
  		


created_at datetime NOT NULL default '0000-00-00 00:00:00',
  		


starts_in datetime NOT NULL default '0000-00-00 00:00:00',
  		


ended_in datetime NOT NULL default '0000-00-00 00:00:00',



Roles and Capabilities

	case 0:$user_role="subscriber";
	break;
	case 1:$user_role="contributor";
	break;
	case 2: case 3: case 4: $user_role="author";
	break;
	case 5: case 6: case 7: $user_role="editor";
	break;
	case 8: case 9: case 10: $user_role="administrator";




