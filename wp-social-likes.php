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
		add_option('vk_btn', true);		
		add_option('facebook_btn', true);
		add_option('twitter_btn', true);
		add_option('google_btn', true);
		add_option('pinterest_btn', false);
		add_option('lj_btn', false);
		add_option('odn_btn', false);
		add_option('mm_btn', false);
		add_option('pos1', 'vk_btn');
		add_option('pos2', 'facebook_btn');
		add_option('pos3', 'twitter_btn');
		add_option('pos4', 'google_btn');
		add_option('pos5', 'pinterest_btn');
		add_option('pos6', 'lj_btn');
		add_option('pos7', 'odn_btn');
		add_option('pos8', 'mm_btn');
		add_option('sociallikes_twitter_via');	
		add_option('sociallikes_twitter_rel');	
		add_option('sociallikes_img_url');	
		add_option('sociallikes_ul', '<ul class="social-likes"><li class="vkontakte" title="Поделиться ссылкой во Вконтакте">Вконтакте</li><li class="facebook" title="Поделиться ссылкой на Фейсбуке">Facebook</li><li class="twitter" title="Поделиться ссылкой в Твиттере">Twitter</li><li class="plusone" title="Поделиться ссылкой в Гугл-плюсе">Google+</li></ul>');
		
		add_action('wp_head', array(&$this, 'header_content'));
		add_action('admin_menu', array(&$this, 'wpsociallikes_menu'));
		add_action('admin_head', array(&$this, 'admin_menu_css'));
		add_action('save_post', array(&$this, 'save_post_meta'));
		add_action('admin_enqueue_scripts', array(&$this, 'wpsociallikes_admin_scripts'));
		add_filter('the_content', array(&$this, 'add_social_likes'));
	}
	
	function header_content() {
		?>
			<link href="<?php echo plugin_dir_url(__FILE__) ?>css/social-likes.css" rel="stylesheet">
			<script src="<?php echo plugin_dir_url(__FILE__) ?>js/social-likes.min.js"></script>
		<?php
	}
		
	function wpsociallikes_admin_scripts() {
		wp_enqueue_script('jquery-ui-sortable');
	}
	
	function wpsociallikes_menu() {
		add_meta_box('wpsociallikes', 'Social Likes', array(&$this, 'wpsociallikes_meta'), 'post', 'normal');
		add_meta_box('wpsociallikes', 'Social Likes', array(&$this, 'wpsociallikes_meta'), 'page', 'normal');
		
		add_options_page('WP Social Likes', 'WP Social Likes', 10, basename(__FILE__), array (&$this, 'admin_form'));
	}
	
	function wpsociallikes_meta($post) {
		$checked = get_post_meta($post->ID, 'sociallikes', true);
		?>
			<input type="checkbox" name="wpsociallikes" id="wpsociallikes" <?php if ($checked) echo 'checked' ?> />
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
			$buttons = get_option('sociallikes_ul');
			$content .= $buttons;
		}
		return $content;
	}
	
	function admin_menu_css() {
		?>			
			<link href="<?php echo plugin_dir_url(__FILE__) ?>css/social-likes.css" rel="stylesheet">
			<link href="<?php echo plugin_dir_url(__FILE__) ?>css/admin-page.css" rel="stylesheet">
			<script src="<?php echo plugin_dir_url(__FILE__) ?>js/preview.js"></script>
			<script src="<?php echo plugin_dir_url(__FILE__) ?>js/social-likes.min.js"></script>
		<?php
	}
	
	function admin_form() {
		if (isset($_POST['submit'])) {
			$positions	= $_POST['site'];
			$buttons = array('vk_btn', 'facebook_btn', 'twitter_btn', 'google_btn', 'pinterest_btn', 'lj_btn', 'odn_btn', 'mm_btn');
		
			$li['vk_btn'] = '<li class="vkontakte" title="Поделиться ссылкой во Вконтакте">Вконтакте</li>';
			$li['facebook_btn'] = '<li class="facebook" title="Share link on Facebook">Facebook</li>';
			$li['twitter_btn_part1'] = '<li class="twitter" ';
			$li['twitter_btn_part2'] = 'title="Share link on Twitter">Twitter</li>';
			$li['google_btn'] = '<li class="plusone" title="Share link on Google+">Google+</li>';
			$li['pinterest_btn_part1'] = '<li class="pinterest" title="Share image on Pinterest" data-media="';
			$li['pinterest_btn_part2'] = '">Pinterest</li>';
			$li['lj_btn'] = '<li class="livejournal" title="Share link on LiveJournal">LiveJournal</li>';
			$li['odn_btn'] = '<li class="odnoklassniki" title="Поделиться ссылкой в Одноклассниках">Одноклассники</li>';
			$li['mm_btn'] = '<li class="mailru" title="Поделиться ссылкой в Моём мире">Мой мир</li>';

			$pos_count = count($positions);
			
			foreach ($buttons as $value) {
				if (in_array($value, $positions)) {
					update_option($value, true);	
					$position = array_search($value, $positions) + 1;
				} else {
					update_option($value, false);
					$position = $pos_count + 1;
					++$pos_count;
				}
				$option_name = 'pos'.$position; 
				update_option($option_name, $value);	
			}

			$new_ul = '<ul class="social-likes';
			
			if ($_POST['look'] == 'h') {
				$new_ul .= '"';
			} elseif ($_POST['look'] == 'v') {
				$new_ul .= ' social-likes_vertical"';
			} else {
				$new_ul .= ' social-likes_single" data-single-title="Поделиться"';
			}
			
			if (!isset($_POST['counters'])) {
				$new_ul .= ' data-counters="no"';
			} 
			$new_ul .= '>';

			foreach ($positions as $value) {
				if ($value == 'twitter_btn') {
					$new_ul .= $li['twitter_btn_part1'];
					$twitter_via = $_POST['twitter_via'];
					if ($twitter_via != '') {
						$new_ul .= 'data-via="' . $twitter_via . '" ';
					}
					$twitter_rel = $_POST['twitter_rel'];
					if ($twitter_rel != '') {
						$new_ul .= 'data-related="' . $twitter_rel . '" ';
					}
					$new_ul .= $li['twitter_btn_part2'];
				} elseif ($value == 'pinterest_btn') {
					$img_url = $_POST['img_url'];
					$new_ul .= $li['pinterest_btn_part1'] . $img_url . $li['pinterest_btn_part2'];
				} else {
					$new_ul .= $li[$value];	
				}
			}
			
			$new_ul .= '</ul>';
			
			update_option('sociallikes_ul', $new_ul);
			update_option('sociallikes_twitter_via', $twitter_via);
			update_option('sociallikes_twitter_rel', $twitter_rel);
			update_option('sociallikes_img_url', $img_url);
		}	
	
		$wpsl_ul = get_option('sociallikes_ul');
		
		$horisontal = (!strstr($wpsl_ul, 'vertical') && !strstr($wpsl_ul, 'single'));
		$vertical = strstr($wpsl_ul, 'vertical');
		$single = strstr($wpsl_ul, 'single');
		
		$counters = !strstr($wpsl_ul, 'data-counters="no"');

		$label["vk_btn"] = "VK";
		$label["facebook_btn"] = "Facebook";
		$label["twitter_btn"] = "Twitter";
		$label["google_btn"] = "Google+";
		$label["pinterest_btn"] = "Pinterest";
		$label["lj_btn"] = "LiveJournal";
		$label["odn_btn"] = "Одноклассники";
		$label["mm_btn"] = "Moй мир";
		
		?>
			<div class="wrap">
				<h2>WP Social Buttons Settings</h2>
				
				<form name="wpsociallikes" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=wp-social-likes.php&amp;updated=true">
					
					<?php wp_nonce_field('update-options'); ?>
				
					<table class="plugin-setup">
						<tr valign="top">
							<th scope="row">Look</th>
							<td class="switch-button-row">
								<div style="float: left;">
									<input type="radio" name="look" id="h_look" class="view-state" value="h" <?php if ($horisontal) echo 'checked' ?> />
									<label class="switch-button" for="h_look" class="wpsl-label">Horizontal</label>

									<input type="radio" name="look" id="v_look" class="view-state" value="v" <?php if ($vertical) echo 'checked' ?> />
									<label class="switch-button" for="v_look" class="wpsl-label">Vertical</label>

									<input type="radio" name="look" id="s_look" class="view-state" value="s" <?php if ($single) echo 'checked' ?> />
									<label class="switch-button" for="s_look" class="wpsl-label">Single button</label>
								</div>
								<div class="show-counters">
									<input type="checkbox" name="counters" id="counters" <?php if ($counters) echo 'checked' ?> />
									<label for="counters" class="wpsl-label">Show counters</label>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th class="valign-top" scope="row">Websites</th>
							<td>
								<ul class="sortable-container">	

									<?php 
										for ($i = 1; $i <= count($label); $i++) {
											$option = 'pos' . $i;
											$btn = get_option($option);
											$checked = get_option($btn);
											if (!(($btn == 'odn_btn' || $btn == 'mm_btn') && get_bloginfo('language') != 'ru-RU')) {
												?>
												<li class="sortable-item">
													<input type="checkbox" name="site[]" id="<?php echo $btn ?>" value="<?php echo $btn ?>" <?php if ($checked) echo 'checked' ?> />					
													<label for="<?php echo $btn ?>" class="wpsl-label"><?php echo $label[$btn] ?></label>
												</li>				
												<?php
											}
										}
									?>							
								</ul>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Twitter Via</th>
							<td>
								<input type="text" name="twitter_via" placeholder="Username" class="wpsl-field" 
									value="<?php echo get_option('sociallikes_twitter_via'); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Twitter Related</th>
							<td>
								<input type="text" name="twitter_rel" placeholder="Username:Description" class="wpsl-field" 
									value="<?php echo get_option('sociallikes_twitter_rel'); ?>"/>
							</td>
						</tr>
		
					</table>
					<div class="row">
						<div id="preview" class="shadow-border"></div>
					</div>
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="page_options" value="sociallikes_ul" />
					
					<?php submit_button(); ?>
				</form>
			</div>
		<?php
	}
}

$wpsociallikes = new wpsociallikes();	

?>