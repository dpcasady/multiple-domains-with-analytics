<?php
/*
CmtMultipleDomains Class
*/


if ( !class_exists( "CmtMultipleDomains" ) ) {

    class CmtMultipleDomains {

        var $access_level = 'manage_options';
        var $options;
        var $server;

        public function __construct() {
            $this->options = $this->get_admin_options();
            $this->server = $_SERVER['SERVER_NAME'];

            add_action( 'admin_menu', array( $this, 'on_admin_menu' ) );
            add_action( 'admin_post_save_multiple_domains', array( $this, 'on_save_changes' ) );

            add_filter( 'option_blogname', array( $this, 'filter_blogname' ), 1 );
            add_filter( 'option_siteurl', array( $this, 'filter_siteurl' ), 1 );
            add_filter( 'option_home', array( $this, 'filter_home' ), 1 );
            add_filter( 'option_blogdescription', array( $this, 'filter_blogdescription' ), 1 );
            add_filter( 'plugin_action_links',  array( $this, 'settings_link' ), 10, 2 );
        }


        public function output_ga() {

            if ( $this->options['ignore_logged_in'] == 'true' && is_user_logged_in() ) {
                print "<!-- Google Analytics tracking code not shown because logged in users are ignored -->\n";
                return false;
            }

            $ua_string = '';
            $count = 1;
            foreach ( $this->options as $key => $value ) {
                if ( substr( $key, 0, 7 ) == 'domain_' ) {
                    if ( strcasecmp( $this->server, $value ) == 0 || strcasecmp( $this->server, 'www.' . $value ) == 0 ) {
                        $ua_string = $this->options['analytics_' . $count];
                    }
                    $count++;
                }
            } ?>

            <?php if ( $this->options['universal_analytics'] == 'true' ) : ?>

            <script type="text/javascript">
                (function(i,s,o,g,r,a,m) {
                    i['GoogleAnalyticsObject'] = r;
                    i[r] = i[r] || function() {
                        (i[r].q = i[r].q || []).push(arguments)
                    }, i[r].l = 1 * new Date();
                    a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
                    a.async = 1;
                    a.src = g;
                    m.parentNode.insertBefore(a,m)
                })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                ga('create', '<?php echo $ua_string;?>', 'auto');
                ga('require', 'displayfeatures');
                ga('send', 'pageview');
            </script>

            <?php else: ?>

            <script type="text/javascript">//<![CDATA[
                var _gaq = _gaq || [];
                _gaq.push(['_setAccount','<?php echo $ua_string;?>']);
                _gaq.push(['_trackPageview']);
                (function() {
                    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
                })();
            //]]></script>

            <?php endif;
        }


        private function plugin_options_url() {
            return admin_url( 'options-general.php?page=' . CMT_MULTIPLE_DOMAINS_NAME );
        }


        public function settings_link( $links, $file ) {
            static $this_plugin;
            if ( empty( $this_plugin ) ) {
                $this_plugin = explode( "/", CMT_BASENAME );
                $this_plugin = $this_plugin[0] . '/multiple-domains.php';
            }
            if ( $file == $this_plugin ) {
                $settings_link = '<a href="' . $this->plugin_options_url() . '">' . 'Settings' . '</a>';
                array_unshift( $links, $settings_link );
            }
            return $links;
        }


        public function filter_blogname( $content ) {

            for ( $count = 1; $count <= $this->options['count']; $count++ ) {
                if ( strcasecmp( $this->server, $this->options['domain_' . $count] ) == 0 || strcasecmp( $this->server, 'www.' . $this->options['domain_' . $count] ) == 0 ) {
                    return $this->options['blogname_' . $count];
                }
            }
            return $content;
        }


        public function filter_siteurl( $content ) {

            for ( $count = 1; $count <= $this->options['count']; $count++ ) {
                if ( strcasecmp( $this->server, $this->options['domain_' . $count] ) == 0 || strcasecmp( $this->server, 'www.' . $this->options['domain_' . $count] ) == 0 ) {
                    return $this->options['siteurl_' . $count];
                }
            }
            return $content;
        }


        public function filter_home( $content ) {

            for ( $count = 1; $count <= $this->options['count']; $count++ ) {
                if ( strcasecmp( $this->server, $this->options['domain_' . $count] ) == 0 || strcasecmp( $this->server, 'www.' . $this->options['domain_' . $count] ) == 0 ) {
                    return $this->options['home_' . $count];
                }
            }
            return $content;
        }


        public function filter_blogdescription( $content ) {

            for ( $count = 1; $count <= $this->options['count']; $count++ ) {
                if ( strcasecmp( $this->server, $this->options['domain_' . $count] ) == 0 || strcasecmp( $this->server, 'www.' . $this->options['domain_' . $count] ) == 0 ) {
                    return $this->options['blogdescription_' . $count];
                }
            }
            return $content;
        }


        public function on_admin_menu() {
            wp_register_style( 'cmt-style', CMT_PLUGIN_URL . 'css/multiple-domains.css' );
            wp_register_script( 'cmt-script', CMT_PLUGIN_URL . '/js/multiple-domains.js', 'jquery' );
            $this->pagehook = add_options_page( 'Multiple Domains with Analytics Options', 'Multiple Domains with Analytics', $this->access_level, CMT_MULTIPLE_DOMAINS_NAME, array( $this, 'on_show_page' ) );
            add_action( 'load-' . $this->pagehook, array( $this, 'on_load_page' ) );
        }


        public function on_load_page() {
            wp_enqueue_script( 'common' );
            wp_enqueue_script( 'wp-lists' );
            wp_enqueue_script( 'postbox' );
            wp_enqueue_script( 'cmt-script' );
            wp_enqueue_style( 'cmt-style' );

            add_meta_box( 'general-options', 'Google Analytics Options', array( $this, 'print_meta_box_options' ), $this->pagehook, 'advanced', 'core' );

            for ( $count = 1; $count <= $this->options['count']; $count++ ) {
                add_meta_box( 'domain-' . $count, '<span class="handle-index">' . $count . '</span>&nbsp; ' . $this->options["domain_" . $count], array( $this, 'print_meta_box_contents' ), $this->pagehook, 'normal', 'core', $count );
            }
        }


        public function get_admin_options() {
            $default_options = array();
            $cmt_options = array();
            $retrieved_options = array();

            require_once CMT_PLUGIN_PATH . 'config.php';
            $retrieved_options = maybe_unserialize( get_option( 'cmt_dm_options' ) );

            /* Set up defaults */

            if ( $retrieved_options == '' ) {  // There are no options set
                add_option( 'cmt_dm_options', serialize( $default_options ) );
                $retrieved_options = $default_options;
            }
            elseif ( count( $retrieved_options ) == 0 ) { // All options are blank
                update_option( 'cmt_dm_options', serialize( $default_options ) );
                $retrieved_options = $default_options;
            }

            /* Count the domains and extract the fields we're interested in */

            $count = 0;

            foreach ( $retrieved_options as $key => $value ) {
                if ( substr( $key, 0, 7 ) == 'domain_' ) {
                    $cmt_options[$key] = $value;
                    $count = $count + 1;
                }
                if ( substr( $key, 0, 9 ) == 'blogname_' ) {
                    $cmt_options[$key] = $value;
                }
                if ( substr( $key, 0, 16 ) == 'blogdescription_' ) {
                    $cmt_options[$key] = $value;
                }
                if ( substr( $key, 0, 8 ) == 'siteurl_' ) {
                    $cmt_options[$key] = $value;
                }
                if ( substr( $key, 0, 5 ) == 'home_' ) {
                    $cmt_options[$key] = $value;
                }
                if ( substr( $key, 0, 10 ) == 'analytics_' ) {
                    $cmt_options[$key] = $value;
                }
                if ( substr( $key, 0, 19 ) == 'g_analytics_enabled' ) {
                    $cmt_options[$key] = $value;
                }
                if ( substr( $key, 0, 16 ) == 'ignore_logged_in' ) {
                    $cmt_options[$key] = $value;
                }
                if ( substr( $key, 0, 19 ) == 'universal_analytics' ) {
                    $cmt_options[$key] = $value;
                }
            }

            $cmt_options['count'] = $count;
            return $cmt_options;
        }


        public function on_show_page() {
            if ( isset( $_GET['cmt_action'] ) && $_GET['cmt_action'] == "Saved" ) { ?>
                <div id="message" class="updated fade"><p><strong><?php _e( 'Changes saved.' ) ?></strong></p></div>
            <?php }

            if ( isset( $_GET['cmt_action'] ) && $_GET['cmt_action'] == "Blank" ) { ?>
                <div id="message" class="updated fade"><p><strong><?php _e( 'Blank domain added. Edit fields and save to complete.' ) ?></strong></p></div>
            <?php } ?>

            <div id="multiple-domains" class="wrap">
                <?php screen_icon( 'options-general' ); ?>
                <h2>Multiple Domains with Analytics</h2>
                <p>Current domain: <b><?php _e( $this->server );?></b></p>
                <form action="admin-post.php" method="post">
                    <?php wp_nonce_field( 'multiple-domains' ); ?>
                    <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
                    <?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
                    <input type="hidden" name="action" value="save_multiple_domains" />
                    <hr>
                    <p>
                        This plugin allows multiple domain names to be used for the same wordpress installation.
                        To make sure that it works correctly, all domains must be configured with the proper DNS settings prior to use.
                    <p>

                    <div id="poststuff" class="metabox-holder">
                        <div id="post-body">
                            <div id="post-body-content">
                                <?php do_meta_boxes( $this->pagehook, 'advanced', $this->options ); ?>
                                <?php do_meta_boxes( $this->pagehook, 'normal', $this->options ); ?>
                                <br />
                                <span class="submit">
                                    <input type="submit" class="button" name="cmt_action" value="Add New Domain" />
                                </span>
                                <span class="submit">
                                    <input type="submit" class="button-primary" name="cmt_action" value="Save All Changes" />
                                </span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <script type="text/javascript">
                //<![CDATA[
                jQuery(document).ready( function($) {
                    $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                    postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
                });
                //]]>
            </script>
        <?php }


        public function on_save_changes() {
            if ( !current_user_can( $this->access_level ) )
                wp_die( __( 'Cheatin&#8217; uh?' ) );
            check_admin_referer( 'multiple-domains' );

            /* Process option saving */

            if ( isset( $_POST["cmt_action"] ) && $_POST["cmt_action"] == "Save All Changes" ) {
                $new_options = array();
                $count = 0;

                foreach ( $_POST as $key => $value ) {

                    if ( substr( $key, 0, 11 ) == 'cmt_domain_' ) {
                        $count = $count + 1;
                        $new_options['domain_' . $count] = $value;
                    }
                    if ( substr( $key, 0, 13 ) == 'cmt_blogname_' ) {
                        $new_options['blogname_' . $count] = $value;
                    }
                    if ( substr( $key, 0, 20 ) == 'cmt_blogdescription_' ) {
                        $new_options['blogdescription_' . $count] = $value;
                    }
                    if ( substr( $key, 0, 12 ) == 'cmt_siteurl_' ) {
                        $new_options['siteurl_' . $count] = $value;
                    }
                    if ( substr( $key, 0, 9 ) == 'cmt_home_' ) {
                        $new_options['home_' . $count] = $value;
                    }
                    if ( substr( $key, 0, 14 ) == 'cmt_analytics_' ) {
                        $new_options['analytics_' . $count] = $value;
                    }
                    if ( substr( $key, 0, 23 ) == 'cmt_g_analytics_enabled' ) {
                        $new_options['g_analytics_enabled'] = $value;
                    }
                    if ( substr( $key, 0, 20 ) == 'cmt_ignore_logged_in' ) {
                        $new_options['ignore_logged_in'] = $value;
                    }
                    if ( substr( $key, 0, 23 ) == 'cmt_universal_analytics' ) {
                        $new_options['universal_analytics'] = $value;
                    }
                }
                if ( get_option( 'cmt_dm_options' ) == '' ) {
                    add_option( 'cmt_dm_options', serialize( $new_options ) );
                }
                else {
                    update_option( 'cmt_dm_options', serialize( $new_options ) );
                }
            }

            if ( isset( $_POST["cmt_action"] ) && $_POST["cmt_action"] == "Add New Domain" ) {


                $this->options['count'] = $this->options['count'] + 1;
                $this->options['domain_' . $this->options['count']] = '';
                $this->options['blogname_' . $this->options['count']] = '';
                $this->options['blogdescription_' . $this->options['count']] = '';
                $this->options['siteurl_' . $this->options['count']] = 'http://';
                $this->options['home_' . $this->options['count']] = 'http://';
                $this->options['analytics_' . $this->options['count']] = '';

                update_option( 'cmt_dm_options', serialize( $this->options ) );
            }

            /* Redirect the post request into get request to use on refresh */

            if ( isset( $_POST["cmt_action"] ) ) {
                $key = "cmt_action";
                if ( $_POST["cmt_action"] == "Save All Changes" ) {
                    $value = "Saved";
                }
                if ( $_POST["cmt_action"] == "Add New Domain" ) {
                    $value = "Blank";
                }
                $_POST['_wp_http_referer'] = add_query_arg( $key, $value, $_POST['_wp_http_referer'] );
            }

            wp_redirect( $_POST['_wp_http_referer'] );
        }


        public function print_meta_box_options( $options ) { ?>
            <table>
                <tr valign="top">
                    <td class="option-label">Google Analytics:</td>
                    <td class="pad">
                        <label>
                            <input type="hidden" id="cmt_g_analytics_enabled" name="cmt_g_analytics_enabled" value="false" />
                            <input type="checkbox" id="cmt_g_analytics_enabled" name="cmt_g_analytics_enabled" value="true" <?php if ( isset( $options['g_analytics_enabled'] ) ) { checked( 'true', $options['g_analytics_enabled'] ); } ?> />
                            Enabled
                        </label>
                        <br />
                        <span class="note">Only check this if you want to use separate analytics codes for each domain. Please deactivate any existing analytics plugins and/or theme options before activating this option.</span>
                    </td>
                </tr>
                <tr valign="top" class="ignore<?php if ( isset( $options['g_analytics_enabled'] ) && $options['g_analytics_enabled'] == 'true' ) {echo ' visible';} else {echo ' invisible';} ?>">
                    <td class="option-label">Ignore Logged in Users:</td>
                    <td class="pad">
                        <label>
                            <input type="hidden" id="cmt_ignore_logged_in" name="cmt_ignore_logged_in" value="false" />
                            <input type="checkbox" id="cmt_ignore_logged_in" name="cmt_ignore_logged_in" value="true" <?php if ( isset( $options['ignore_logged_in'] ) ) { checked( 'true', $options['ignore_logged_in'] ); } ?> />
                            Enabled
                        </label>
                        <br />
                        <span class="note">Allows you to easily remove Google Analytics tracking for logged in users.</span>
                    </td>
                </tr>
                <tr valign="top" class="ignore<?php if ( isset( $options['g_analytics_enabled'] ) && $options['g_analytics_enabled'] == 'true' ) {echo ' visible';} else {echo ' invisible';} ?>">
                    <td class="option-label">Universal Analytics:</td>
                    <td class="pad">
                        <label>
                            <input type="hidden" id="cmt_universal_analytics" name="cmt_universal_analytics" value="false" />
                            <input type="checkbox" id="cmt_universal_analytics" name="cmt_universal_analytics" value="true" <?php if ( isset( $options['universal_analytics'] ) ) { checked( 'true', $options['universal_analytics'] ); } ?> />
                            Enabled
                        </label>
                        <br />
                        <span class="note">Includes the analytics.js JavaScript snippet—a new way to measure how users interact with your website. It is similar to the previous tracking code, ga.js, but offers more flexibility for developers to customize their implementations.</span>
                    </td>
                </tr>
            </table>
        <?php }


        public function print_meta_box_contents( $options, $count ) {
            $count = $count['args']; ?>
            <div class="domain">
            <table class="optiontable">
                <tr>
                    <th scope="row">Domain:</th>
                    <td>www.<input type="text" id="cmt_domain_<?php echo $count; ?>" class="domain" name="cmt_domain_<?php echo $count; ?>" value="<?php print $options['domain_' . $count]; ?>" size="46" /></td>
                </tr><tr>
                    <th scope="row">Weblog title:</th>
                    <td><input type="text" id="cmt_blogname_<?php echo $count; ?>" class="blogname" name="cmt_blogname_<?php echo $count; ?>" value="<?php print $options['blogname_' . $count]; ?>" size="50" /></td>
                </tr><tr>
                    <th scope="row">Tagline:</th>
                    <td><input type="text" id="cmt_blogdescription_<?php echo $count; ?>" class="blogdescription" name="cmt_blogdescription_<?php echo $count; ?>" value="<?php print $options['blogdescription_' . $count]; ?>" size="50" /></td>
                </tr><tr>
                    <th scope="row">Wordpress address (URL):</th>
                    <td><input type="text" id="cmt_siteurl_<?php echo $count; ?>" class="siteurl" name="cmt_siteurl_<?php echo $count; ?>" value="<?php print $options['siteurl_' . $count]; ?>" size="50" /></td>
                </tr><tr>
                    <th scope="row">Blog address (URL):</th>
                    <td><input type="text" id="cmt_home_<?php echo $count; ?>" class="home" name="cmt_home_<?php echo $count; ?>" value="<?php print $options['home_' . $count]; ?>" size="50" /></td>
                </tr><tr class="ga<?php if ( isset( $options['g_analytics_enabled'] ) && $options['g_analytics_enabled'] == 'true' ) {echo ' visible';} else {echo ' invisible';} ?>">
                    <th scope="row">Google Analytics Code:</th>
                    <td><input type="text" id="cmt_analytics_<?php echo $count; ?>" class="analytics" name="cmt_analytics_<?php echo $count; ?>" value="<?php print $options['analytics_' . $count]; ?>" size="50" /></td>
                </tr>
            </table>
            <br /><br />
            <span class="submit"><input type="button" class="button clearAll" value="Clear All Fields" /></span>
            <span class="submit"><input type="button" class="button" onclick="void(document.getElementById( 'domain-<?php echo $count; ?>' ).parentNode.removeChild(document.getElementById( 'domain-<?php echo $count; ?>' )));" value="Delete Domain" /></span>
            <span class="submit"><input type="button" class="button getCurrent" value="Get Current Domain" /></span>
            <br /><br />
            </div>
        <?php }
    }
}


?>