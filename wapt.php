<?php
/**
 * Plugin Name:  Wordpress Yearwise Monthwise Archives Lists
 * Description: A Wordpress Yearwise Monthwise Archives Lists
 * Version: 2.0.1
 * Author: Position2 WAAS Team
 * Author URI: www.position2.com
 * License: A "Slug" license name e.g. GPL2
 */

/* shortcode section*/

add_shortcode('BLOG-ARCHIVES', [new wapt_Blog_Archives, 'widget']);



class wapt_Blog_Archives extends WP_Widget {

  function __construct() {
		 $widget_ops = array(  'description' => __( 'Yearly Montly Post Archives', 'default' ) );
      parent::__construct(
      	'wapt_blog_archives',
      	 __('Yearly Montly Post Archives', 'default'), 
      	 $widget_ops  );
	} // end of wapt_Blog_Archives
		
	function widget( $args, $instance ) {
		extract( (array)$args );
	    global $wpdb;
	    $before_widget = $before_title = $after_title = $after_widget = null;
		//Our variables from the widget settings
	  if(is_array($instance) && isset($instance['title'])){
			$title = apply_filters('widget_title', $instance['title'] );
	  } else {
	 		$title = " ";
	 	}
		// include style sheet & script file to plugin
		wp_enqueue_script('archive-scripts',plugins_url( 'wapt.js' , __FILE__ ), array('jquery'), false ,true);

		wp_enqueue_style('archive-styles',plugins_url( 'css/style.css' , __FILE__ ), true);
		// end before widget 
		echo $before_widget;
		// Display the widget title 
            echo ' <!-- BLOG ARCHIVES BEGIN -->' ;         
            echo' <div id="BlogArchivesWrapper">
        	<div id="BlogArchivesList">';
	        if ($title):
				echo $before_title . $title . $after_title;
			endif;  
	 
      	    echo '<div class="blog-list-archive">';

		$years = $wpdb->get_col("SELECT DISTINCT YEAR(post_date)
										FROM $wpdb->posts
										 WHERE post_status = 'publish'
										AND post_type = 'post' 
										ORDER BY post_date DESC");
		echo '<ul class="archive-menu">';
		foreach($years as $year) :
		      	echo '<li class="year-archive"><a href="JavaScript:void(0)">'.$year.'</a>';
				     	$months = $wpdb->get_col("SELECT DISTINCT MONTH(post_date)
											          FROM $wpdb->posts 
											          WHERE post_status = 'publish' 
											          AND post_type = 'post'
											          AND YEAR(post_date) = '".$year."' 
											          ORDER BY post_date DESC");
 				echo '<ul style="display:none" class="archive-sub-menu">';
		        foreach($months as $month) :
				    echo '<li class="month-archive"><a href="JavaScript:void(0)">'.date( 'F', mktime(0, 0, 0, $month) ).'</a>';
		                   $sposts = $wpdb->get_col( " SELECT ID
											                FROM $wpdb->posts
											                WHERE MONTH(post_date) = '$month'
										                    AND YEAR(post_date)    =  '$year'
										                    AND `post_status`      = 'publish'
										                    AND `post_type`        = 'post'
		            										ORDER BY post_date DESC " );
		            	echo '<ul style="display:none" class="archive-post-title">';
							foreach( $sposts as $spost ) :
                                 echo '<li><a href="'.get_permalink( $spost ).'">' . get_the_title( $spost ) . '</a></li>';
							endforeach; 
				        echo '</ul>';
				   echo '</li>'; 
				endforeach; 
			    	echo '</ul>
			    </li>';
	 		endforeach; 
   		echo '</ul>';
	 	wp_reset_query();
	   	echo '</div>';          
		echo  $after_widget;
	}  // end of widgets
   
   //Update the widget function begain

	function update( $new_instance, $old_instance ) {
				$instance = $old_instance;
				//Strip tags from title and name to remove HTML 
				$instance['title'] = strip_tags( $new_instance['title'] );
				return $instance;
	}  // end of function update

	function form( $instance ) {
				//Set up some default widget settings
				$instance['profile_style'] = array();
				$defaults = array( 'title' => __('Blog Archives', 'default'));
				$instance = wp_parse_args( (array) $instance, $defaults ); 
				// Widget Title: Text Input.
				?>
			<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'default'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:95%;" />
			</p> 
		<?php
		}  // end on form function  
	} // end of class wapt_Blog_Archives

	add_action( 'widgets_init', 'wapt_Blog_Archives_init');
	function wapt_Blog_Archives_init() {
		register_widget( 'wapt_Blog_Archives' );
	}
