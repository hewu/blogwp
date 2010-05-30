<?php

class eventifywidget extends WP_Widget{


	function eventifywidget()
	{
		/* Widget settings. */

		$widget_ops = array( 'classname' => 'eventifywidget', 'description' => 'A widget to display events schduled using Eventify.' );
		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'eventify-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'eventify-widget', 'Eventify Events Listing', $widget_ops, $control_ops );

	} // construtor for the widget class
	
	
	
 		function widget( $args, $instance ) {
		extract( $args );
		
		
		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		

		/*Get the data from the database*/
		global $wpdb;
		$table_name = $wpdb->prefix."em_main";
		$qry= "select * from ".$table_name." order by em_timestamp ASC" ;		
			
			//echo $qry;
		 	$results = $wpdb->get_results($qry);
		 	//$results = $wpdb->query($qry);
		date("m-d-Y");

		$date_today = getdate();
		$date_today = $date_today['mon']."/".$date_today['mday']."/".$date_today['year'];
		$date_today = strtotime($date_today);
		$popupcssurl = get_bloginfo('wpurl').'/wp-content/plugins/eventify/css/popup_widget.css';		

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
			//print_r($results);
		/* Display the list of events today/tomorrow/etc */
		$i=1;?>
				
		<link type="text/css" rel="stylesheet" href="<?php echo $popupcssurl; ?>" />
<div id="mainwpopup">
	<div id="wpemmainstuff">
		<div id="wpemtitle"></div>
		
		<div id="wpemtext"></div>
	</div>
</div>
					<?php echo "<ul>";
					
		if ( 'Display events occuring in the next "n" days.' == $instance['emdisp'] )
		{
			foreach($results as $row){
				if($instance['di_format']=="dd/mm/yyyy")
				{
					$rowemdate = date("d/m/Y",strtotime($row->em_date));
				}
				elseif($instance['di_format']=="mm/dd/yyyy")
				{
						$rowemdate = date("m/d/Y",strtotime($row->em_date));
				}
				elseif($instance['di_format']=="mm.dd.yyyy")
				{
						$rowemdate = date("m.d.Y",strtotime($row->em_date));
				}
				else
				{
						$rowemdate = date("d.m.Y",strtotime($row->em_date));
				}
				if(strtotime($row->em_date)>=$date_today && strtotime($row->em_date)<=strtotime("+".$instance['ndays']." day"))
				{  
					if($row->em_savetype>0)
					{
						
						
						echo "<li>$rowemdate - <a href=\"".get_permalink($row->em_savetype)."\">".$row->em_title."</a>  </li>";
					}
					else
					{
						echo "<li>$rowemdate - <a href=\"javascript:showMe('<strong>Event Desc:</strong> ". str_ireplace("'","&rsquo;",$row->em_desc)."<br/><Br/><strong>Date:</strong> $rowemdate;<br/><br/><strong>Event Time:</strong> $row->em_time<Br/><Br/><strong>Event Venue:</strong> $row->em_venue<Br/><Br/><strong>Event Time Zone:</strong> $row->em_timezone<Br/><Br/>','<span class=\'titlespop\'>$row->em_title</span> ');\">".$row->em_title."</a><a href=\"#dialog\" name=\"modal\">something</a>  </li>";
					}
					
					/*echo "<strong>Time:</strong> ".$row->em_time."<br/>";
					echo "<strong>Desc:</strong> ".$row->em_desc."<br/>";
					echo "<strong>Date: </strong>".$row->em_date."<br/>";
					$i++;*/
				}
			
			}
		} //displaying next "n" events;
		
		if ( 'Display next "n" events.' == $instance['emdisp'] )
		{
			$countloop = $instance['ndays'];
			
			if($countloop>count($results))
			{
				$countloop=count($results);
			}
			
			$arr_i=0;
			//echo count($results);
			//print_r($results[$arr_i]->em_desc);
			while($countloop>0)
			{
				if(strtotime($results[$arr_i]->em_date)>=$date_today)
				{
					if($instance['di_format']=="dd/mm/yyyy")
					{
						$rowemdate = date("d/m/Y",strtotime($results[$arr_i]->em_date));
					}
					elseif($instance['di_format']=="mm/dd/yyyy")
					{
						$rowemdate = date("m/d/Y",strtotime($results[$arr_i]->em_date));
					}
					elseif($instance['di_format']=="mm.dd.yyyy")
					{
						$rowemdate = date("m.d.Y",strtotime($results[$arr_i]->em_date));
					}
					else
					{
						$rowemdate = date("d.m.Y",strtotime($results[$arr_i]->em_date));
					}
					if($results[$arr_i]->em_savetype>0)
					{
						print_r( "<li> ".$rowemdate." - <a href=\"".get_permalink($results[$arr_i]->em_savetype)."\">".$results[$arr_i]->em_title."</a> </li>");
					}
					else
					{
						print_r( "<li> ".$rowemdate." - <a href=\"".$results[$arr_i]->em_id."\" rel=\"#dialog\" name=\"modal\">".$results[$arr_i]->em_title."</a> </li>");
						
					}
					
				}
				$countloop--;
				$arr_i++;
			}
		}//displaying  events in the next "n" days;
		
		echo "</ul>";
		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		/*Some error handling bits*/
		if(!is_numeric(strip_tags($new_instance['ndays'])))
		{
			$new_instance['ndays']="7";
		}
		if(!is_numeric(strip_tags($new_instance['nevents'])))
		{ 
			$new_instance['nevents']="5";
		}
		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['ndays'] = strip_tags($new_instance['ndays']);
		$instance['emdisp'] = $new_instance['emdisp'];
		$instance['di_format'] = $new_instance['di_format'];
		//$instance['sex'] = $new_instance['sex'];
		//$instance['show_sex'] = $new_instance['show_sex'];
		
		return $instance;
	}

	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Upcoming Events', 'ndays' => '7', 'emdisp'=>'Display next "n" events.','di_format'=>'mm/dd/yyyy');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'emdisp' ); ?>">How do you want to display the events? :</label>
		<select id="<?php echo $this->get_field_id( 'emdisp' ); ?>" name="<?php echo $this->get_field_name( 'emdisp' ); ?>" class="widefat" style="width:100%;">
				<option <?php if ( 'Display next "n" events.' == $instance['emdisp'] ) echo 'selected="selected"'; ?> >Display next "n" events.</option>
				<option <?php if ( 'Display events occuring in the next "n" days.' == $instance['emdisp'] ) echo 'selected="selected"'; ?>>Display events occuring in the next "n" days.</option>
				<?php// echo $instance['format']; ?>
		</select>
		
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'di_format' ); ?>">Select a date format :</label>
		<select id="<?php echo $this->get_field_id( 'di_format' ); ?>" name="<?php echo $this->get_field_name( 'di_format' ); ?>" class="widefat" style="width:100%;">
				
				<option <?php if ( 'mm/dd/yyyy' == $instance['di_format'] ) echo 'selected="selected"'; ?> >mm/dd/yyyy</option>
				<option <?php if ( 'mm.dd.yyyy' == $instance['di_format'] ) echo 'selected="selected"'; ?> >mm.dd.yyyy</option>
				<option <?php if ( 'dd/mm/yyyy' == $instance['di_format'] ) echo 'selected="selected"'; ?>>dd/mm/yyyy</option>
				<option <?php if ( 'dd.mm.yyyy' == $instance['di_format'] ) echo 'selected="selected"'; ?>>dd.mm.yyyy</option>
				<?php //echo $instance['format']; ?>
		</select>
		
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'ndays' ); ?>"><?php if ( 'Display next "n" events.' == $instance['emdisp'] ) echo 'Number of events to display:'; ?>  <?php if ( 'Display events occuring in the next "n" days.' == $instance['emdisp'] ) echo 'Show events occuring in the next "n" days:'; ?></label>
			<input id="<?php echo $this->get_field_id( 'ndays' ); ?>" name="<?php echo $this->get_field_name( 'ndays' ); ?>" value="<?php echo $instance['ndays']; ?>" style="width:100%;" />
			<small>Please make sure, the value entered above is a numeric value, where "n" = numeric digits (number of +n days from today )</small>
		</p>

		
		
		
		<?php }


} //end of class for eventify_widget 
  ?>
