<?php

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/**
 * Site Front Page
 *
 * Note: You can overwrite front-page.php as well as any other Template in Child Theme.
 * Create the same file (name) include in /responsive-child-theme/ and you're all set to go!
 * @see            http://codex.wordpress.org/Child_Themes and
 *                 http://themeid.com/forum/topic/505/child-theme-example/
 *
 * @file           front-page.php
 * @package        Soul 
 * @author         Fred Chevalier - Workingdesign 
 * @version        Release: 1.0
 * @since          available since Release 1.0
 */

/**
 * Globalize Theme Options
 */
$responsive_options = responsive_get_options();
/**
 * If front page is set to display the
 * blog posts index, include home.php;
 * otherwise, display static front page
 * content
 */
if ( 'posts' == get_option( 'show_on_front' ) && $responsive_options['front_page'] != 1 ) {
	get_template_part( 'home' );
} elseif ( 'page' == get_option( 'show_on_front' ) && $responsive_options['front_page'] != 1 ) {
	$template = get_post_meta( get_option( 'page_on_front' ), '_wp_page_template', true );
	$template = ( $template == 'default' ) ? 'index.php' : $template;
	locate_template( $template, true );
} else { 

	get_header();
	
	//test for first install no database
	$db = get_option( 'responsive_theme_options' );
    //test if all options are empty so we can display default text if they are
    $empty = ( empty( $responsive_options['home_headline'] ) && empty( $responsive_options['home_subheadline'] ) && empty( $responsive_options['home_content_area'] ) ) ? false : true;
	?>

	<!-- slider -->
	<div class="flexslider">

		<ul class="slides">
		<?php 
			$loop = new WP_Query(array('post_type' => 'slides', 'posts_per_page' => -1, 'order'=> 'ASC')); 
			
			while ( $loop->have_posts() ) : $loop->the_post(); ?>

				<li>
					<?php $url = get_post_meta($post->ID, "url", true);
					if($url!='') { 
						echo '<a href="'.$url.'">';
						echo the_post_thumbnail('full');
						echo '</a>';
					} else {
						echo the_post_thumbnail('full');
					} ?>
							
				</li>

				<?php endwhile; ?>
					
				<?php wp_reset_query(); ?>
			</ul>

	</div>

	<div id="featured" class="grid col-940">
	
		<div id="featured-intro" class="grid col-540">
			<div class="featured-wrapper">
			<?php
				$feature = new WP_Query();
                $feature->query(array( 'page_id' => 33 ));
                while ( $feature->have_posts()) : $feature->the_post();?>
                
                <div class="featured-content">
                	<?php the_content();?>
                </div>
                <?php endwhile;

            wp_reset_query(); ?>
			</div><!-- end of featured-wrapper -->
			
		</div><!-- end of .col-540 -->

		<div id="featured-events" class="grid col-380 fit">
			<div class="featured-wrapper">

				<h3>Upcoming Events</h3>
		  
				<?php
				$args = array( 
					'post_type' => 'shows',
		        	'orderby' => 'show_date',
		        	'meta_query' => array(
		            	array(
		               		'key' => 'show_date',
		                	'value' => current_time( 'timestamp' ), //grc 11-2014
		                	//'value' => current_time( 'mysql' ), // reverted 03/2015 grc
		                	'type' => 'NUMERIC',
		                	'compare' => '>='
		            ),
		        ),
		        	'orderby' => 'meta_value_num',
	        		'order' => 'ASC',
	        		//'posts_per_page' => 2, //grc 11-2014
	        		'posts_per_page' => 1,
		        );

		        //query reset at the bottom

				 $loop = new WP_Query( $args );
				   while ( $loop->have_posts() ) : $loop->the_post();?>
				    
				    <div class="information-event">
				    	
				    	<div class="thumbnail-event"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_post_thumbnail('thumbnail'); ?></a>
				    	</div>

				    	<h4>
				    		<a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a>
				    		<br />by <?php echo get_post_meta($post->ID, 'show_author', true) ?>
				    	</h4>

				    	<p>
					    	<?php 
					    		$unixtime = get_post_meta($post->ID, 'show_date', true);				    		
					    		echo get_post_meta($post->ID, 'show_start_date', true);
					    		echo " to ";
					    		echo date("F d, Y", $unixtime);
					    	?>
					    	<br />
					    	<?php echo get_post_meta($post->ID, 'show_venue', true); ?>
					    	<br />
					    	<a href="<?php get_post_meta($post->ID, 'show_url', true); ?>" target="_blank">
					    		<?php echo get_post_meta($post->ID, 'show_url', true); ?>
					    	</a>
					    	<br />
					    	<br>
					    	<?php echo get_post_meta($post->ID, 'show_tel', true); ?>
					    	<br />
					    	<?php echo get_post_meta($post->ID, 'show_price', true); ?>
					    	<br />
					    	<?php echo get_post_meta($post->ID, 'show_featuring', true); ?>
				    	</p>

					</div>

					<!-- <div class="thumbnail-event"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_post_thumbnail(//'thumbnail'); ?></a></div> -->

				    <div class="clear"></div>


				<?php endwhile; 
				wp_reset_query(); ?>
			  
			</div><!-- end of featured-wrapper -->						
		</div><!-- end of #featured-events --> 

		<div class="clear"></div>
	
	</div><!-- end of #featured -->
               
	<?php 
	get_sidebar('home');
	get_footer(); 
}
?>