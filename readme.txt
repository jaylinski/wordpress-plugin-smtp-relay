=== SMTP Relay ===
Contributors: jakobword
Tags: smtp, mail, relay
Requires at least: 4.9
Tested up to: 4.9
Stable tag: 0.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

SMTP Relay lets you configure a SMTP relay for outgoing emails.

== Description ==

Enter a host, port, from name and from address and send emails via a SMTP relay without having to use the native
PHP `mail()` function.

This is especially useful if the `mail()` function is not configured on your system but a SMTP relay is available.

This plugin only works with unauthenticated SMTP relays!

== Installation ==

Upload the SMTP Relay plugin to your blog, activate it, then configure it.

== Screenshots ==

1. Admin Interface

== Changelog ==

= 0.1.1 =
*Release Date - 2018-08-08*

* Include script only on settings page

= 0.1.0 =
*Release Date - 2018-07-30*

* Initial version
