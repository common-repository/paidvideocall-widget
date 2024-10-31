<?php 
/**
* Trigger this file on plugin uninstall
*
*@package: PaidVideoCall Widget
*/

if( !defined ('WP_UNINSTALL_PLUGIN'))
{
	exit();
}

$plugin_data_keys = array('_pvcw_widget_api_key','_pvcw_widget_url','_pvcw_widget_page');

foreach($plugin_data_keys as $plugin_data_key)
{
	delete_option($plugin_data_key);
}
