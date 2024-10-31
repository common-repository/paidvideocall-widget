<?php

/**
 * @package PaidVideoCall Widget
 * @version 1.0
 * @author PaidVideoCall <support@paidvideocall.com>
 */

/*
 * Plugin Name: PaidVideoCall Widget
 * Description: Display a paidvideocall profile widget in your website.
 * Version: 1.0
 * Requires at least: 4.1
 * Requires PHP: 5.6
 * Author: PaidVideoCall
 * Author URI: https://paidvideocall.com
 * License: GPLv3
 * Text Domain: paidvideocall
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'PVCW_VERSION', '1.0.0' );
define( 'PVCW_API_URL', 'https://rest.paidvideocall.com:8080/api/' );
define( 'PVCW_ACCESS_KEY', 'paidvideocallaccesskey' );

$plugin = plugin_basename(__FILE__);

register_activation_hook( __FILE__, 'pvcw_widget_activate' );
function pvcw_widget_activate() {
	add_action('admin_menu', 'pvcw_widget_menu');
}

add_action('admin_menu', 'pvcw_widget_menu');
function pvcw_widget_menu() {
	add_options_page( 'PaidVideoCall Widget Settings', 'PaidVideoCall Widget', 'manage_options', 'paidvideocallwidgetsettings', 'pvcw_widget_settings_page' );	
}

add_filter("plugin_action_links_$plugin", 'pvcw_widget_settings_link' );
function pvcw_widget_settings_link($links) {
	$settings_link = '<a href="options-general.php?page=paidvideocallwidgetsettings">'.__('Settings','paidvideocall').'</a>';
  	array_unshift($links, $settings_link);
  	return $links;
}

add_action('wp_footer', 'pvcw_add_widget_script');
function pvcw_add_widget_script() {
	if(!empty(get_option('_pvcw_widget_api_key')) && !empty(get_option('_pvcw_widget_url'))) {
		if(empty(get_option('_pvcw_widget_page')) || get_option('_pvcw_widget_page') == 'all') {
			wp_enqueue_script('pvcw_widget_script', esc_url(get_option('_pvcw_widget_url')) ,array(),'',true);
		}
		else {
			$pvcw_widget_page_array = get_option('_pvcw_widget_page');
			if(in_array(get_queried_object_id(),$pvcw_widget_page_array)) {
				wp_enqueue_script('pvcw_widget_script', esc_url(get_option('_pvcw_widget_url')) ,array(),'',true);
			}
		}
	}
}

add_action( 'admin_init', 'pvcw_widget_settings' );
function pvcw_widget_settings() {
	register_setting( 'paidvideocall-widget-settings-group', 'pvcw_widget_api_key' );	
}

add_action('admin_enqueue_scripts', 'pvcw_admin_script');
function pvcw_admin_script() {
	wp_enqueue_script('chosen_script', plugins_url('/assets/js/chosen.jquery.min.js',__FILE__),array(),'',true);
	wp_enqueue_script('pvcw_script', plugins_url('/assets/js/pvcw_script.js',__FILE__),array(),'',true);
	wp_localize_script('pvcw_script', 'ajax_var', array('url' => admin_url('admin-ajax.php')));	
	wp_enqueue_style('chosen_style', plugins_url('/assets/css/chosen.min.css',__FILE__));
	wp_enqueue_style('pvcw_style', plugins_url('/assets/css/pvcw_style.css',__FILE__));	
}

if ( isset( $_GET['settings-updated'] ) ) {
    if ( !empty(get_option('pvcw_widget_api_key')) ) {
        echo '<div id="message" class="notice notice-warning is-dismissible">
            <p><strong>'.__('Widget enabled successfully.', 'paidvideocall').'</strong></p>
        </div>';
	}
}

add_action( 'wp_ajax_pvcw_widget_page', 'pvcw_widget_page');
add_action( 'wp_ajax_nopriv_pvcw_widget_page', 'pvcw_widget_page');
function pvcw_widget_page () {
    if(isset($_POST['pvcw_widget_page_id']) && !empty($_POST['pvcw_widget_page_id'])) {
		if($_POST['pvcw_widget_page_id'] == 'all') {
			update_option("_pvcw_widget_page", sanitize_text_field($_POST['pvcw_widget_page_id']));
			echo "saved";
		}
		else {
			if(count($_POST['pvcw_widget_page_id']) != 0) {
				$pvcw_widget_page_id_array = array();
				foreach ( $_POST['pvcw_widget_page_id'] as $pvcw_widget_page_id ) {
					$pvcw_widget_page_id_array[] = sanitize_text_field($pvcw_widget_page_id);
				}
				update_option("_pvcw_widget_page", $pvcw_widget_page_id_array);
				echo "saved";
			}
		}
    }
	die();
}

add_action( 'wp_ajax_pvcw_widget_url', 'pvcw_widget_url');
add_action( 'wp_ajax_nopriv_pvcw_widget_url', 'pvcw_widget_url');
function pvcw_widget_url () {
    if(isset($_POST['pvcw_widget_key']) && !empty($_POST['pvcw_widget_key'])) {
        update_option("_pvcw_widget_api_key", sanitize_text_field($_POST['pvcw_widget_key']));
		update_option("_pvcw_widget_url", sanitize_text_field($_POST['pvcw_widget_url']));
		echo "saved";
    }
	die();
}

add_action( 'wp_ajax_pvcw_disable_widget_url', 'pvcw_disable_widget_url');
add_action( 'wp_ajax_nopriv_pvcw_disable_widget_url', 'pvcw_disable_widget_url');
function pvcw_disable_widget_url() {
	if(isset($_POST['pvcw_widget_key']) && !empty($_POST['pvcw_widget_key'])) {
        delete_option("_pvcw_widget_api_key");
		delete_option("_pvcw_widget_url");
		delete_option("_pvcw_widget_page");
		echo "disabled";
    }
	die();
}

function pvcw_widget_settings_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e('PaidVideoCall Widget Settings','paidvideocall') ?></h1>					
		<div class="pvcw_section">
			<div class="pvcw_section_left">
				<div class="pvcw_sub_section">
					<h2><?php esc_html_e('License Information','paidvideocall') ?></h2>
					<div class="widget-info">
						<?php $url = 'https://app.paidvideocall.com'; ?>
						<p><?php printf( __( 'Visit <a href="%s" target="_blank">PaidVideoCall App</a> to find your widget key.', 'paidvideocall' ), esc_url( $url ) ); ?></p>
						<p><?php esc_html_e('Input your widget key into the field below to integrate your PaidVideoCall widget.','paidvideocall') ?></p>
					</div>
					<!-- Settings Form -->
					<form method="post" action="options.php" id="paidvideocall-widget-key-form">
						<?php settings_fields( 'paidvideocall-widget-settings-group' ); ?>
						<?php do_settings_sections( 'paidvideocall-widget-settings-group' ); ?>
						<?php $pvcw_widget_key = esc_attr( get_option('_pvcw_widget_api_key') ); ?>
						<?php $pvcw_widget_page =  get_option('_pvcw_widget_page') ; ?>
						<table class="form-table">				
							<tr>
								<th><?php esc_html_e('Widget Key','paidvideocall'); ?></th>
								<td><input type="text" name="_pvcw_widget_api_key" id="pvcw_widget_api_key" value="<?php echo esc_attr($pvcw_widget_key); ?>" <?php if(!empty($pvcw_widget_key)){ echo "readonly"; } ?>></td>
							</tr>
						</table>	
						<!-- Save Changes Button -->
						<div class="form-bottom">
							<input type="submit" id="submit" class="button button-primary" name="save" value="Save" <?php if(!empty($pvcw_widget_key)){ echo "disabled"; } ?>>
							<a href="javascript:void(0);" id="disable_widget_btn" class="button button-primary disable_widget_btn" style="<?php if(empty($pvcw_widget_key)){ echo "display:none"; } ?>"><?php esc_html_e('Disable','paidvideocall'); ?></a>
							<img src="<?php echo esc_url( plugins_url( '/assets/images/loader.gif', __FILE__ ) ); ?>" class="pvcw_loader" alt="loader">
							<div class="response-msg" id="pvcw_widget_key_response"></div>
						</div>
					</form>
				</div>	
				<div class="pvcw_sub_section" id="widget_page_section" style="<?php if(!empty($pvcw_widget_key)){ echo 'display:block'; } ?>">
					<h2><?php esc_html_e('Widget show settings','paidvideocall') ?></h2>
					<table class="form-table">				
						<tr>
							<th><?php esc_html_e('Show on','paidvideocall'); ?></th>
							<td>
								<label><input type="checkbox" name="show_all_pages" value="all" id="show_all_pages"  <?php if(!is_array($pvcw_widget_page)){ echo 'checked'; } ?>>&nbsp;<?php esc_html_e('All pages','paidvideocall'); ?></label>
							</td>
						</tr>
						<tr class="pvcw_pages_row <?php if(is_array($pvcw_widget_page)){ echo 'show_row'; } ?>">	
							<th><?php esc_html_e('Select pages','paidvideocall'); ?></th>
							<td>
								<select name="_pvcw_widget_page" id="pvcw_widget_page" data-placeholder="<?php esc_html_e('Choose a page...','paidvideocall'); ?>" multiple class="pvcw-chosen-select">
									<?php 
									$all_pages = get_pages();
									if(count($all_pages) != 0) {
										$is_selected = '';
										foreach($all_pages as $all_page) {
											echo '<option value="'.esc_attr($all_page->ID).'">'.esc_html($all_page->post_title).'</option>';
										}
									}
									?>
								</select>
							</td>
						</tr>				
					</table>	
					<!-- Save Changes Button -->
					<div class="form-bottom">
						<a href="javascript:void(0);" id="pvcw_widget_page_save" class="button button-primary pvcw_widget_page_save"><?php esc_html_e('Save','paidvideocall'); ?></a>
						<div class="response-msg" id="pvcw_widget_page_response"></div>
					</div>
					<input type="hidden" id="selected_widget_page" value="<?php echo esc_attr(implode(",",$pvcw_widget_page)); ?>">
					<input type="hidden"id="api_url" value="<?php echo PVCW_API_URL; ?>">
					<input type="hidden"id="access_key" value="<?php echo PVCW_ACCESS_KEY; ?>">
				</div>
			</div>	
			<div class="pvcw_section_right">
				<div class="pvcw_sub_section">
					<h2><?php esc_html_e('How get widget key','paidvideocall'); ?></h2>
					<div class="key_process">
						<ul>
							<li><?php printf( __('1) Visit <a href="%s" target="_blank">PaidVideoCall App</a> and login there.', 'paidvideocall'), esc_url( $url ) ); ?></li>
							<li><?php printf( __('2) After login, just go to <a href="%s" target="_blank">Widget Setup</a> under Promote menu.', 'paidvideocall'), esc_url( $url.'/promote/widget-setup') ); ?><br><img src="<?php echo esc_url( plugins_url( '/assets/images/step-1.png', __FILE__ ) ); ?>" alt="<?php echo __('step 1','paidvideocall'); ?>"></li>
							<li><?php esc_html_e('3) Now choose integration type "Wordpress", fill website name and website url. After fill these information, click on "Save changes" button to save information.','paidvideocall'); ?><br><img src="<?php echo esc_url( plugins_url( '/assets/images/step-2.png', __FILE__ ) ); ?>" alt="<?php echo __('step 2','paidvideocall'); ?>"></li>
							<li><?php esc_html_e('4) Now widget key will be generated for your configuration, just click on "Copy" button to copy it and paste it plugin Widget key field.','paidvideocall'); ?><br><img src="<?php echo esc_url( plugins_url( '/assets/images/step-3.png', __FILE__ ) ); ?>" alt="<?php echo __('step 3','paidvideocall'); ?>"></li>
						</ul>
					</div>
				</div>
			</div>
		</div>	
	</div>	
	<?php
}