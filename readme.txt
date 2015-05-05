=== Multiple Domains with Analytics ===

Contributors:      dpcasady
Tags:              domains, multiple, mirror, multiple domains, multiple analytics, multiple domains with analytics, multiple google analytics
Requires at least: 2.8
Tested up to:      4.2.1
Stable tag:        1.1.3
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

The Multiple Domains with Analytics plugin allows Wordpress to be mirrored across multiple domain names. This requires your DNS settings to be configured ahead of time so that all of your domain names point to the same location. While configuring DNS settings in this way will point all visitors to the same site regardless of domain name, any interaction on the page (clicking an internal link) will revert back to the base domain name that exists in the Wordpress general options. This plugin allows the domain name that was initially used by the visitor to stay constant as well as display a customizeable Name and Tagline for the duration of their visit. In addition, this plugin can include separate Google Analytics codes to track the traffic to each domain.

Full list of features:

* Unlimited number of mirrored domains.
* Customization for Site Title, Tagline, Analytics Code and URL for each domain.
* Can be used with or without another Google Analytics plugin, it's your choice.
* If enabled, this plugin uses the asynchronous Google Analytics tracking code, the fastest and most reliable tracking code Google Analytics offers.
* Option to ignore logged in users when Google Analytics is enabled.

== Installation ==

This section describes how to install the plugin and get it working.

1. Make sure that your DNS settings are configured correctly so that all of your domains are pointing to the same location.
2. Upload `cmt-multiple-domains` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Go to the options panel under the 'Settings' menu and add your list of domains and optional Google Analytics codes.

== Changelog ==

= 1.1.3 =
* Various improvements.
* Addition of universal analytics.

= 1.1.2 =
* Performance enhancements.

= 1.1.1 =
* Minor bug fixes.
* Updated options page.

= 1.1 =
* First public release.