<?php
/*
Plugin Name: Enhanced Author Widget
Description: Display the "Biographical Info" and Gravatar of any author's profile in your blog's sidebar.
Version: 0.1
Author: Boston Dell-Vandenberg
Author URI: http://bostondv.com/
*/

class enhanced_author_widget extends WP_Widget {

	function enhanced_author_widget() {
		$widget_ops = array('classname' => 'widget-author-bio', 'description' => "Display an author's biographical info and optional Gravatar." );
		$this->WP_Widget('author_bio', 'Enhanced Author', $widget_ops);
	}
	
	function widget($args, $instance) {
		extract( $args );	
		
		if ($instance['author']) {
			global $wpdb;
			$bio = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM wp_usermeta WHERE meta_key = 'description' AND wp_usermeta.user_id = " . $instance['author'] . ";"));
			$author = $wpdb->get_var($wpdb->prepare("SELECT user_email FROM wp_users WHERE ID = " . $instance['author'] . ";"));
			$display_name = $wpdb->get_var($wpdb->prepare("SELECT display_name FROM wp_users WHERE ID = " . $instance['author'] . ";"));
			if (empty($instance['link'])) {
				$instance['link'] = $wpdb->get_var($wpdb->prepare("SELECT user_url FROM wp_users WHERE ID = " . $instance['author'] . ";"));
			}
		} else {
			global $post;
			$author = $post->post_author;
			$bio = get_the_author_meta('description', $author);
			$display_name = get_the_author_meta('display_name', $author);
			if (empty($instance['link'])) {
				$instance['link'] = get_the_author_meta('user_url', $author);
			}
		}

		if ($instance['title']) {
			$title = esc_attr($instance['title']);
		} else {
			$title = $display_name;
		}

		echo $before_widget;
		echo $before_title;
		if ($instance['link']) {
			echo '<a href="' . $instance['link'] . '">' . $title . '</a>';
		} else {
			echo $title;
		}
		echo $after_title;
		
		if ( 'display' == $instance['gravatar'] ) {
			$gravatar_image = get_avatar( $author, $size = $instance['gravatar_size'] );
			$output = '<div class="author-grav align-' . $instance['gravatar_align'] . '">' . $gravatar_image . '</div>';
			if ( 'yes' != $instance['only_gravatar'] ) {
				$output .= '<p class="user-bio author-bio">' . $bio . '</p>';
			}
		} else {
			$output .= '<p class="user-bio author-bio">' . $bio . '</p>';
		}

		if ($instance['link']) {
			$output .= '<p class="read-more"><a href="' . $instance['link'] . '">' . __('Read More &rarr;') . '</a></p>';
		}
		
		echo $output;

		echo "\n" . $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['link'] = strip_tags($new_instance['link']);
		$instance['author'] = $new_instance['author'];
		$instance['gravatar'] = $new_instance['gravatar'];
		$instance['gravatar_size'] = $new_instance['gravatar_size'];
		$instance['gravatar_align'] = $new_instance['gravatar_align'];
		$instance['only_gravatar'] = $new_instance['only_gravatar'];
		
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array('title'=>'', 'author'=>'', 'gravatar'=>'', 'gravatar_size'=>'96', 'gravatar_align'=>'none') );
		
		$title = esc_attr($instance['title']);
		$link = esc_attr($instance['link']);
		$author = $instance['author'];
		$gravatar = $instance['gravatar'];
		$gravatar_size = $instance['gravatar_size'];
		$gravatar_align = $instance['gravatar_align'];
		$only_gravatar = $instance['only_gravatar'];
			
			echo '<p><label for="<' . $this->get_field_id('title') . '">' . __('Custom Title:') . '
			<input class="widefat" id="<' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" />
			</label></p>';
			echo '<p><label for="<' . $this->get_field_id('link') . '">' . __('Custom Link:') . '
			<input class="widefat" id="<' . $this->get_field_id('link') . '" name="' . $this->get_field_name('link') . '" type="text" value="' . $link . '" />
			</label></p>';
			echo '<p><label for="<' . $this->get_field_id('author') . '">' . __('Custom Author:') . '
			<select id="<' . $this->get_field_id('author') . '" name="' . $this->get_field_name('author') . '" class="widefat">';

			global $wpdb;
			//This is the query to use if the blog ID is 1 or if the multi-site function is not enabled or available.
			$simple_authors_query = $wpdb->get_results($wpdb->prepare("SELECT distinct ID,display_name FROM wp_users,wp_usermeta WHERE wp_users.ID=wp_usermeta.user_id AND wp_usermeta.meta_key='wp_user_level' AND wp_usermeta.meta_value != 0;"));
			
			//If the multi-site function is enabled.
			if (function_exists('is_multisite') && is_multisite()) {
			
				//Get the current blog's ID.
				$this_blog = $wpdb->blogid;
				
				if (1 == $this_blog) {
					$authors = $simple_authors_query;
				}
				//If the blog's ID is not 1, we need a more customized query which uses the blog's ID in obtaining the authors to use for the drop-down.
				else {
					$authors = $wpdb->get_results($wpdb->prepare("SELECT distinct ID,display_name FROM wp_users,wp_usermeta WHERE wp_users.ID=wp_usermeta.user_id AND wp_usermeta.meta_key='wp_" . $this_blog . "_user_level' AND wp_usermeta.meta_value != 0;"));
				}
			}
			// If the multi-site function is not enabled or available, there is no need to do that extra stuff above; just run the simple query.
			else {
				$authors = $simple_authors_query;
			}
			echo '<option value="">-- Post Author --</option>';
			foreach ( $authors as $author ){
				echo '<option value="'. $author->ID .'"';
				if($author->ID == $instance['author']){
					echo ' selected ';
				}
				echo '>'. $author->display_name . '</option>'."\n";
			}
			echo '</select></label></p>';
		?>

			<p>
				<label for="<?php echo $this->get_field_id('gravatar'); ?>">
					<input id="<?php echo $this->get_field_id('gravatar'); ?>" name="<?php echo $this->get_field_name('gravatar'); ?>" type="checkbox" value="display" <?php if($gravatar == "display") echo 'CHECKED'; ?> onchange="jQuery('div#extra-options').slideToggle()" />
					<?php echo __('Display the <a href="http://gravatar.com/" title="Gravatar">Gravatar</a>'); ?>
				</label>
			</p>

			<style type="text/css">
				#extra-options {
					background:#eee;
					border:1px solid #ddd;
					padding:5px;
					-moz-border-radius: 5px;
					-webkit-border-radius: 5px;
				}
			</style>

			<div id="extra-options" <?php if ( 'display' != $gravatar ) echo 'style="display: none;"'; ?>>

				<p>
					<label for="<?php echo $this->get_field_id('only_gravatar'); ?>">
						<input id="<?php echo $this->get_field_id('only_gravatar'); ?>" name="<?php echo $this->get_field_name('only_gravatar'); ?>" type="checkbox" value="yes" <?php if($only_gravatar == "yes") echo 'CHECKED'; ?> />
						<?php echo __('Only the Gravatar?'); ?>
					</label>
				</p>

				<p>
					<label for="<?php echo $this->get_field_id('gravatar_size'); ?>"><?php echo __('Size:'); ?>
						<select id="<?php echo $this->get_field_id('gravatar_size'); ?>" name="<?php echo $this->get_field_name('gravatar_size'); ?>">'
						<?php
							$sizes = array('64' => 'Small - 64px', '96' => 'Medium - 96px', '128' => 'Large - 128px', '256' => 'Extra Large - 256px');
							foreach ( $sizes as $size => $size_display ) {
								echo  '<option value="' . $size . '" ';
								if ( $size == $gravatar_size ) echo 'selected ';
								echo '>' . __($size_display) . '</option>' . "\n";
							}
						?>
						</select>
					</label>
				</p>

				<p>
					<label for="<?php echo $this->get_field_id('gravatar_align'); ?>"><?php echo  __('Alignment:'); ?>
						<select id="<?php echo $this->get_field_id('gravatar_align'); ?>" name="<?php echo $this->get_field_name('gravatar_align'); ?>">
						<?php
							$alignments = array('None', 'Left', 'Center', 'Right');
							foreach ( $alignments as $alignment ) {
								echo  '<option value="' . strtolower($alignment) . '" ';
								if ( strtolower($alignment) == $gravatar_align ) echo 'selected ';
								echo '>' . __($alignment) . '</option>' . "\n";
							}
						?>
						</select>
					</label>
				</p>
			</div>
			<br>
		<?php	
	}
	
}

function enhanced_author_init() {
	register_widget('enhanced_author_widget');
}

add_action('widgets_init', 'enhanced_author_init');

function enhanced_author_widget_style() {
	$myStyleUrl = plugins_url('enhanced-author-widget.css', __FILE__); 
	$myStyleFile = WP_PLUGIN_DIR . '/enhanced-author-widget/enhanced-author-widget.css';
	if ( file_exists($myStyleFile) ) {
		wp_register_style('enhanced-author-widget', $myStyleUrl);
		wp_enqueue_style('enhanced-author-widget');
	}
}

add_action('wp_print_styles', 'enhanced_author_widget_style');