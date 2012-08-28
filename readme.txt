=== Multiple Domains with Analytics ===

Contributors:      Danny Casady
Tags:              domains, multiple, mirror, multiple domains, multiple analytics, multiple domains with analytics, multiple google analytics
Requires at least: 2.8 
Tested up to:      3.4.1
Stable tag:        1.1

== Description ==

The Multiple Domains with Analytics plugin allows Wordpress to be mirrored across multiple domain names. This requires your DNS settings to be configured so that all of your domain names point to the same location. While configuring DNS settings in this way will point all visitors to the same site regardless of domain name, any interaction on the page (clicking an internal link) will revert back to the base domain name that exists in the Wordpress general options. This plugin allows whichever domain name that was initially used by the visitor to stay constant as well as display a unique Name and Tagline for the duration of their visit. In addition, this plugin can include separate Google Analytics codes for each domain to track the traffic to each domain.

== Installation ==

This section describes how to install the plugin and get it working.

1. Make sure that your DNS settings are configured correctly so that all of your domains are pointing to the same location.
2. Upload `cmt-multiple-domains` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Go to the options panel under the 'Settings' menu and add your list of domains and optional analytics account numbers.

== Changelog ==

= 1.1 =
* First public release.