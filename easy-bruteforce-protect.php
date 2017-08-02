<?php
/**
 * Plugin Name: Easy Bruteforce Protect
 * Plugin URI: http://www.buyhttp.com/wordpress_plugins.html
 * Description: Adds code to your .htaccess file to protect from simple brute force attacks. Deactivate to disable protection.
 * Version: 1.0.0
 * Author: BuyHTTP
 * Author URI: http://www.buyhttp.com
 * License: GPL
 */

defined('ABSPATH') or die("No script kiddies please!");

register_activation_hook( __FILE__, 'easfbfpActivate' );
register_deactivation_hook( __FILE__, 'easfbfpDeactivate' );


function easfbfpActivate()
{
	$home_path = get_home_path();
	$parsed_url = parse_url(site_url());
	if ( ( ! file_exists( $home_path.'.htaccess' ) && is_writable( $home_path ) ) || is_writable( $home_path . '.htaccess' ) ) {
		// We can make our changes
		if(file_exists( $home_path.'.htaccess' )){
			// Edit File
			$lines = file($home_path.'.htaccess');
			$lines[] = "\n";
			$lines[] = "# BEGIN EASYBFP\n";
			$lines[] = "<IfModule mod_rewrite.c>\n";
			$lines[] = "RewriteEngine on\n";
			if(!empty($parsed_url['path'])){
				$lines[] = "RewriteBase ".$parsed_url['path']."/\n";
			}
			$lines[] = "RewriteCond %{REQUEST_METHOD} POST\n";
			$lines[] = "RewriteCond %{HTTP_REFERER} !^".$parsed_url['scheme']."://".str_replace('.', '\.', $parsed_url['host'])." [NC]\n";
			$lines[] = "RewriteCond %{REQUEST_URI} ^(.*)?wp-login\.php(.*)$ [OR]\n";
			$lines[] = "RewriteCond %{REQUEST_URI} ^(.*)?wp-admin$\n";
			$lines[] = "RewriteRule ^(.*)$ - [F]\n";
			$lines[] = "</IfModule>\n";
			$lines[] = "# END EASYBFP\n";
			
			$fp = fopen($home_path.'.htaccess', 'w');
			foreach($lines as $line){
				fwrite($fp, "$line");
			}
			fclose($fp);
		} else {
			// New File
			$fp = fopen($home_path.'.htaccess','w');
			fwrite($fp, "# BEGIN EASYBFP\n");
			fwrite($fp,"<IfModule mod_rewrite.c>\n");
			fwrite($fp,"RewriteEngine on\n");
			if(!empty($parsed_url['path'])){
				fwrite($fp,"RewriteBase ".$parsed_url['path']."/\n");
			}
			fwrite($fp,"RewriteCond %{REQUEST_METHOD} POST\n");
			fwrite($fp,"RewriteCond %{HTTP_REFERER} !^".$parsed_url['scheme']."://".str_replace('.', '\.', $parsed_url['host'])." [NC]\n");
			fwrite($fp,"RewriteCond %{REQUEST_URI} ^(.*)?wp-login\.php(.*)$ [OR]\n");
			fwrite($fp,"RewriteCond %{REQUEST_URI} ^(.*)?wp-admin$\n");
			fwrite($fp,"RewriteRule ^(.*)$ - [F]\n");
			fwrite($fp,"</IfModule>\n");
			fwrite($fp, "# END EASYBFP\n");
			fclose($fp);
		}
	} else {
		// Not writable
		wp_die(_e('Your .htaccess file or root WordPress directory is not writable'));
	}
}

function easfbfpDeactivate()
{
	$home_path = get_home_path();
	$parsed_url = parse_url(site_url());
	if ( is_writable( $home_path.'.htaccess' ) ) {
		// We can make our changes
		$file = file_get_contents($home_path.'.htaccess');
		$file =  deleteEasybfp('# BEGIN EASYBFP', '# END EASYBFP', $file);
		
		$fp = fopen($home_path.'.htaccess', 'w');
		fwrite($fp, $file);
		fclose($fp);
	} else {
		// Not writable
		wp_die(_e('Your .htaccess file is not writable or doesn\'t exist.'));
	}
}

function deleteEasybfp($start, $end, $string)
{
  $beginningPos = strpos($string, $start);
  $endPos = strpos($string, $end);
  if ($beginningPos === false || $endPos === false) {
    return $string;
  }

  $delete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

  return str_replace($delete, '', $string);
}