<?php
/*
Plugin Name: Multiple Domains with Analytics
Description: Allows Wordpress to be mirrored across multiple domain names. Includes optional analytics functionality.
Author: Danny Casady
Version: 1.1
License: GPL
*/


define('CMT_PLUGIN_PATH', plugin_dir_path( __FILE__ ));

require_once CMT_PLUGIN_PATH . 'multiple-domains.class.php';


if (class_exists("CmtMultipleDomains")) {
	$cmt_multiple_domains_plugin = new CmtMultipleDomains();
	$domain_options = array();
	$domain_options = $cmt_multiple_domains_plugin->get_admin_options();

	if ( $domain_options['g_analytics_enabled'] == 'true' ) {
		add_action('wp_head', array($cmt_multiple_domains_plugin, 'output_ga'));
	}
}

?>