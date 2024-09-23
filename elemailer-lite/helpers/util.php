<?php

namespace Elemailer_Lite\Helpers;

require_once __DIR__ . '/vendor/autoload.php';

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

defined('ABSPATH') || exit;

/**
 * Global helper class
 * used to make some verious type method
 * 
 * @author elEmailer
 * @since 1.0.0
 */
class Util
{

	/**
	 * Get elemailer lite older version if has any.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public static function old_version()
	{
		$info = get_option('elemailer_lite_info');
		$version = isset($info['version']) ? $info['version'] : '';
		return '' == $version ? -1 : $version;
	}

	/**
	 * custom wp_kses function with parameters for escaping easily
	 *
	 * @param [string] $raw
	 * @return void
	 * @since 1.0.0
	 */
	public static function kses($raw)
	{

		$allowed_tags = array(
			'a'								 => array(
				'class'	 => array(),
				'href'	 => array(),
				'rel'	 => array(),
				'title'	 => array(),
				'target' => array(),
				'style'  => array(),
			),
			'abbr'							 => array(
				'title' => array(),
				'style' => array(),
			),
			'b'								 => array(),
			'blockquote'					 => array(
				'cite'  => array(),
				'style' => array(),
			),
			'cite'							 => array(
				'title' => array(),
				'style' => array(),
			),
			'code'							 => array(),
			'del'							 => array(
				'datetime'	 => array(),
				'title'		 => array(),
				'style' 	 => array(),
			),
			'dd'							 => array(),
			'div'							 => array(
				'class'	 => array(),
				'title'	 => array(),
				'style'	 => array(),
			),
			'dl'							 => array(),
			'dt'							 => array(),
			'em'							 => array(),
			'h1'							 => array(
				'class' => array(),
				'style' => array(),
			),
			'h2'							 => array(
				'class' => array(),
				'style' => array(),
			),
			'h3'							 => array(
				'class' => array(),
				'style' => array(),
			),
			'h4'							 => array(
				'class' => array(),
				'style' => array(),
			),
			'h5'							 => array(
				'class' => array(),
				'style' => array(),
			),
			'h6'							 => array(
				'class' => array(),
				'style' => array(),
			),
			'i'								 => array(
				'class' => array(),
				'style' => array(),
			),
			'img'							 => array(
				'alt'	 => array(),
				'class'	 => array(),
				'height' => array(),
				'src'	 => array(),
				'width'	 => array(),
				'style'  => array(),
			),
			'li'							 => array(
				'class' => array(),
				'style' => array(),
			),
			'ol'							 => array(
				'class' => array(),
				'style' => array(),
			),
			'p'								 => array(
				'class' => array(),
				'style' => array(),
			),
			'q'								 => array(
				'cite'	 => array(),
				'title'	 => array(),
				'style'	 => array(),
			),
			'span'							 => array(
				'class'	 => array(),
				'title'	 => array(),
				'style'	 => array(),
			),
			'iframe'						 => array(
				'width'			 => array(),
				'height'		 => array(),
				'scrolling'		 => array(),
				'frameborder'	 => array(),
				'allow'			 => array(),
				'src'			 => array(),
				'style' 		 => array(),
			),
			'strike'						 => array(),
			'br'							 => array(),
			'strong'						 => array(),
			'data-wow-duration'				 => array(),
			'data-wow-delay'				 => array(),
			'data-wallpaper-options'		 => array(),
			'data-stellar-background-ratio'	 => array(),
			'ul'							 => array(
				'class' => array(),
				'style' => array(),
			),
		);

		// add whitelist css property 'display' in wp_kses function
		add_filter('safe_style_css', function ($styles) {
			$styles[] = 'display';
			return $styles;
		});

		if (function_exists('wp_kses')) { // WP is here
			return wp_kses($raw, $allowed_tags);
		} else {
			return $raw;
		}
	}

	/**
	 * get client ip function
	 *
	 * @return string[ip]
	 * @since 1.0.1
	 */
	public static function get_client_ip()
	{
		$server_ip_keys = [
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		];

		foreach ($server_ip_keys as $key) {
			if (isset($_SERVER[$key]) && filter_var($_SERVER[$key], FILTER_VALIDATE_IP)) {
				return $_SERVER[$key];
			}
		}

		// Fallback local ip.
		return '127.0.0.1';
	}

	/**
	 * render elementor content by using content id function
	 *
	 * @param int[id] $content_id
	 * @param boolean $style
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function render_elementor_content($content_id, $style = false)
	{
		$elementor_instance = \Elementor\Plugin::instance();
		return $elementor_instance->frontend->get_builder_content_for_display($content_id, $style);
	}

	/**
	 * dynamically add url param function
	 * mainly used to overcome permalink structure
	 *
	 * @param string[url] $url
	 * @param string[key] $key
	 * @param string[value] $value
	 * @return void
	 * @since 1.0.0
	 */
	public static function add_param_url($url, $key, $value)
	{

		$url = preg_replace('/(.*)(?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
		$url = substr($url, 0, -1);

		if (strpos($url, '?') === false) {
			return ($url . '?' . $key . '=' . $value);
		} else {
			return ($url . '&' . $key . '=' . $value);
		}
	}

	/**
	 * get all post type
	 * includes wp raw post types and all cpt
	 * used for selecting post type in post widgets
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function get_all_post_type()
	{
		$args = array(
			'public'	=> 'true',
		);
		$post_types = get_post_types($args, 'objects');
		$return_types = [];

		foreach ($post_types  as $post_type) {
			//remove unnecessary post types
			if ($post_type->name == 'elementor_library' || $post_type->name == 'attachment') {
				continue;
			}
			// set readable name and slug
			$return_types[$post_type->name] = $post_type->labels->singular_name;
		}
		return $return_types;
	}

	/**
	 * get all image size function 
	 * used for selecting image size in post widgets
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function get_all_image_sizes()
	{
		$sizes = [];

		$all_image_sizes = wp_get_registered_image_subsizes();

		foreach ($all_image_sizes as $key => $value) {
			$sizes[$key] = ucwords(str_replace('_', ' ', $key)) . ' - ' . $value['width'] . ' x ' . $value['height'];
		}

		$sizes['full'] = 'Full';

		return $sizes;
	}

	/**
	 * minify css function, write to a file after minified
	 * used for minifying css for sending it to the mail client
	 *
	 * @param string[path] $read_file_path
	 * @param string[path] $write_file_path
	 * @return void
	 * @since 1.0.0
	 */
	public static function minify_css($read_file_path, $write_file_path)
	{
		$source = file_get_contents($read_file_path);
		$css = '<style>';
		$css .= str_replace('; ', ';', str_replace(' }', '}', str_replace('{ ', '{', str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), "", preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $source)))));
		//$css .= '</style>';
		$destination = file_put_contents($write_file_path, $css);
	}

	/**
	 * process email template's dynamic style for sending into email function
	 *
	 * @param integer[number] $template_id
	 *
	 * @return string[css] <style>'.$css.'</style>
	 * @since 1.0.0
	 */
	public static function get_template_styles($template_id)
	{

		$raw_string = self::render_elementor_content($template_id, true);

		$split = preg_split("/<\/style>/", $raw_string);

		$css = (isset($split[0]) ? $split[0] : '');
		/**
		 * remove starting style tag '<style>' to merge this style with other style
		 * replace '.elementor-element.' with '.' for overcome prefixing issue with outlook
		 * replace '}}' with '}' for avoiding extra closing bracket of media query.
		 */
		$css = str_replace(['<style>', '.elementor-element.', '}}'], ['', '.', '}'], $css);

		// remove media query for supporting this style to mail client outlook, yandex
		$css = preg_replace('/@media( )*\((max|min)-width:( )*[0-9]*px\)( )*(and( )*\((max|min)-width:( )*[0-9]*px\)( )*)?{/', '', $css);

		return $css . '</style>';
	}

	/**
	 * process email template into html for sending as email function
	 *
	 * @param integer[number] $template_id
	 *
	 * @return string[html] $data_html
	 * @since 1.0.0
	 */
	public static function get_template_content($template_id)
	{

		$raw_string = self::render_elementor_content($template_id, true);

		$split = preg_split("/<\/style>/", $raw_string);

		$raw_body = str_replace((isset($split[0]) ? $split[0] : '') . '</style>', '', $raw_string);

		return ltrim($raw_body);
	}

	/**
	 * build html email template function after rendering shortcode data from form submission
	 *
	 * @param integer[number] $template_id
	 * @param string[html] $email_content
	 * @return string[html] $data_html
	 * @since 1.0.0
	 */
	public static function get_email_html_template($template_id, $email_content)
	{
		$email_content = str_replace(['<section', '</section>'], ['<div', '</div>'], $email_content);

		ob_start();
?>

		<!doctype html>
		<html>

		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<?php include ELE_MAILER_LITE_PLUGIN_DIR . 'app/form-template/view/default-elementor-style.php'; ?>
			<?php echo self::get_template_styles($template_id); ?>
		</head>

		<body class="elementor-page-<?php echo $template_id; ?> elemailer-send-template">
			<?php echo $email_content; ?>

			<?php $info=get_option('elemailer_lite_info');  // run footer branding only for new users after v-1.0.0
			
			if ( isset( $info['old_user'] ) && ! $info['old_user'] && 'yes' !== get_option( 'elemailer_hide_mail_branding', true ) ) : ?>

		 	<!-- START Branding FOOTER -->
			<div class="elementor-section-wrap" style="text-align: center;padding-top: 40px;">
				<table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: auto;font-size: 14px;">
				<tr>
					<td class="content-block">
					Email designed with Elementor <div style="width: 1em; display:inline-block;">❤️</div> Powered by  <a target="_blank" href="https://elemailer.com?source=inemail">Elemailer</a>                           
					<a target="_blank" href="https://elemailer.com?source=inemail">
						<img src="https://elemailer.com/wp-content/uploads/2020/11/logo.png" style=" display: block; width: 120px; margin: auto; padding: 15px 0px 15px 0px; ">
					</a>
					</td>
				</tr>
				</table>
			</div>
			<!-- END Branding FOOTER -->

    	<?php endif; ?>

		</body>

		</html>

<?php
		$data_html = ob_get_contents();
		ob_end_clean();

		$cssToInlineStyles = new CssToInlineStyles();

		// output
		$output = $cssToInlineStyles->convert( $data_html );

		return $output;
	}
}
