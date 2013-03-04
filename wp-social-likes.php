<?php
/*
Plugin Name: WP Social Likes
Description: Wordpress plugin for Social Likes (http://sapegin.me/projects/social-likes)
Version: 0.2
Author: TS Soft

Copyright 2013 TS Soft (email: dev@ts-soft.ru )

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

*/

class wpsociallikes
{
	function wpsociallikes() 
	{
		add_action('wp_head', array(&$this, 'header_content'));
		add_action('admin_menu', array(&$this, 'wpsociallikes_meta_box'));
		add_action('save_post', array(&$this, 'save_post_meta'));
		add_filter('the_content', array(&$this, 'add_social_likes'));
	}
	
	function header_content() {
		?>
			<link href="<?php echo plugin_dir_url(__FILE__) ?>css/social-likes.css" rel="stylesheet">
			<script src="<?php echo plugin_dir_url(__FILE__) ?>js/social-likes.min.js"></script>
		<?php
	}
		
	function wpsociallikes_meta_box() {
		add_meta_box('wpsociallikes', 'Social Likes', array(&$this, 'wpsociallikes_meta'), 'post', 'normal');
		add_meta_box('wpsociallikes', 'Social Likes', array(&$this, 'wpsociallikes_meta'), 'page', 'normal');
	}
	
	function wpsociallikes_meta($post) {
		$checked = get_post_meta($post->ID, 'sociallikes', true);
		?>
			<input type="checkbox" name="wpsociallikes" <?php if ($checked == true) echo 'checked' ?>/>
			<label for="wpsociallikes">Add social buttons to the post</label>
		<?php
	}
	
	function save_post_meta($post_id) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		{
			return;
		}

		if ('page' == $_POST['post_type']) 
		{
			if (!current_user_can('edit_page', $post_id))
			{
				return;
			}
		}
		else
		{
			if (!current_user_can('edit_post', $post_id))
			{
				return;
			}
		}

		update_post_meta($post_id, 'sociallikes', isset($_POST['wpsociallikes']));
	}
	
	function add_social_likes($content='') {
		global $post;
		if ((is_page() || is_single()) && get_post_meta($post->ID, 'sociallikes', true))
		{
			$buttons = '<ul class="social-likes"><li class="vkontakte" title="Поделиться ссылкой во Вконтакте">Вконтакте</li><li class="facebook" title="Поделиться ссылкой на Фейсбуке">Facebook</li><li class="twitter" title="Поделиться ссылкой в Твиттере">Twitter</li><li class="plusone" title="Поделиться ссылкой в Гугл-плюсе">Google+</li></ul>';
			$content .= $buttons;
		}
		return $content;
	}
}

$wpsociallikes = new wpsociallikes();	

?>