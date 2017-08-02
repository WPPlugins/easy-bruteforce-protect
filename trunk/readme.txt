=== Easy Bruteforce Protect ===
Contributors: buyhttp, keliix06
Tags: security, .htaccess, brute force
Tested up to: 3.9.1
License: GPL
License URI: http://www.gnu.org/copyleft/gpl.html

This plugin modifies your .htaccess file to provide protection from brute force attack bots.

== Description ==
This plugin modifies your .htaccess file to provide protection from brute force attack bots.

== Installation ==
All you need to do is install and activate the plugin. Deactivate to remove protection from the .htaccess file.

== Frequently Asked Questions ==

= What code is added? =

# BEGIN EASYBFP
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteBase INSTALLED_FOLDER
RewriteCond %{REQUEST_METHOD} POST
RewriteCond %{HTTP_REFERER} !^http://YOURSITE.COM [NC]
RewriteCond %{REQUEST_URI} ^(.*)?wp-login\.php(.*)$ [OR]
RewriteCond %{REQUEST_URI} ^(.*)?wp-admin$
RewriteRule ^(.*)$ - [F]
</IfModule>
# END EASYBFP