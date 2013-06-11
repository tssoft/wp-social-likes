<?php
/*
Plugin Name: Social Likes
Description: Wordpress plugin for Social Likes library by Artem Sapegin (http://sapegin.me/projects/social-likes)
Version: 1.3
Author: TS Soft
Author URI: http://ts-soft.ru/en/
License: MIT

Copyright 2013 TS Soft LLC (email: dev@ts-soft.ru )

Permission is hereby granted, free of charge, to any person obtaining a 
copy of this software and associated documentation files (the 
"Software"), to deal in the Software without restriction, including 
without limitation the rights to use, copy, modify, merge, publish, 
distribute, sublicense, and/or sell copies of the Software, and to 
permit persons to whom the Software is furnished to do so, subject to 
the following conditions: 

The above copyright notice and this permission notice shall be included 
in all copies or substantial portions of the Software. 

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY 
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, 
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE 
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE. 

*/

class wpsociallikes
{
	var $ru_titles = array(
		"Поделиться ссылкой во Вконтакте", 
		"Поделиться ссылкой на Фейсбуке", 
		"Поделиться ссылкой в Твиттере",
		"Поделиться ссылкой в Гугл-плюсе",
		"Поделиться картинкой на Пинтересте",
		"Поделиться ссылкой в ЖЖ",
		"Поделиться ссылкой в Одноклассниках",
		"Поделиться ссылкой в Моём мире"
	);
					
	var $en_titles = array(
		"Share link on VK",
		"Share link on Facebook",
		"Share link on Twitter",
		"Share link on Google+",
		"Share image on Pinterest",
		"Share link on LiveJournal",
		"Поделиться ссылкой в Одноклассниках",
		"Поделиться ссылкой в Моём мире"
	);

	var $lang;
	
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
		add_option('sociallikes_counters');
		add_option('sociallikes_look', 'h');
		add_option('sociallikes_twitter_via');	
		add_option('sociallikes_twitter_rel');
		add_option('sociallikes_pinterest_img');
		add_option('sociallikes_post', true);	
		add_option('sociallikes_page', false);	
		
		add_action('wp_head', array(&$this, 'header_content'));
		add_action('wp_enqueue_scripts', array(&$this, 'header_scripts'));
		add_action('admin_menu', array(&$this, 'wpsociallikes_menu'));
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
	
	function header_scripts() {
		wp_enqueue_script('jquery');
	}
	
	function wpsociallikes_admin_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-sortable');
	}
	
	function wpsociallikes_menu() {
		$post_opt = get_option('sociallikes_post');
		$page_opt = get_option('sociallikes_page');
		add_meta_box('wpsociallikes', 'Social Likes', array(&$this, 'wpsociallikes_meta'), 'post', 'normal', 'default', array('default'=>$post_opt));
		add_meta_box('wpsociallikes', 'Social Likes', array(&$this, 'wpsociallikes_meta'), 'page', 'normal', 'default', array('default'=>$page_opt));
		
		$plugin_page = add_options_page('Social Likes', 'Social Likes', 10, basename(__FILE__), array (&$this, 'display_admin_form'));
		add_action('admin_head-' . $plugin_page, array(&$this, 'admin_menu_head'));
	}
	
	function wpsociallikes_meta($post, $metabox) {
		if (!strstr($_SERVER['REQUEST_URI'], '-new.php')) {
			$checked = get_post_meta($post->ID, 'sociallikes', true);
		} else {
			$checked = $metabox['args']['default'];
		}
		
		if ($checked) {
			$img_url = get_post_meta($post->ID, 'sociallikes_img_url', true);
		} else {
			$img_url = '';
		}
		
		?>
			<div id="social-likes">
				<div style="padding: 5px 0">
					<input type="checkbox" name="wpsociallikes" id="wpsociallikes" <?php if ($checked) echo 'checked class="checked"' ?> title="<?php echo get_permalink($post->ID); ?>" />
					<label for="wpsociallikes">Add social buttons</label>
				</div>
				
				<table>
					<tr>
						<td><label for="image_url" style="padding-right:5px">Image&nbspURL:</label></td>
						<td style="width:100%">
							<input name="image_url" id="image_url" value="<?php echo $img_url ?>" <?php if (!$checked) echo 'disabled' ?> type="text" placeholder="Image URL (required for Pinterest)" style="width:100%" />
						</td>
					</tr>
				</table>
			</div>
			
			<script>
				(function($) {
					$('input#wpsociallikes').change(function () {
						$(this).toggleClass('checked');
						if ($(this).hasClass('checked')) {
							$('#image_url').removeAttr('disabled');
						} else {
							$('#image_url').attr('value', '');
							$('#image_url').attr('disabled', 'disabled');
						}
					});	
				})( jQuery );
			</script>	
		<?php
	}
	
	function save_post_meta($post_id) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		if ('page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id)) {
				return;
			}
		} else {
			if (!current_user_can('edit_post', $post_id)) {
				return;
			}
		}

		update_post_meta($post_id, 'sociallikes', isset($_POST['wpsociallikes']));
		if (($_POST['image_url'] == "") & get_option('sociallikes_pinterest_img')) {
			//get first image
			$img_url = "";
			$post = get_post($post_id);
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
			$img_url = $matches [1] [0];
			update_post_meta($post_id, 'sociallikes_img_url', $img_url);
		}
		else
			update_post_meta($post_id, 'sociallikes_img_url', $_POST['image_url']);
	}
	
	function add_social_likes($content = '') {
		global $post, $page, $pages;
		$post_content = $pages[$page-1];
		$this->lang = get_bloginfo('language');
		if ((is_page() || is_single() || !preg_match('/<!--more(.*?)?-->/', $post_content, $matches)) && get_post_meta($post->ID, 'sociallikes', true))
		{
			$buttons = $this->build_ul();
			$buttons = str_replace('class="social-likes"', 'class="social-likes" data-title="'.$post->post_title.'"', $buttons);
			$img_url = get_post_meta($post->ID, 'sociallikes_img_url', true);
			if (strstr($buttons, 'Pinterest') && $img_url != '') {
				$parts = explode('data-media="', $buttons);
				$buttons = $parts[0] . 'data-media="' . $img_url . $parts[1];
			}
			if (!is_single() && !is_page()) {
				$buttons = str_replace('class="social-likes"', 'class="social-likes" data-url="'.get_permalink( $post->ID ).'"', $buttons);
			}
			if ($this->lang != 'ru-RU') {
				$buttons = str_replace($this->ru_titles, $this->en_titles, $buttons);
			} else {
				$buttons = str_replace($this->en_titles, $this->ru_titles, $buttons);
			}
			$content .= $buttons;
		}
		return $content;
	}

	function build_ul() {
		if ($this->lang == 'ru-RU') {
			$titles = $this->ru_titles;
		} else {
			$titles = $this->en_titles;
		}

		$twitter_via = get_option('sociallikes_twitter_via');
		$twitter_via = get_option('sociallikes_twitter_rel');
		$look = get_option('sociallikes_look');
		
		$li['vk_btn'] = '<li class="vkontakte" title="'.$titles[0].'">Вконтакте</li>';
		$li['facebook_btn'] = '<li class="facebook" title="'.$titles[1].'">Facebook</li>';
		$li['twitter_btn'] = '<li class="twitter" ';
		if ($twitter_via != '') {
			$li['twitter_btn'] .= 'data-via="' . $twitter_via . '" ';
		}
		if ($twitter_rel != '') {
			$li['twitter_btn'] .= 'data-related="' . $twitter_rel . '" ';
		}
		$li['twitter_btn'] .= 'title="'.$titles[2].'">Twitter</li>';
		$li['google_btn'] = '<li class="plusone" title="'.$titles[3].'">Google+</li>';
		$li['pinterest_btn'] = '<li class="pinterest" title="'.$titles[4].'" data-media="">Pinterest</li>';
		$li['lj_btn'] = '<li class="livejournal" title="'.$titles[5].'">LiveJournal</li>';
		$li['odn_btn'] = '<li class="odnoklassniki" title="'.$titles[6].'">Одноклассники</li>';
		$li['mm_btn'] = '<li class="mailru" title="'.$titles[7].'">Мой мир</li>';

		$new_ul = '<ul class="social-likes';

		if ($look == 'h') {
			$new_ul .= '"';
		} elseif ($look == 'v') {
			$new_ul .= ' social-likes_vertical"';
		} else {
			$new_ul .= ' social-likes_single" data-single-title="';
			if ($this->lang == 'ru-RU') {
				$new_ul .= 'Поделиться"';
			} else {
				$new_ul .= 'Share"';
			}
		}

		if (!get_option('sociallikes_counters')) {
			$new_ul .= ' data-counters="no"';
		} 
		$new_ul .= '>';

		for ($i = 1; $i <= count($li); $i++) {
			$option = 'pos' . $i;
			$btn = get_option($option);
			if (get_option($btn)) {
				$new_ul .= $li[$btn];		
			}
		}
		$new_ul .= '</ul>';

		return $new_ul;
	}
	
	function admin_menu_head() {
		?>			
			<link href="<?php echo plugin_dir_url(__FILE__) ?>css/social-likes.css" rel="stylesheet">
			<link href="<?php echo plugin_dir_url(__FILE__) ?>css/admin-page.css" rel="stylesheet">
			<script src="<?php echo plugin_dir_url(__FILE__) ?>js/preview.js"></script>
			<script src="<?php echo plugin_dir_url(__FILE__) ?>js/social-likes.min.js"></script>
		<?php
	}
	
	function display_admin_form() {
		if (isset($_POST['submit']) || isset($_POST['apply_to_posts']) || isset($_POST['apply_to_pages'])) {
			$this->submit_admin_form();
		}
		if (isset($_POST['apply_to_posts'])) {
			$args = array('numberposts' => -1, 'post_type' => 'post', 'post_status' => 'any');
			$result = get_posts($args);
			foreach ($result as $post) {
				update_post_meta($post->ID, 'sociallikes', isset($_POST['post_chb']));
			}
		}	
		if (isset($_POST['apply_to_pages'])) {
			$args = array('post_type' => 'page');
			$result = get_pages($args);
			foreach ($result as $post) {
				update_post_meta($post->ID, 'sociallikes', isset($_POST['page_chb']));
			}
		}
	
		$look = get_option('sociallikes_look');
		$counters = get_option('sociallikes_counters');
		$post = get_option('sociallikes_post');
		$page = get_option('sociallikes_page');

		$label["vk_btn"] = "VK";
		$label["facebook_btn"] = "Facebook";
		$label["twitter_btn"] = "Twitter";
		$label["google_btn"] = "Google+";
		$label["pinterest_btn"] = "Pinterest";
		$label["lj_btn"] = "LiveJournal";
		$label["odn_btn"] = "Одноклассники";
		$label["mm_btn"] = "Moй мир";

		$this->lang = get_bloginfo('language');
		?>
			<div class="wrap">
				<h2>Social Likes Settings</h2>

				<form name="wpsociallikes" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=wp-social-likes.php&amp;updated=true">
					
					<?php wp_nonce_field('update-options'); ?>
				
					<table class="plugin-setup">
						<tr valign="top">
							<th scope="row">Look</th>
							<td class="switch-button-row">
								<div style="float: left;">
									<input type="radio" name="look" id="h_look" class="view-state" value="h" <?php if ($look == 'h') echo 'checked' ?> />
									<label class="switch-button" for="h_look" class="wpsl-label">Horizontal</label>

									<input type="radio" name="look" id="v_look" class="view-state" value="v" <?php if ($look == 'v') echo 'checked' ?> />
									<label class="switch-button" for="v_look" class="wpsl-label">Vertical</label>

									<input type="radio" name="look" id="s_look" class="view-state" value="s" <?php if ($look == 's') echo 'checked' ?> />
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
											$hidden = ($this->lang != 'ru-RU') && !$checked && ($btn == 'odn_btn' || $btn == 'mm_btn');
											?>
											<li class="sortable-item" <?php if ($hidden) echo ' hidden="true"' ?>>
												<input type="checkbox" name="site[]" id="<?php echo $btn ?>" value="<?php echo $btn ?>" <?php if ($checked) echo 'checked' ?> />					
												<label for="<?php echo $btn ?>" class="wpsl-label"><?php echo $label[$btn] ?></label>
											</li>				
											<?php
										}
									?>			
								</ul>
								<?php 
									if ($this->lang != 'ru-RU' && !(get_option('odn_btn') && get_option('mm_btn'))) {
										?><span class="more-websites">More websites</span><?php		
									}
								?>
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
						<tr valign="top">
							<th scope="row"></th>
							<td scope="row">
								<input type="checkbox" name="pinterest_img" id="pinterest_img" <?php if (get_option('sociallikes_pinterest_img')) echo 'checked' ?> />
								<label for="pinterest_img" class="wpsl-label">Automatically place first image in the post/page to the Image URL field</label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"></th>
							<td>
								<input type="checkbox" name="post_chb" id="post_chb" <?php if ($post) echo 'checked' ?> />					
								<label for="post_chb" class="wpsl-label">Add by default for new posts</label>
								<input type="submit" name="apply_to_posts" id="apply_to_posts" value="Apply to existing posts" class="button-secondary"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"></th>
							<td>
								<input type="checkbox" name="page_chb" id="page_chb" <?php if ($page) echo 'checked' ?> />					
								<label for="page_chb" class="wpsl-label">Add by default for new pages</label>
								<input type="submit" name="apply_to_pages" id="apply_to_pages" value="Apply to existing pages" class="button-secondary" />
							</td>
						</tr>
		
					</table>
					<div class="row">
						<div id="preview" class="shadow-border" <?php if ($this->lang == 'ru-RU') echo 'language="ru"' ?> ></div>
					</div>
					
					<?php submit_button(); ?>
				</form>
			</div>
		<?php
	}

	function submit_admin_form() {
		$positions	= $_POST['site'];
		$buttons = array('vk_btn', 'facebook_btn', 'twitter_btn', 'google_btn', 'pinterest_btn', 'lj_btn', 'odn_btn', 'mm_btn');
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

		update_option('sociallikes_counters', isset($_POST['counters']));
		update_option('sociallikes_look', $_POST['look']);
		update_option('sociallikes_twitter_via', $_POST['twitter_via']);
		update_option('sociallikes_twitter_rel', $_POST['twitter_rel']);
		update_option('sociallikes_pinterest_img', isset($_POST['pinterest_img']));
		update_option('sociallikes_post', isset($_POST['post_chb']));
		update_option('sociallikes_page', isset($_POST['page_chb']));
	}
}

$wpsociallikes = new wpsociallikes();	

?>