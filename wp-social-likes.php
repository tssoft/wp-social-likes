<?php
/*
Plugin Name: WP Social Likes
Description: Wordpress plugin for Social Likes (http://sapegin.me/projects/social-likes)
Version: 0.1
*/

class wpsociallikes
{
	function wpsociallikes() 
	{
		add_action( 'wp_head', array(&$this, 'header_content') );
		
		add_filter( 'mce_buttons', array(&$this, 'mce_buttons') );
		add_filter( 'mce_external_plugins', array(&$this, 'mce_external_plugins') );
	}
	
	function mce_external_plugins($plugin_array) 
	{
		$plugin_array['wpsociallikes'] = plugins_url ('wp-social-likes/js/button_plugin.js');
		return $plugin_array;
	}
	
	function mce_buttons($buttons)
	{
		array_push($buttons, "social");
  		return $buttons;
	}

	function header_content() {
		?>
			<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>css/social-likes.css">
			<script src="<?php echo plugin_dir_url(__FILE__) ?>js/social-likes.min.js"></script>
		<?php
	}
}

$wpsociallikes = new wpsociallikes();	

?>