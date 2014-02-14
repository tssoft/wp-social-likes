<?php
/*
Plugin Name: Social Likes
Description: Wordpress plugin for Social Likes library by Artem Sapegin (http://sapegin.me/projects/social-likes)
Version: 1.9
Author: TS Soft
Author URI: http://ts-soft.ru/en/
License: MIT

Copyright 2014 TS Soft LLC (email: dev@ts-soft.ru )

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
	var $lang;
	
	function wpsociallikes() {	
		add_option('vk_btn', true);		
		add_option('facebook_btn', true);
		add_option('twitter_btn', true);
		add_option('google_btn', true);
		add_option('pinterest_btn', false);
		//add_option('lj_btn', false);
		add_option('odn_btn', false);
		add_option('mm_btn', false);
		add_option('pos1', 'vk_btn');
		add_option('pos2', 'facebook_btn');
		add_option('pos3', 'twitter_btn');
		add_option('pos4', 'google_btn');
		add_option('pos5', 'pinterest_btn');
		add_option('pos6', 'odn_btn');
		add_option('pos7', 'mm_btn');
		add_option('sociallikes_counters', true);
		add_option('sociallikes_look', 'h');
		add_option('sociallikes_twitter_via');
		//add_option('sociallikes_twitter_rel');
		add_option('sociallikes_pinterest_img');
		add_option('sociallikes_post', true);
		add_option('sociallikes_page', false);	
		add_option('sociallikes_skin', 'classic');
		add_option('sociallikes_light', false); // Deprecated
		add_option('sociallikes_icons', false);
		add_option('sociallikes_zeroes', false);
		add_option('sociallikes_customlocale', '');
		add_option('sociallikes_placement', 'after');
		
		add_action('init', array(&$this, 'ap_action_init'));
		add_action('wp_head', array(&$this, 'header_content'));
		add_action('wp_enqueue_scripts', array(&$this, 'header_scripts'));
		add_action('admin_menu', array(&$this, 'wpsociallikes_menu'));
		add_action('save_post', array(&$this, 'save_post_meta'));
		add_action('admin_enqueue_scripts', array(&$this, 'wpsociallikes_admin_scripts'));
		add_filter('the_content', array(&$this, 'add_social_likes'));

		// https://github.com/tssoft/wp-social-likes/issues/7
		add_filter('the_excerpt_rss', array(&$this, 'exclude_div_in_RSS_description'));
		add_filter('the_content_feed', array(&$this, 'exclude_div_in_RSS_content'));
	}
	
	function ap_action_init() {
		$customLocale = get_option('sociallikes_customlocale');
		$textdomainError = false;
		if ($customLocale != '') {
			$textdomainError =
				!load_textdomain('wp-social-likes', plugin_dir_path( __FILE__ ).'/languages/wp-social-likes-'.$customLocale.'.mo');
		}
		if (($customLocale == '') || $textdomainError) {
			load_plugin_textdomain('wp-social-likes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
		}
		$this->title_vkontakte = __('Share link on VK', 'wp-social-likes');
		$this->title_facebook = __('Share link on Facebook', 'wp-social-likes');
		$this->title_twitter = __('Share link on Twitter', 'wp-social-likes');
		$this->title_plusone = __('Share link on Google+', 'wp-social-likes');
		$this->title_pinterest = __('Share image on Pinterest', 'wp-social-likes');
		//$this->title_livejournal = __('Share link on LiveJournal', 'wp-social-likes');
		$this->title_odnoklassniki = __('Share link on Odnoklassniki', 'wp-social-likes');
		$this->title_mailru = __('Share link on Mail.ru', 'wp-social-likes');
		$this->label_vkontakte = __('VK', 'wp-social-likes');
		$this->label_facebook = __('Facebook', 'wp-social-likes');
		$this->label_twitter = __('Twitter', 'wp-social-likes');
		$this->label_plusone = __('Google+', 'wp-social-likes');
		$this->label_pinterest = __('Pinterest', 'wp-social-likes');
		//$this->label_livejournal = __('LiveJournal', 'wp-social-likes');
		$this->label_odnoklassniki = __('Odnoklassniki', 'wp-social-likes');
		$this->label_mailru = __('Mail.ru', 'wp-social-likes');
		$this->label_share = __('Share', 'wp-social-likes');
	}
	
	function header_content() {
		$skin = str_replace('light', '', get_option('sociallikes_skin'));
		if (($skin != 'classic') && ($skin != 'flat') && ($skin != 'birman')) {
			$skin = 'classic';
		}
		?>
			<link rel="stylesheet" id="styleClassic" href="<?php echo plugin_dir_url(__FILE__) ?>css/social-likes_<?php echo $skin ?>.css">
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
					<label for="wpsociallikes"><?php _e('Add social buttons', 'wp-social-likes') ?></label>
				</div>
				
				<table>
					<tr>
						<td><label for="image_url" style="padding-right:5px"><?php _e('Image&nbspURL:', 'wp-social-likes') ?></label></td>
						<td style="width:100%">
							<input name="image_url" id="image_url" value="<?php echo $img_url ?>" <?php if (!$checked) echo 'disabled' ?> type="text" placeholder="<?php _e('Image URL (required for Pinterest)', 'wp-social-likes') ?>" style="width:100%" />
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
			$buttons = $this->build_buttons();
			$buttons = str_replace(' data-counters', ' data-title="'.$post->post_title.'" data-counters', $buttons);
			$img_url = get_post_meta($post->ID, 'sociallikes_img_url', true);
			if (strstr($buttons, 'Pinterest') && $img_url != '') {
				$parts = explode('data-media="', $buttons);
				$buttons = $parts[0] . 'data-media="' . $img_url . $parts[1];
			}
			if (!is_single() && !is_page()) {
				$buttons = str_replace(' data-counters', ' data-url="'.get_permalink( $post->ID ).'" data-counters', $buttons);
			}
			$placement = get_option('sociallikes_placement');
			if ($placement == 'before') {
				$content = $buttons . $content;
			} else if ($placement == 'before-after') {
				$content = $buttons . $content . $buttons;
			} else {
				$content .= $buttons;
				if ($placement != 'after') {
					update_option('sociallikes_placement', 'after');
				}
			}
		}
		return $content;
	}

	function build_buttons() {
		$twitter_via = get_option('sociallikes_twitter_via');
		//$twitter_rel = get_option('sociallikes_twitter_rel');
		$look = get_option('sociallikes_look');
		$skin = get_option('sociallikes_skin');
		$lightOption = get_option('sociallikes_light'); // For backward compatibility
		$light = false;
		if (strpos($skin, 'light') || $lightOption) {
		    $light = true;
		    $skin = str_replace('light', '', $skin);
		}
		$iconsOnly = get_option('sociallikes_icons');
		
		$label_vkontakte = $iconsOnly ? '' : $this->label_vkontakte;
		$label_facebook = $iconsOnly ? '' : $this->label_facebook;
		$label_twitter = $iconsOnly ? '' : $this->label_twitter;
		$label_plusone = $iconsOnly ? '' : $this->label_plusone;
		$label_pinterest = $iconsOnly ? '' : $this->label_pinterest;
		//$label_livejournal = $iconsOnly ? '' : $this->label_livejournal;
		$label_odnoklassniki = $iconsOnly ? '' : $this->label_odnoklassniki;
		$label_mailru = $iconsOnly ? '' : $this->label_mailru;
		
		$socialButton['vk_btn'] = '<div class="vkontakte" title="'.$this->title_vkontakte.'">'.$label_vkontakte.'</div>';
		$socialButton['facebook_btn'] = '<div class="facebook" title="'.$this->title_facebook.'">'.$label_facebook.'</div>';
		$socialButton['twitter_btn'] = '<div class="twitter" ';
		if ($twitter_via != '') {
			$socialButton['twitter_btn'] .= 'data-via="' . $twitter_via . '" ';
		}
		/*if ($twitter_rel != '') {
			$socialButton['twitter_btn'] .= 'data-related="' . $twitter_rel . '" ';
		}*/
		$socialButton['twitter_btn'] .= 'title="'.$this->title_twitter.'">'.$label_twitter.'</div>';
		$socialButton['google_btn'] = '<div class="plusone" title="'.$this->title_plusone.'">'.$label_plusone.'</div>';
		$socialButton['pinterest_btn'] = '<div class="pinterest" title="'.$this->title_pinterest.'" data-media="">'.$label_pinterest.'</div>';
		//$socialButton['lj_btn'] = '<div class="livejournal" title="'.$this->title_livejournal.'">'.$label_livejournal.'</div>';
		$socialButton['odn_btn'] = '<div class="odnoklassniki" title="'.$this->title_odnoklassniki.'">'.$label_odnoklassniki.'</div>';
		$socialButton['mm_btn'] = '<div class="mailru" title="'.$this->title_mailru.'">'.$label_mailru.'</div>';

		$main_div = '<div class="social-likes';

		$classAppend = (($skin == 'flat') && $light) ? ' social-likes_light' : '';
		if ($iconsOnly) {
			$classAppend .= " social-likes_notext";
		}

		if ($look == 'h') {
			$main_div .= $classAppend.'"';
		} elseif ($look == 'v') {
			$main_div .= ' social-likes_vertical'.$classAppend.'"';
		} else {
			$main_div .= ' social-likes_single'.$classAppend.'" data-single-title="'.$this->label_share.'"';
		}

		$main_div .= get_option('sociallikes_counters') ? ' data-counters="yes"' : ' data-counters="no"';
		$main_div .= get_option('sociallikes_zeroes') ? ' data-zeroes="yes"' : '';

		$main_div .= '>';

		for ($i = 1; $i <= count($socialButton); $i++) {
			$option = 'pos' . $i;
			$btn = get_option($option);
			if (get_option($btn)) {
				$main_div .= $socialButton[$btn];		
			}
		}
		$main_div .= '</div>';

		return $main_div;
	}
	
	function admin_menu_head() {
		?>
			<link rel="stylesheet" id="styleClassic" href="<?php echo plugin_dir_url(__FILE__) ?>css/social-likes_classic.css">
		    <link rel="stylesheet" id="styleFlat" href="<?php echo plugin_dir_url(__FILE__) ?>css/social-likes_flat.css">
			<link rel="stylesheet" id="styleBirman" href="<?php echo plugin_dir_url(__FILE__) ?>css/social-likes_birman.css">
			<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>css/admin-page.css">
			<script src="<?php echo plugin_dir_url(__FILE__) ?>js/social-likes.min.js"></script>
			<script src="<?php echo plugin_dir_url(__FILE__) ?>js/preview.js"></script>
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
		$skin = get_option('sociallikes_skin');
		$lightOption = get_option('sociallikes_light'); // For backward compatibility
		$light = false;
		if (strpos($skin, 'light')) {
		    $light = true;
		} else if ($lightOption) {
		    $light = true;
			if ($skin == 'flat') {
				$skin .= 'light';
			}
		}
		if (($skin != 'classic') && ($skin != 'flat') && ($skin != 'flatlight') && ($skin != 'birman')) {
			$skin = 'classic';
		}
		$zeroes = get_option('sociallikes_zeroes');
		$icons = get_option('sociallikes_icons');

		$label["vk_btn"] = __("VK", 'wp-social-likes');
		$label["facebook_btn"] = __("Facebook", 'wp-social-likes');
		$label["twitter_btn"] = __("Twitter", 'wp-social-likes');
		$label["google_btn"] = __("Google+", 'wp-social-likes');
		$label["pinterest_btn"] = __("Pinterest", 'wp-social-likes');
		//$label["lj_btn"] = __("LiveJournal", 'wp-social-likes');
		$label["odn_btn"] = __("Odnoklassniki", 'wp-social-likes');
		$label["mm_btn"] = __("Mail.ru", 'wp-social-likes');

		$this->lang = get_bloginfo('language');
		?>
			<div class="wrap">
				<h2><?php _e('Social Likes Settings', 'wp-social-likes') ?></h2>

				<form name="wpsociallikes" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=wp-social-likes.php&amp;updated=true">
					
					<?php wp_nonce_field('update-options'); ?>
					
					<input id="title_vkontakte" type="hidden" value="<?php echo $this->title_vkontakte ?>">
					<input id="title_facebook" type="hidden" value="<?php echo $this->title_facebook ?>">
					<input id="title_twitter" type="hidden" value="<?php echo $this->title_twitter ?>">
					<input id="title_plusone" type="hidden" value="<?php echo $this->title_plusone ?>">
					<input id="title_pinterest" type="hidden" value="<?php echo $this->title_pinterest ?>">
					<!--input id="title_livejournal" type="hidden" value="<?php echo $this->title_livejournal ?>"-->
					<input id="title_odnoklassniki" type="hidden" value="<?php echo $this->title_odnoklassniki ?>">
					<input id="title_mailru" type="hidden" value="<?php echo $this->title_mailru ?>">
					<input id="label_vkontakte" type="hidden" value="<?php echo $this->label_vkontakte ?>">
					<input id="label_facebook" type="hidden" value="<?php echo $this->label_facebook ?>">
					<input id="label_twitter" type="hidden" value="<?php echo $this->label_twitter ?>">
					<input id="label_plusone" type="hidden" value="<?php echo $this->label_plusone ?>">
					<input id="label_pinterest" type="hidden" value="<?php echo $this->label_pinterest ?>">
					<!--input id="label_livejournal" type="hidden" value="<?php echo $this->label_livejournal ?>"-->
					<input id="label_odnoklassniki" type="hidden" value="<?php echo $this->label_odnoklassniki ?>">
					<input id="label_mailru" type="hidden" value="<?php echo $this->label_mailru ?>">
					<input id="label_share" type="hidden" value="<?php echo $this->label_share ?>">
					<input id="confirm_leaving_message" type="hidden" value="<?php _e('You have unsaved changes on this page. Do you want to leave this page and discard your changes?', 'wp-social-likes') ?>">
					
					<table class="plugin-setup">
						<tr valign="top">
							<th scope="row"><?php _e('Skin', 'wp-social-likes') ?></th>
							<td class="switch-button-row">
								<div style="float: left;">
									<input type="radio" name="skin" id="skin_classic" class="view-state<?php if ($skin == 'classic') echo ' checked' ?>" value="classic" <?php if ($skin == 'classic') echo 'checked' ?> />
									<label class="switch-button" for="skin_classic"><?php _e('Classic', 'wp-social-likes') ?></label>

									<input type="radio" name="skin" id="skin_flat" class="view-state<?php if ($skin == 'flat') echo ' checked' ?>" value="flat" <?php if ($skin == 'flat') echo ' checked' ?> />
									<label class="switch-button" for="skin_flat"><?php _e('Flat β', 'wp-social-likes') ?></label>

									<input type="radio" name="skin" id="skin_flatlight" class="view-state<?php if ($skin == 'flat') echo ' checked' ?>" value="flatlight" <?php if ($skin == 'flatlight') echo ' checked' ?> />
									<label class="switch-button" for="skin_flatlight"><?php _e('Flat Light β', 'wp-social-likes') ?></label>

									<input type="radio" name="skin" id="skin_birman" class="view-state<?php if ($skin == 'birman') echo ' checked' ?>" value="birman" <?php if ($skin == 'birman') echo ' checked' ?> />
									<label class="switch-button" for="skin_birman"><?php _e('Birman β', 'wp-social-likes') ?></label>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Look', 'wp-social-likes') ?></th>
							<td class="switch-button-row">
								<div style="float: left;">
									<input type="radio" name="look" id="h_look" class="view-state<?php if ($look == 'h') echo ' checked' ?>" value="h" <?php if ($look == 'h') echo 'checked' ?> />
									<label class="switch-button" for="h_look"<!--class="wpsl-label-->"><?php _e('Horizontal', 'wp-social-likes') ?></label>

									<input type="radio" name="look" id="v_look" class="view-state<?php if ($look == 'v') echo ' checked' ?>" value="v" <?php if ($look == 'v') echo ' checked' ?> />
									<label class="switch-button" for="v_look"><?php _e('Vertical', 'wp-social-likes') ?></label>

									<input type="radio" name="look" id="s_look" class="view-state<?php if ($look == 's') echo ' checked' ?>" value="s" <?php if ($look == 's') echo ' checked' ?> />
									<label class="switch-button" for="s_look"><?php _e('Single button', 'wp-social-likes') ?></label>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"></th>
							<td scope="row" class="without-bottom">
								<div class="option-checkboxes">
									<input type="checkbox" name="counters" id="counters" <?php if ($counters) echo 'checked' ?> />
									<label for="counters" class="wpsl-label"><?php _e('Show counters', 'wp-social-likes') ?></label>
								</div>
								<div class="option-checkboxes" id="withZeroes">
									<input type="checkbox" name="zeroes" id="zeroes" <?php if ($zeroes) echo 'checked' ?> />
									<label for="zeroes" class="wpsl-label"><?php _e('With zeroes', 'wp-social-likes') ?></label>
								</div>
								<div class="option-checkboxes" id="iconsOnly">
									<input type="checkbox" name="icons" id="icons" <?php if ($icons) echo 'checked' ?> />
									<label for="icons" class="wpsl-label"><?php _e('Icons only', 'wp-social-likes') ?></label>
								</div>
							</td>
						</tr>
						<tr valign="top">
							<th class="valign-top" scope="row"><?php _e('Websites', 'wp-social-likes') ?></th>
							<td class="without-bottom">
								<ul class="sortable-container">	
									<?php
										for ($i = 1; $i <= count($label); $i++) {
											$option = 'pos' . $i;
											$btn = get_option($option);
											$checked = get_option($btn);
											$hidden = ($this->lang != 'ru-RU') && !$checked && ($btn == 'odn_btn' || $btn == 'mm_btn');
											?>
											<li class="sortable-item<?php if ($hidden) echo ' hidden' ?>">
												<input type="checkbox" name="site[]" id="<?php echo $btn ?>" value="<?php echo $btn ?>" <?php if ($checked) echo 'checked' ?> />					
												<label for="<?php echo $btn ?>" class="wpsl-label"><?php echo $label[$btn] ?></label>
											</li>				
											<?php
										}
									?>			
								</ul>
								<?php
									if ($this->lang != 'ru-RU' && !(get_option('odn_btn') && get_option('mm_btn'))) {
										?><span class="more-websites"><?php _e('More websites', 'wp-social-likes') ?></span><?php		
									}
								?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Twitter Via', 'wp-social-likes') ?></th>
							<td>
								<input type="text" name="twitter_via" placeholder="<?php _e('Username', 'wp-social-likes') ?>" class="wpsl-field" 
									value="<?php echo get_option('sociallikes_twitter_via'); ?>" />
							</td>
						</tr>
						<!--tr valign="top">
							<th scope="row">Twitter Related</th>
							<td>
								<input type="text" name="twitter_rel" placeholder="Username:Description" class="wpsl-field" 
									value="<?php echo get_option('sociallikes_twitter_rel'); ?>"/>
							</td>
						</tr-->
						<tr valign="top">
							<th scope="row"></th>
							<td scope="row" class="without-bottom">
								<input type="checkbox" name="pinterest_img" id="pinterest_img" <?php if (get_option('sociallikes_pinterest_img')) echo 'checked' ?> />
								<label for="pinterest_img" class="wpsl-label"><?php _e('Automatically place first image in the post/page to the Image URL field', 'wp-social-likes') ?></label>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"></th>
							<td class="without-bottom">
								<input type="checkbox" name="post_chb" id="post_chb" <?php if ($post) echo 'checked' ?> />					
								<label for="post_chb" class="wpsl-label"><?php _e('Add by default for new posts', 'wp-social-likes') ?></label>
								<input type="submit" name="apply_to_posts" id="apply_to_posts" value="<?php _e('Apply to existing posts', 'wp-social-likes') ?>" class="button-secondary"/>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"></th>
							<td>
								<input type="checkbox" name="page_chb" id="page_chb" <?php if ($page) echo 'checked' ?> />					
								<label for="page_chb" class="wpsl-label"><?php _e('Add by default for new pages', 'wp-social-likes') ?></label>
								<input type="submit" name="apply_to_pages" id="apply_to_pages" value="<?php _e('Apply to existing pages', 'wp-social-likes') ?>" class="button-secondary" />
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
		$buttons = array('vk_btn', 'facebook_btn', 'twitter_btn', 'google_btn', 'pinterest_btn', 'odn_btn', 'mm_btn');
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
		//update_option('sociallikes_twitter_rel', $_POST['twitter_rel']);
		update_option('sociallikes_pinterest_img', isset($_POST['pinterest_img']));
		update_option('sociallikes_post', isset($_POST['post_chb']));
		update_option('sociallikes_page', isset($_POST['page_chb']));
		update_option('sociallikes_skin', $_POST['skin']);
		//update_option('sociallikes_light', $_POST['light']);
		update_option('sociallikes_zeroes', $_POST['zeroes']);
		update_option('sociallikes_icons', $_POST['icons']);
	}

	function exclude_div_in_RSS_description($content) {
		global $post;
		if (get_post_meta($post->ID, 'sociallikes', true)) {
			$index = strripos($content, ' ');
			$content = substr_replace($content, '', $index);
		}
	    return $content;
	}

	function exclude_div_in_RSS_content($content) {
	    if (is_feed()) {
	    	$content = preg_replace("/<div.*(class)=(\"|')social-likes(\"|').*>.*<\/div>/smUi", '', $content);
	    }

	    return $content;
	}
}

$wpsociallikes = new wpsociallikes();	

?>