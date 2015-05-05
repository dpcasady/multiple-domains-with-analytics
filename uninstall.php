<?php
/*
Uninstall
*/


/* If uninstall not called from WordPress exit */

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

/* Delete all options associated with this plugin */

delete_option( 'cmt_dm_options' );

?>