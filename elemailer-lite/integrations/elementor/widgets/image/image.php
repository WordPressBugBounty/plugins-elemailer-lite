<?php

namespace Elemailer_Lite\Integrations\Elementor\Widgets;

defined('ABSPATH') || exit;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Utils;

/**
 * image widget class for registering image widget
 *
 * @author elEmailer 
 * @since 1.0.0
 */
class Elemailer_Widget_Image extends Widget_Base
{

	public function get_name()
	{
		return 'elemailer-image';
	}

	public function get_title()
	{
		return esc_html__('Image', 'elemailer-lite');
	}

	public function get_icon()
	{
		return 'eicon-image';
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
		return ['void', 'template', 'image'];
	}

	protected function register_controls()
	{

		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Image Content', 'elemailer-lite'),
			]
		);

		$this->add_control(
			'void_email_image',
			[
				'label' => __('Choose Image', 'elemailer-lite'),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'width',
			[
				'label' => __('Width (%)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '100',
				],
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],

				],
			]
		);

		$this->add_control(
			'text_align',
			[
				'label' => __('Alignment', 'elemailer-lite'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'elemailer-lite'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'elemailer-lite'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'elemailer-lite'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
			]
		);

		$this->add_control(
			'image_link_type',
			[
				'label' => __('Link', 'elemailer-lite'),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => __('None', 'elemailer-lite'),
					'custom_url' => __('Custom URL', 'elemailer-lite'),
				],
			]
		);

		$this->add_control(
			'image_link',
			[
				'label' => __('Custom URL', 'elemailer-lite'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'elemailer-lite'),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
				'condition' => [
					'image_link_type' => 'custom_url',
				],
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

		$void_email_image = isset($settings['void_email_image']['url']) ? $settings['void_email_image']['url'] : '';
		$void_email_image_alt = \Elementor\Control_Media::get_image_alt( $settings['void_email_image'] );
		$void_email_image_title = \Elementor\Control_Media::get_image_title( $settings['void_email_image'] );


		$alignment = isset($settings['text_align']) ? $settings['text_align'] : 'left';

		$image_link_type = (($settings['image_link_type'] != '') ? $settings['image_link_type'] : 'none');
		$image_link = (isset($settings['image_link']['url']) ? $settings['image_link']['url'] : '');

		$image_styles  = 'margin: 0 auto;display: inline-block;';
		$image_styles .= 'width: ' . (($settings['width']['size'] ? $settings['width']['size'] : '100') . ($settings['width']['unit'] ? $settings['width']['unit'] : '%')) . ';';
		$image_styles .= 'max-width: 100%;';

		$advance_style = 'background: ' . (($settings['advance_background_type'] == 'color') ? (($settings['advance_background_color'] != '') ? $settings['advance_background_color'] . ';' : '#0000;') : 'url("' . esc_url($settings['advance_background_image']['url']) . '") no-repeat fixed center;');
		$advance_style .= ' margin: ' . (($settings['advance_margin']['top'] != '') ? $settings['advance_margin']['top'] . 'px ' . $settings['advance_margin']['right'] . 'px ' . $settings['advance_margin']['bottom'] . 'px ' . $settings['advance_margin']['left'] . 'px;' : '0px 0px 0px 0px;');
		$advance_style .= ' padding: ' . (($settings['advance_padding']['top'] != '') ? $settings['advance_padding']['top'] . 'px ' . $settings['advance_padding']['right'] . 'px ' . $settings['advance_padding']['bottom'] . 'px ' . $settings['advance_padding']['left'] . 'px;' : '0px 0px 0px 0px;');

?>

		<div style=" text-align: <?php echo esc_attr($alignment); ?>; <?php echo esc_attr($advance_style); ?>;" class="void-email-image">
			<?php if ($image_link_type == 'custom_url') : ?>
				<a href="<?php echo esc_url(do_shortcode($image_link)); ?>">
				<?php endif; ?>
				<img width="<?php echo ($settings['width']['size'] ? $settings['width']['size'] : '600'); ?>%" style="<?php echo esc_attr($image_styles); ?>" src="<?php echo esc_url($void_email_image) ?>" alt="<?php echo $void_email_image_alt; ?>" title="<?php echo $void_email_image_title; ?>" >
				<?php if ($image_link_type == 'custom_url') : ?>
				</a>
			<?php endif; ?>
		</div>
<?php
	}
}
