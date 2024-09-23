<?php

namespace Elemailer_Lite\Integrations\Elementor\Widgets;

defined('ABSPATH') || exit;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Utils;


/**
 * video widget class for registering video widget
 *
 * @author elEmailer 
 * @since 1.0.0
 */
class Elemailer_Widget_Video extends Widget_Base
{

	public function get_name()
	{
		return 'elemailer-video';
	}

	public function get_title()
	{
		return esc_html__('Video', 'elemailer-lite');
	}

	public function get_icon()
	{
		return 'eicon-youtube';
	}

	public function show_in_panel()
	{
		$post_type = get_post_type();
		return (in_array($post_type, ['em-form-template', 'em-emails-template']));
	}

	public function get_categories()
	{
		return array('elemailer-template-builder-fields');
	}

	public function get_keywords()
	{
		return ['void', 'template', 'video'];
	}

	protected function register_controls()
	{

		$this->start_controls_section(
			'section_video',
			[
				'label' => __('Video Thumbnail', 'elemailer-lite'),
			]
		);

		$this->add_control(
			'video_type',
			[
				'label' => __('Source', 'elemailer-lite'),
				'type' => Controls_Manager::SELECT,
				'default' => 'youtube',
				'options' => [
					'youtube' => __('YouTube', 'elemailer-lite'),
					'vimeo' => __('Vimeo', 'elemailer-lite'),
					'dailymotion' => __('Dailymotion', 'elemailer-lite'),
					'hosted' => __('Self Hosted', 'elemailer-lite'),
				],
			]
		);

		$this->add_control(
			'void_video_thumbnail',
			[
				'label' => __('Choose Image', 'elemailer-lite'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'video_type' => 'hosted',
				]
			]
		);

		$this->add_control(
			'void_video_thumbnail_notice',
			[
				'label' => __('Choose Image', 'elemailer-lite'),
				'type' => Controls_Manager::RAW_HTML,
				'raw' => ('<br>Thumbnail will be automatically generate for this video source.'),
				'condition' => [
					'video_type!' => 'hosted',
				]
			]
		);

		$this->add_control(
			'youtube_video_link',
			[
				'label' => __('Video Url', 'elemailer-lite'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'elemailer-lite'),
				'default' => [
					'url' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				],
				'condition' => [
					'video_type' => 'youtube',
				]
			]
		);

		$this->add_control(
			'dailymotion_video_link',
			[
				'label' => __('Video Url', 'elemailer-lite'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'elemailer-lite'),
				'default' => [
					'url' => 'https://www.dailymotion.com/video/x6tqhqb',
				],
				'condition' => [
					'video_type' => 'dailymotion',
				]
			]
		);

		$this->add_control(
			'vimeo_video_link',
			[
				'label' => __('Video Url', 'elemailer-lite'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'elemailer-lite'),
				'default' => [
					'url' => 'https://vimeo.com/235215203',
				],
				'condition' => [
					'video_type' => 'vimeo',
				]
			]
		);

		$this->add_control(
			'hosted_video_link',
			[
				'label' => __('Video Url', 'elemailer-lite'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'elemailer-lite'),
				'condition' => [
					'video_type' => 'hosted',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'advanced_section',
			[
				'label' => esc_html__('Advanced Style', 'elemailer-lite'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'advance_margin',
			[
				'label' => __('Margin (px)', 'elemailer-lite'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default' => [
					'top' => '10',
					'right' => '10',
					'bottom' => '10',
					'left' => '10',
					'isLinked' => true,
				],
			]
		);

		$this->add_control(
			'advance_padding',
			[
				'label' => __('Padding (px)', 'elemailer-lite'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
			]
		);

		$this->add_control(
			'advance_background_type',
			[
				'label' => __('Background Type', 'elemailer-lite'),
				'type' => Controls_Manager::SELECT,
				'default' => 'color',
				'options' => [
					'color'  => __('Color', 'elemailer-lite'),
					'image' => __('Image', 'elemailer-lite'),
				],
			]
		);

		$this->add_control(
			'advance_background_color',
			[
				'label' => __('Background Color', 'elemailer-lite'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'advance_background_type' => 'color',
				],
				'dynamic' => [
                    'active' => false,
                ],
                'global' => [
                    'active' => false,
                ],
			]
		);

		$this->add_control(
			'advance_background_image',
			[
				'label' => __('Choose Image', 'elemailer-lite'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'advance_background_type' => 'image',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render($instance = [])
	{
		$settings = $this->get_settings_for_display();

		$void_video_thumbnail = '';
		$email_video_url = '';
		$play_icon = '';

		$video_type = (($settings['video_type'] != '') ? $settings['video_type'] : 'youtube');

		switch ($video_type) {
			case 'youtube':
				$email_video_url = isset($settings['youtube_video_link']['url']) ? $settings['youtube_video_link']['url'] : '';
				parse_str(parse_url($email_video_url, PHP_URL_QUERY), $url_params);
				$void_video_thumbnail = isset($url_params['v']) ? 'https://img.youtube.com/vi/' . $url_params['v'] . '/hqdefault.jpg' : 'https://img.youtube.com/vi/XHOmBV4js_E/hqdefault.jpg';
				$play_icon = plugin_dir_url(__FILE__) . 'assets/img/youtube-play-icon.png';
				break;
			case 'vimeo':
				$email_video_url = isset($settings['vimeo_video_link']['url']) ? $settings['vimeo_video_link']['url'] : '';
				$vid = substr($email_video_url, 18, 9);
				$vid = (strlen($vid) == 9) ? $vid : '235215203';
				$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$vid.php"));
				$void_video_thumbnail = isset($hash[0]['thumbnail_large']) ? $hash[0]['thumbnail_large'] : $hash[0]['thumbnail'];
				$play_icon = plugin_dir_url(__FILE__) . 'assets/img/vimeo-play-icon.png';
				break;
			case 'dailymotion':
				$email_video_url = isset($settings['dailymotion_video_link']['url']) ? $settings['dailymotion_video_link']['url'] : '';
				$void_video_thumbnail = str_replace('https://www.dailymotion.com/video', 'https://www.dailymotion.com/thumbnail/video', $email_video_url);
				$play_icon = plugin_dir_url(__FILE__) . 'assets/img/dailymotion-play-icon.png';
				break;
			case 'hosted':
				$email_video_url = isset($settings['hosted_video_link']['url']) ? $settings['hosted_video_link']['url'] : '';
				$void_video_thumbnail = $void_video_thumbnail = isset($settings['void_video_thumbnail']['url']) ? $settings['void_video_thumbnail']['url'] : '';
				$play_icon = plugin_dir_url(__FILE__) . 'assets/img/hosted-play-icon.png';
				break;
			default:
				break;
		}

		$advance_style = 'background: ' . (($settings['advance_background_type'] == 'color') ? (($settings['advance_background_color'] != '') ? $settings['advance_background_color'] . ';' : '#0000;') : 'url("' . esc_url($settings['advance_background_image']['url']) . '") no-repeat fixed center;');
		$advance_style .= ' margin: ' . (($settings['advance_margin']['top'] != '') ? $settings['advance_margin']['top'] . 'px ' . $settings['advance_margin']['right'] . 'px ' . $settings['advance_margin']['bottom'] . 'px ' . $settings['advance_margin']['left'] . 'px;' : '0px 0px 0px 0px;');
		$advance_style .= ' padding: ' . (($settings['advance_padding']['top'] != '') ? $settings['advance_padding']['top'] . 'px ' . $settings['advance_padding']['right'] . 'px ' . $settings['advance_padding']['bottom'] . 'px ' . $settings['advance_padding']['left'] . 'px;' : '0px 0px 0px 0px;');

?>
		<div style=" text-align: left; <?php echo esc_attr($advance_style); ?>" class="void-email-video-wrapper">
			<a style="display: block;" href="<?php echo esc_url($email_video_url); ?>">
				<div style="background-image: url('<?php echo esc_url($void_video_thumbnail) ?>');background-size: 100% 100%;">
					<img class="elemailer-play-icon" src="<?php echo esc_url($play_icon); ?>" alt="">
				</div>
			</a>
		</div>
<?php
	}
}
