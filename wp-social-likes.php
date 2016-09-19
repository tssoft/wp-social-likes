<?php
/*
Plugin Name: Social Likes
Description: Wordpress plugin for Social Likes library by Artem Sapegin (http://sapegin.me/projects/social-likes)
Version: 6.9.19
Author: TS Soft
Author URI: http://ts-soft.ru/en/
License: MIT

Copyright 2016 TS Soft LLC (email: dev@ts-soft.ru )

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

class wpsociallikes {
	const OPTION_NAME_MAIN = 'sociallikes';
	const OPTION_NAME_CUSTOM_LOCALE = 'sociallikes_customlocale';
	const OPTION_NAME_PLACEMENT = 'sociallikes_placement';
	const OPTION_NAME_SHORTCODE = 'sociallikes_shortcode';
	const OPTION_NAME_EXCERPTS = 'sociallikes_excerpts';

	var $lang;
	var $options;
	var $buttons = array(
		'vk_btn',
		'facebook_btn',
		'twitter_btn',
		'google_btn',
		'pinterest_btn',
		'lj_btn',
		'linkedin_btn',
		'odn_btn',
		'mm_btn',
		'email_btn'
	);
	var $skins = array('classic', 'flat', 'birman');

	function wpsociallikes() {	
		add_option(self::OPTION_NAME_CUSTOM_LOCALE, '');
		add_option(self::OPTION_NAME_PLACEMENT, 'after');
		add_option(self::OPTION_NAME_SHORTCODE, 'disabled');
		add_option(self::OPTION_NAME_EXCERPTS, 'disabled');

		add_action('init', array(&$this, 'ap_action_init'));
		add_action('wp_enqueue_scripts', array(&$this, 'wp_enqueue_scripts'));
		add_action('wp_enqueue_styles', array(&$this, 'wp_enqueue_styles'));
		add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
		add_action('admin_menu', array(&$this, 'admin_menu'));
		add_action('save_post', array(&$this, 'save_post_meta'));
		add_filter('the_content', array(&$this, 'add_social_likes'));
		add_filter('get_the_excerpt', array(&$this, 'get_the_excerpt'), 0);
		add_filter('wp_trim_excerpt', array(&$this, 'wp_trim_excerpt'));

		add_filter('plugin_action_links', array(&$this, 'add_action_links'), 10, 2);

		add_shortcode('wp-social-likes', array(&$this, 'shortcode_content'));
	}

	function ap_action_init() {
		$this->load_options();
		$custom_locale = $this->options->customLocale;
		if ($custom_locale) {
			load_textdomain('wp-social-likes', plugin_dir_path( __FILE__ ).'/languages/wp-social-likes-'.$custom_locale.'.mo');
		} else {
			load_plugin_textdomain('wp-social-likes', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
		}
		$this->title_vkontakte = __('Share link on VK', 'wp-social-likes');
		$this->title_facebook = __('Share link on Facebook', 'wp-social-likes');
		$this->title_twitter = __('Share link on Twitter', 'wp-social-likes');
		$this->title_plusone = __('Share link on Google+', 'wp-social-likes');
		$this->title_pinterest = __('Share image on Pinterest', 'wp-social-likes');
		$this->title_livejournal = __('Share link on LiveJournal', 'wp-social-likes');
		$this->title_linkedin = __('Share link on LinkedIn', 'wp-social-likes');
		$this->title_odnoklassniki = __('Share link on Odnoklassniki', 'wp-social-likes');
		$this->title_mailru = __('Share link on Mail.ru', 'wp-social-likes');
		$this->title_email = __('Share link by E-mail', 'wp-social-likes');
		$this->label_vkontakte = __('VK', 'wp-social-likes');
		$this->label_facebook = __('Facebook', 'wp-social-likes');
		$this->label_twitter = __('Twitter', 'wp-social-likes');
		$this->label_plusone = __('Google+', 'wp-social-likes');
		$this->label_pinterest = __('Pinterest', 'wp-social-likes');
		$this->label_livejournal = __('LiveJournal', 'wp-social-likes');
		$this->label_linkedin = __('LinkedIn', 'wp-social-likes');
		$this->label_odnoklassniki = __('Odnoklassniki', 'wp-social-likes');
		$this->label_mailru = __('Mail.ru', 'wp-social-likes');
		$this->label_email = __('E-mail', 'wp-social-likes');
		$this->label_share = __('Share', 'wp-social-likes');
	}

	function wp_enqueue_scripts() {
		wp_enqueue_script('jquery');

		$this->enqueue_script_library();

		if ($this->custom_buttons_enabled()) {
			wp_register_script('social_likes_custom_buttons', plugins_url('js/custom-buttons.js', __FILE__));
			wp_enqueue_script('social_likes_custom_buttons');
		}

		$skin = str_replace('light', '', $this->options->skin);
		if (($skin != 'classic') && ($skin != 'flat') && ($skin != 'birman')) {
			$skin = 'classic';
		}
		$this->enqueue_style_by_skin($skin);

		if ($this->custom_buttons_enabled()) {
			$this->enqueue_style_custom_buttons();
			$this->enqueue_style_custom_buttons_by_skin($skin);
		}
	}

	function admin_enqueue_scripts($hook) {
		if ('settings_page_wp-social-likes' !== $hook) {
			return;
		}

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-sortable');

		$this->enqueue_script_library();
		wp_register_script('social_likes_admin_preview', plugins_url('js/preview.js', __FILE__));
		wp_enqueue_script('social_likes_admin_preview');

		wp_register_style('social_likes_admin', plugins_url('css/admin-page.css', __FILE__));
		wp_enqueue_style('social_likes_admin');

		$this->enqueue_style_custom_buttons();
		foreach ($this->skins as $skin) {
			$this->enqueue_style_by_skin($skin);
			$this->enqueue_style_custom_buttons_by_skin($skin);
		}
	}

	function enqueue_script_library() {
		wp_register_script('social_likes_library', plugins_url('js/social-likes.min.js', __FILE__));
		wp_enqueue_script('social_likes_library');
	}

	function enqueue_style_by_skin($skin) {
		wp_register_style('social_likes_style_' . $skin, plugins_url('css/social-likes_' . $skin . '.css', __FILE__));
		wp_enqueue_style('social_likes_style_' . $skin);
	}

	function enqueue_style_custom_buttons() {
		wp_register_style('social_likes_custom_buttons', plugins_url('css/custom-buttons.css', __FILE__));
		wp_enqueue_style('social_likes_custom_buttons');
	}

	function enqueue_style_custom_buttons_by_skin($skin) {
		wp_register_style('social_likes_style_' . $skin . '_custom_buttons', plugins_url('css/custom-buttons_' . $skin . '.css', __FILE__));
		wp_enqueue_style('social_likes_style_' . $skin . '_custom_buttons');
	}

	function admin_menu() {
		$post_opt = $this->options->post;
		$page_opt = $this->options->page;
		add_meta_box('wpsociallikes', 'Social Likes', array(&$this, 'wpsociallikes_meta'), 'post', 'normal', 'default', array('default'=>$post_opt));
		add_meta_box('wpsociallikes', 'Social Likes', array(&$this, 'wpsociallikes_meta'), 'page', 'normal', 'default', array('default'=>$page_opt));

		$args = array(
		  'public'   => true,
		  '_builtin' => false
		);
		$post_types = get_post_types($args, 'names', 'and');
	  	foreach ($post_types  as $post_type ) {
	    	add_meta_box('wpsociallikes', 'Social Likes', array(&$this, 'wpsociallikes_meta'), $post_type, 'normal', 'default', array('default'=>$post_opt));
	  	}

		$page = add_options_page('Social Likes', 'Social Likes', 'administrator', basename(__FILE__), array (&$this, 'display_admin_form'));
	}

	function wpsociallikes_meta($post, $metabox) {
		if (!strstr($_SERVER['REQUEST_URI'], '-new.php')) {
			$checked = get_post_meta($post->ID, 'sociallikes', true);
		} else {
			$checked = $metabox['args']['default'];
		}

		if ($checked) {
			$img_url = get_post_meta($post->ID, 'sociallikes_img_url', true);
			if ($img_url == '' && $this->options->pinterestImg) {
				$img_url = $this->get_post_first_img($post);
			}
		} else {
			$img_url = '';
		}

		?>
			<div id="social-likes">
				<input type="hidden" name="wpsociallikes_update_meta" value="true" />

				<div style="padding: 5px 0">
					<input type="checkbox" name="wpsociallikes_enabled" id="wpsociallikes_enabled" <?php if ($checked) echo 'checked class="checked"' ?> title="<?php echo get_permalink($post->ID); ?>" />
					<label for="wpsociallikes_enabled"><?php _e('Add social buttons', 'wp-social-likes') ?></label>
				</div>

				<table>
					<tr>
						<td><label for="wpsociallikes_image_url" style="padding-right:5px"><?php _e('Image&nbspURL:', 'wp-social-likes') ?></label></td>
						<td style="width:100%">
							<input name="wpsociallikes_image_url" id="wpsociallikes_image_url" value="<?php echo $img_url ?>" <?php if (!$checked) echo 'disabled' ?> type="text" placeholder="<?php _e('Image URL (required for Pinterest)', 'wp-social-likes') ?>" style="width:100%" />
						</td>
					</tr>
				</table>
			</div>

			<script>
				(function($) {
					var savedImageUrlValue = '';
					$('input#wpsociallikes_enabled').change(function () {
						var $this = $(this);
						$this.toggleClass('checked');
						var socialLikesEnabled = $this.hasClass('checked');
						var imageUrlField = $('#wpsociallikes_image_url');
						if (socialLikesEnabled) {
							imageUrlField
								.removeAttr('disabled')
								.val(savedImageUrlValue);
						} else {
							savedImageUrlValue = imageUrlField.val();
							imageUrlField
								.attr('disabled', 'disabled')
								.val('');
						}
					});	
				})( jQuery );
			</script>	
		<?php
	}

	function get_post_first_img($post) {
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		$match = $matches[1];
		return count($match) !== 0 ? $match[0] : null;
	}

	function save_post_meta($post_id) {
		if (!isset($_POST['wpsociallikes_update_meta']) || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
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

		update_post_meta($post_id, 'sociallikes', isset($_POST['wpsociallikes_enabled']));

		$img_url_sent = isset($_POST['wpsociallikes_image_url']);
		$img_url = $img_url_sent ? $_POST['wpsociallikes_image_url'] : '';
		if ($img_url_sent && $img_url == '' && $this->options->pinterestImg) {
			$post = get_post($post_id);
			$img_url = $this->get_post_first_img($post); 
		}
		update_post_meta($post_id, 'sociallikes_img_url', $img_url);
	}

	function add_social_likes($content = '') {
		global $post;
		if (in_the_loop() && !is_feed() && !$this->is_excerpt && get_post_meta($post->ID, 'sociallikes', true)
				&& (is_page() || is_single() || $this->options->excerpts || !$this->is_post_with_excerpt())) {
			$this->lang = get_bloginfo('language');
			$buttons = $this->build_buttons($post);
			$placement = $this->options->placement;
			if ($placement != 'none') {
				if ($placement == 'before') {
					$content = $buttons . $content;
				} else if ($placement == 'before-after') {
					$content = $buttons . $content . $buttons;
				} else {
					$content .= $buttons;
				}
			}
		}
		return $content;
	}

	function is_post_with_excerpt() {
		global $page, $pages;
		$post_content = $pages[$page - 1];
		return preg_match('/<!--more(.*?)?-->/', $post_content);
	}

	function build_buttons($post) {
		$twitter_via = $this->options->twitterVia;
		//$twitter_rel = $this->options->twitterRel;
		$look = $this->options->look;
		$skin = $this->options->skin;
		$light = false;
		if (strpos($skin, 'light')) {
		    $light = true;
		    $skin = str_replace('light', '', $skin);
		}
		$iconsOnly = $this->options->iconsOnly;

		$label_vkontakte = $iconsOnly ? '' : $this->label_vkontakte;
		$label_facebook = $iconsOnly ? '' : $this->label_facebook;
		$label_twitter = $iconsOnly ? '' : $this->label_twitter;
		$label_plusone = $iconsOnly ? '' : $this->label_plusone;
		$label_pinterest = $iconsOnly ? '' : $this->label_pinterest;
		$label_livejournal = $iconsOnly ? '' : $this->label_livejournal;
		$label_linkedin = $iconsOnly ? '' : $this->label_linkedin;
		$label_odnoklassniki = $iconsOnly ? '' : $this->label_odnoklassniki;
		$label_mailru = $iconsOnly ? '' : $this->label_mailru;
		$label_email = $iconsOnly ? '' : $this->label_email;

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

		$img_url = get_post_meta($post->ID, 'sociallikes_img_url', true);
		$socialButton['pinterest_btn'] = '<div class="pinterest" title="' . $this->title_pinterest . '"';
		if ($img_url != '') {
			$socialButton['pinterest_btn'] .= ' data-media="' . $img_url . '"';
		}
		if ($img_url == '' && $this->options->pinterestImg) {
			$socialButton['pinterest_btn'] .= ' data-media="' . $this->get_post_first_img($post) . '"';	
		}
		$socialButton['pinterest_btn'] .= '>' . $label_pinterest . '</div>';

		$socialButton['lj_btn'] =
			'<div class="livejournal" title="'
			.$this->title_livejournal
			.'" data-html="&lt;a href=\'{url}\'&gt;{title}&lt;/a&gt;">'
			.$label_livejournal.'</div>';

		$socialButton['linkedin_btn'] = '<div class="linkedin" title="'.$this->title_linkedin.'">'.$label_linkedin.'</div>';

		$socialButton['odn_btn'] = '<div class="odnoklassniki" title="'.$this->title_odnoklassniki.'">'.$label_odnoklassniki.'</div>';

		$socialButton['mm_btn'] = '<div class="mailru" title="'.$this->title_mailru.'">'.$label_mailru.'</div>';

		$socialButton['email_btn'] = '<div class="email" title="'.$this->title_email.'">'.$label_email.'</div>';

		$main_div = '<div class="social-likes';

		$classAppend = '';
		if ($iconsOnly) {
			$classAppend .= ' social-likes_notext';
		}
		if (($skin == 'flat') && $light) {
			$classAppend .= ' social-likes_light';
		}

		if ($look == 'h') {
			$main_div .= $classAppend.'"';
		} elseif ($look == 'v') {
			$main_div .= ' social-likes_vertical'.$classAppend.'"';
		} else {
			$main_div .= ' social-likes_single'.$classAppend.'" data-single-title="'.$this->label_share.'"';
		}

		$main_div .= ' data-title="' . $post->post_title . '"';
		$main_div .= ' data-url="' . get_permalink( $post->ID ) . '"';
		$main_div .= $this->options->counters ? ' data-counters="yes"' : ' data-counters="no"';
		$main_div .= $this->options->zeroes ? ' data-zeroes="yes"' : '';

		$main_div .= '>';

		foreach ($this->options->buttons as $btn) {
			if (in_array($btn, $this->buttons)) {
				$main_div .= $socialButton[$btn];
			}
		}
		$main_div .= '</div><form style="display: none;" class="sociallikes-livejournal-form"></form>';

		return $main_div;
	}

	function get_the_excerpt($text) {
		$this->is_excerpt = true;
		return $text;
	}

	function wp_trim_excerpt($text) {
		$this->is_excerpt = false;
		return $text;
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

		$this->load_options();

		$look = $this->options->look;
		$counters = $this->options->counters;
		$post = $this->options->post;
		$page = $this->options->page;
		$skin = $this->options->skin;
		$light = false;
		if (strpos($skin, 'light')) {
		    $light = true;
		}
		if (($skin != 'classic') && ($skin != 'flat') && ($skin != 'flatlight') && ($skin != 'birman')) {
			$skin = 'classic';
		}
		$zeroes = $this->options->zeroes;
		$iconsOnly = $this->options->iconsOnly;

		$label["vk_btn"] = __("VK", 'wp-social-likes');
		$label["facebook_btn"] = __("Facebook", 'wp-social-likes');
		$label["twitter_btn"] = __("Twitter", 'wp-social-likes');
		$label["google_btn"] = __("Google+", 'wp-social-likes');
		$label["pinterest_btn"] = __("Pinterest", 'wp-social-likes');
		$label["lj_btn"] = __("LiveJournal", 'wp-social-likes');
		$label["linkedin_btn"] = __("LinkedIn", 'wp-social-likes');
		$label["odn_btn"] = __("Odnoklassniki", 'wp-social-likes');
		$label["mm_btn"] = __("Mail.ru", 'wp-social-likes');
		$label["email_btn"] = __("E-mail", 'wp-social-likes');

		$this->lang = get_bloginfo('language');
		?>
			<div class="wrap">
				<h2><?php _e('Social Likes Settings', 'wp-social-likes') ?></h2>

				<form name="wpsociallikes" method="post" action="?page=wp-social-likes.php&amp;updated=true">

					<?php wp_nonce_field('update-options'); ?>

					<input id="title_vkontakte" type="hidden" value="<?php echo $this->title_vkontakte ?>">
					<input id="title_facebook" type="hidden" value="<?php echo $this->title_facebook ?>">
					<input id="title_twitter" type="hidden" value="<?php echo $this->title_twitter ?>">
					<input id="title_plusone" type="hidden" value="<?php echo $this->title_plusone ?>">
					<input id="title_pinterest" type="hidden" value="<?php echo $this->title_pinterest ?>">
					<input id="title_livejournal" type="hidden" value="<?php echo $this->title_livejournal ?>">
					<input id="title_linkedin" type="hidden" value="<?php echo $this->title_linkedin ?>">
					<input id="title_odnoklassniki" type="hidden" value="<?php echo $this->title_odnoklassniki ?>">
					<input id="title_mailru" type="hidden" value="<?php echo $this->title_mailru ?>">
					<input id="title_email" type="hidden" value="<?php echo $this->title_email ?>">
					<input id="label_vkontakte" type="hidden" value="<?php echo $this->label_vkontakte ?>">
					<input id="label_facebook" type="hidden" value="<?php echo $this->label_facebook ?>">
					<input id="label_twitter" type="hidden" value="<?php echo $this->label_twitter ?>">
					<input id="label_plusone" type="hidden" value="<?php echo $this->label_plusone ?>">
					<input id="label_pinterest" type="hidden" value="<?php echo $this->label_pinterest ?>">
					<input id="label_livejournal" type="hidden" value="<?php echo $this->label_livejournal ?>">
					<input id="label_linkedin" type="hidden" value="<?php echo $this->label_linkedin ?>">
					<input id="label_odnoklassniki" type="hidden" value="<?php echo $this->label_odnoklassniki ?>">
					<input id="label_mailru" type="hidden" value="<?php echo $this->label_mailru ?>">
					<input id="label_email" type="hidden" value="<?php echo $this->label_email ?>">
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
									<label class="switch-button" for="skin_flat"><?php _e('Flat', 'wp-social-likes') ?></label>

									<input type="radio" name="skin" id="skin_flatlight" class="view-state<?php if ($skin == 'flat') echo ' checked' ?>" value="flatlight" <?php if ($skin == 'flatlight') echo ' checked' ?> />
									<label class="switch-button" for="skin_flatlight"><?php _e('Flat Light', 'wp-social-likes') ?></label>

									<input type="radio" name="skin" id="skin_birman" class="view-state<?php if ($skin == 'birman') echo ' checked' ?>" value="birman" <?php if ($skin == 'birman') echo ' checked' ?> />
									<label class="switch-button" for="skin_birman"><?php _e('Birman', 'wp-social-likes') ?></label>
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
								<div class="option-checkboxes" id="zeroes-container">
									<input type="checkbox" name="zeroes" id="zeroes" <?php if ($zeroes) echo 'checked' ?> />
									<label for="zeroes" class="wpsl-label"><?php _e('With zeroes', 'wp-social-likes') ?></label>
								</div>
								<div class="option-checkboxes" id="icons-container">
									<input type="checkbox" name="icons" id="icons" <?php if ($iconsOnly) echo 'checked' ?> />
									<label for="icons" class="wpsl-label"><?php _e('Icons only', 'wp-social-likes') ?></label>
								</div>
							</td>
						</tr>
						<tr>
							<td/>
							<td class="wpsl-info-block">
								<?php _e('Twitter, LiveJournal & E-mail counters do not work by design', 'wp-social-likes') ?>
							</td>
						</tr>
						<tr valign="top">
							<th class="valign-top" scope="row"><?php _e('Websites', 'wp-social-likes') ?></th>
							<td class="without-bottom">
								<ul class="sortable-container">	
									<?php
										$remainingButtons = $this->buttons;
										for ($i = 1; $i <= count($label); $i++) {
											$btn = null;
											$checked = false;
											$buttons = & $this->options->buttons;
											if ($i <= count($buttons)) {
												$btn = $buttons[$i - 1];
												$index = array_search($btn, $remainingButtons, true);
												if ($index !== false) {
													array_splice($remainingButtons, $index, 1);
													$checked = true;
												}
											}
											if ($btn == null) {
												$btn = array_shift($remainingButtons);
											}
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
									if ($this->lang != 'ru-RU'
										&& !($this->is_button_active('odn_btn')
										&& $this->is_button_active('mm_btn'))) {
										?><span class="more-websites"><?php _e('More websites', 'wp-social-likes') ?></span><?php		
									}
								?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e('Twitter Via', 'wp-social-likes') ?></th>
							<td>
								<input type="text" name="twitter_via" placeholder="<?php _e('Username', 'wp-social-likes') ?>" class="wpsl-field" 
									value="<?php echo $this->options->twitterVia; ?>" />
							</td>
						</tr>
						<!--tr valign="top">
							<th scope="row">Twitter Related</th>
							<td>
								<input type="text" name="twitter_rel" placeholder="Username:Description" class="wpsl-field" 
									value="<?php echo $this->options->twitterRel; ?>"/>
							</td>
						</tr-->
						<tr valign="top">
							<th scope="row"></th>
							<td scope="row" class="without-bottom">
								<input type="checkbox" name="pinterest_img" id="pinterest_img"<?php if ($this->options->pinterestImg) echo ' checked' ?> />
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
		$options = new wpsociallikes_options();
		$options->skin = $_POST['skin'];
		$options->look = $_POST['look'];
		$options->post = isset($_POST['post_chb']);
		$options->page = isset($_POST['page_chb']);
		$options->pinterestImg = isset($_POST['pinterest_img']);
		$options->twitterVia = $_POST['twitter_via'];
		//$options->twitterRel = $_POST['twitter_rel'];
		$options->iconsOnly = isset($_POST['icons']);
		$options->counters = isset($_POST['counters']);
		$options->zeroes = isset($_POST['zeroes']);
		$options->buttons = array();
		if (isset($_POST['site'])) {
			foreach ($_POST['site'] as $btn) {
				if (in_array($btn, $this->buttons)) {
					array_push($options->buttons, $btn);
				}
			}
		}
		update_option(self::OPTION_NAME_MAIN, $options);
		$this->delete_deprecated_options();
	}

	function shortcode_content() {
		global $post;
		if ($this->options->shortcode) {
			return $this->build_buttons($post);
		}
		return '';
	}

	function add_action_links($all_links, $current_file) {
		if (basename(__FILE__) == basename($current_file)) {
			$plugin_file_name_parts = explode('/', plugin_basename(__FILE__));
			$plugin_file_name = $plugin_file_name_parts[count($plugin_file_name_parts) - 1];
			$settings_link = '<a href="' . admin_url('options-general.php?page='
				. $plugin_file_name) . '">'
				. __('Settings', 'wp-social-likes') . '</a>';
			array_unshift($all_links, $settings_link);
		}
		return $all_links;
	}

	function custom_buttons_enabled() {
		return $this->is_button_active('lj_btn') || $this->is_button_active('linkedin_btn') || $this->is_button_active('email_btn');
	}

	function is_button_active($name) {
		return in_array($name, $this->options->buttons);
	}

	function load_options() {
		$options = $this->load_deprecated_options();

		if (!$options) {
			$options = get_option(self::OPTION_NAME_MAIN);
			if (!$options || !is_object($options) || !is_a($options, 'wpsociallikes_options')) {
				$options = new wpsociallikes_options();
				$options->counters = true;
				$options->look = 'h';
				$options->post = true;
				$options->skin = 'classic';
			}
		}

		if (!$options->buttons || !is_array($options->buttons)) {
			$options->buttons = array();
			for ($i = 0; $i < 4; $i++) {
				array_push($options->buttons, $this->buttons[$i]);
			}
		}

		$options->customLocale = get_option(self::OPTION_NAME_CUSTOM_LOCALE);
		$options->placement = get_option(self::OPTION_NAME_PLACEMENT);
		$options->shortcode = get_option(self::OPTION_NAME_SHORTCODE) == 'enabled';
		$options->excerpts = get_option(self::OPTION_NAME_EXCERPTS) == 'enabled';

		if (!$options->look) {
			$options->look = 'h';
		}
		if (!$options->skin) {
			$options->skin = 'classic';
		}
		if (!$options->twitterVia) {
			$options->twitterVia = '';
		}
		if (!$options->twitterRel) {
			$options->twitterRel = '';
		}

		$this->options = $options;
	}

	function load_deprecated_options() {
		$options = $this->load_deprecated_options_1_11();
		if (!$options) {
			$options = $this->load_deprecated_options_5_5();
		}
		return $options;
	}

	function load_deprecated_options_1_11() {
		if (!get_option('sociallikes_skin')
			|| !get_option('sociallikes_look')) {
			return null;
		}
		$options = new wpsociallikes_options();
		$options->skin = get_option('sociallikes_skin');
		$options->look = get_option('sociallikes_look');
		$options->post = get_option('sociallikes_post');
		$options->page = get_option('sociallikes_page');
		$options->pinterestImg = get_option('sociallikes_pinterest_img');
		$options->twitterVia = get_option('sociallikes_twitter_via');
		$options->twitterRel = get_option('sociallikes_twitter_rel');
		$options->iconsOnly = get_option('sociallikes_icons');
		$options->counters = get_option('sociallikes_counters');
		$options->zeroes = get_option('sociallikes_zeroes');
		$options->buttons = array();
		for ($i = 1; $i <= 8; $i++) {
			$option = 'pos' . $i;
			$btn = get_option($option);
			if (get_option($btn)) {
				array_push($options->buttons, $btn);
			}
		}
		return $options;
	}

	function load_deprecated_options_5_5() {
		$options_array = get_option(self::OPTION_NAME_MAIN);
		if (!$options_array || !is_array($options_array)) {
			return null;
		}
		$options = new wpsociallikes_options();
		$options->skin = isset($options_array['skin']) ? $options_array['skin'] : null;
		$options->look = isset($options_array['look']) ? $options_array['look'] : null;
		$options->post = isset($options_array['post']) ? $options_array['post'] : null;
		$options->page = isset($options_array['page']) ? $options_array['page'] : null;
		$options->pinterestImg = isset($options_array['pinterestImg']) ? $options_array['pinterestImg'] : null;
		$options->twitterVia = isset($options_array['twitterVia']) ? $options_array['twitterVia'] : null;
		$options->twitterRel = isset($options_array['twitterRel']) ? $options_array['twitterRel'] : null;
		$options->iconsOnly = isset($options_array['iconsOnly']) ? $options_array['iconsOnly'] : null;
		$options->counters = isset($options_array['counters']) ? $options_array['counters'] : null;
		$options->zeroes = isset($options_array['zeroes']) ? $options_array['zeroes'] : null;
		$options->buttons = isset($options_array['buttons']) ? $options_array['buttons'] : null;
		return $options;
	}

	function delete_deprecated_options() {
		delete_option('sociallikes_counters');
		delete_option('sociallikes_look');
		delete_option('sociallikes_twitter_via');
		delete_option('sociallikes_twitter_rel');
		delete_option('sociallikes_pinterest_img');
		delete_option('sociallikes_post');
		delete_option('sociallikes_page');
		delete_option('sociallikes_skin');
		delete_option('sociallikes_icons');
		delete_option('sociallikes_zeroes');
		delete_option('sociallikes_ul');
		delete_option('vk_btn');
		delete_option('facebook_btn');
		delete_option('twitter_btn');
		delete_option('google_btn');
		delete_option('pinterest_btn');
		delete_option('lj_btn');
		delete_option('odn_btn');
		delete_option('mm_btn');
		delete_option('pos1');
		delete_option('pos2');
		delete_option('pos3');
		delete_option('pos4');
		delete_option('pos5');
		delete_option('pos6');
		delete_option('pos7');
		delete_option('pos8');
	}
}

class wpsociallikes_options {
	var $skin;
	var $look;
	var $post;
	var $page;
	var $pinterestImg;
	var $twitterVia;
	var $twitterRel;
	var $iconsOnly;
	var $counters;
	var $zeroes;
	var $buttons;
}

$wpsociallikes = new wpsociallikes();

function social_likes($postId = null) {
	echo get_social_likes($postId);
}

function get_social_likes($postId = null) {
	$post = get_post($postId);
	global $wpsociallikes;
	return $post != null ? $wpsociallikes->build_buttons($post) : '';
}
?>
