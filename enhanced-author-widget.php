<?php
/*
Plugin Name: Enhanced Author Widget
Plugin URI: http://pomelodesign.com/enhanced-author-widget
Description: Display the biographical info, gravatar, and link of any authors profile in your blogs sidebar.
Version: 1.1
Author: Pomelo Design Inc.
Author URI: http://pomelodesign.com/
License: GPL2

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class enhanced_author_widget extends WP_Widget {

	function enhanced_author_widget() {
		$widget_ops = array('classname' => 'widget-author-bio', 'description' => "Display the biographical info, gravatar, and link of any authors profile." );
		$this->WP_Widget('author_bio', 'Enhanced Author', $widget_ops);
	}
	
	function widget($args, $instance) {
		extract( $args );	
		
		if ( $instance['author'] != 0 ) {
			$author_id = $instance['author'];
		} else {
			global $post;
			$author_id = $post->post_author;
		}

		$author = get_userdata( $author_id );
		$author_bio = $author->user_description;
		$author_name = $author->display_name;
		$author_link = get_author_posts_url( $author_id );

		if ( $instance['link'] ) {
			$link = esc_attr( $instance['link'] );
		} else {
			$link = $author_link;
		}

		if ( $instance['title'] ) {
			$title = esc_attr( $instance['title'] );
		} else {
			$title = $author_name;
		}

		echo $before_widget;
		echo $before_title;
		echo '<a href="' . $link . '">' . $title . '</a>';
		echo $after_title;
		
		if ( 'display' == $instance['gravatar'] ) {
			$gravatar_image = get_avatar( $author_id, $size = $instance['gravatar_size'] );
			$output = '<div class="author-grav align-' . $instance['gravatar_align'] . '">' . $gravatar_image . '</div>';
			if ( 'yes' != $instance['only_gravatar'] ) {
				$output .= '<p class="user-bio author-bio">' . $author_bio . '</p>';
			}
		} else {
			$output .= '<p class="user-bio author-bio">' . $author_bio . '</p>';
		}

		$output .= '<p class="read-more"><a href="' . $link . '">' . __('Read More &rarr;') . '</a></p>';
		
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

		?>
			
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>'"><?php _e('Custom Title:'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('link'); ?>"><?php _e('Custom URL:'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('link'); ?>" name="<?php echo $this->get_field_name('link'); ?>" type="text" value="<?php echo $link; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('author'); ?>"><?php _e('Author:'); ?></label>
				<?php 
				$args = array(
					'show_option_all' => 'Current Post Author',
					'id' => $this->get_field_id('author'),
					'name' => $this->get_field_name('author'),
					'selected' => $author
				);
				wp_dropdown_users( $args ); 
				?>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('gravatar'); ?>">
					<input id="<?php echo $this->get_field_id('gravatar'); ?>" name="<?php echo $this->get_field_name('gravatar'); ?>" type="checkbox" value="display" <?php if($gravatar == "display") echo 'CHECKED'; ?> onchange="jQuery('div#extra-options').slideToggle('fast')" />
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
			<p class="credits"><small>Developed by <a href="http://pomelodesign.com">Pomelo Design</a></small></p>
		<?php	
	}
	
}

function enhanced_author_init() {
	register_widget('enhanced_author_widget');
}

add_action('widgets_init', 'enhanced_author_init');

function enhanced_author_widget_style() {
	$myStyleUrl = plugins_url('/enhanced-author-widget/enhanced-author-widget.css'); 
	$myStyleFile = WP_PLUGIN_DIR . '/enhanced-author-widget/enhanced-author-widget.css';
	if ( file_exists($myStyleFile) ) {
		wp_register_style('enhanced-author-widget', $myStyleUrl);
		wp_enqueue_style('enhanced-author-widget');
	}
}

add_action('wp_print_styles', 'enhanced_author_widget_style');