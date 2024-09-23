<?php

namespace Elemailer_Lite\Integrations\Elementor\Widgets;

defined('ABSPATH') || exit;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Utils;

/**
 * divider widget class for registering divider widget
 *
 * @author elEmailer 
 * @since 1.0.0
 */
class Elemailer_Widget_Divider extends Widget_Base
{

	public function get_name()
	{
		return 'elemailer-divider';
	}

	public function get_title()
	{
		return esc_html__('Divider', 'elemailer-lite');
	}

	public function get_icon()
	{
		return 'eicon-divider';
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
		return ['void', 'template', 'divider'];
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Divider', 'elemailer-lite'),
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label' => __('Divider Style', 'elemailer-lite'),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => __('Solid', 'elemailer-lite'),
					'dashed' => __('Dashed', 'elemailer-lite'),
					'dotted' => __('Dotted', 'elemailer-lite'),
					'double' => __('Double', 'elemailer-lite'),

				],
			]
		);

		$this->add_control(
			'void_divider',
			[
				'label' => __('Color', 'elemailer-lite'),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [
                    'active' => false,
                ],
                'global' => [
                    'active' => false,
                ],
				'default' => '#000',

			]
		);

		$this->add_control(
			'void_divider_width',

			[
				'label' => __('Width (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '1',
				],
				'size_units' => ['px'],
				'range' => [

					'px' => [
						'min' => 1,
						'max' => 100,
					],

				],
			]
		);

		$this->add_control(
			'void_divider_gap',

			[
				'label' => __('Gap (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [

					'px' => [
						'min' => 1,
						'max' => 100,
					],

				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
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


		$divider_styles  = 'margin:0px; border-top-width: 0px;border-left-width: 0px;border-right-width: 0px;display: block;width: 100%;';
		$divider_styles .= 'border-style: ' . (($settings['divider_style'] != '') ? $settings['divider_style'] : 'solid') . ';';
		$divider_styles .= 'border-bottom-color: ' . (($settings['void_divider'] != '') ? $settings['void_divider'] : '#000') . ';';
		$divider_styles .= 'border-bottom-width: ' . (($settings['void_divider_width']['size'] != '') ? $settings['void_divider_width']['size'] : '1') . 'px;';
		$divider_styles .= 'margin-top: ' . (($settings['void_divider_gap']['size'] != '') ? $settings['void_divider_gap']['size'] : '10') . 'px;';
		$divider_styles .= 'margin-bottom: ' . (($settings['void_divider_gap']['size'] != '') ? $settings['void_divider_gap']['size'] : '10') . 'px;';

		$advance_style = 'background: ' . (($settings['advance_background_type'] == 'color') ? (($settings['advance_background_color'] != '') ? $settings['advance_background_color'] . ';' : '#0000;') : 'url("' . esc_url($settings['advance_background_image']['url']) . '") no-repeat fixed center;');
		$advance_style .= ' margin: ' . (($settings['advance_margin']['top'] != '') ? $settings['advance_margin']['top'] . 'px ' . $settings['advance_margin']['right'] . 'px ' . $settings['advance_margin']['bottom'] . 'px ' . $settings['advance_margin']['left'] . 'px;' : '0px 0px 0px 0px;');
		$advance_style .= ' padding: ' . (($settings['advance_padding']['top'] != '') ? $settings['advance_padding']['top'] . 'px ' . $settings['advance_padding']['right'] . 'px ' . $settings['advance_padding']['bottom'] . 'px ' . $settings['advance_padding']['left'] . 'px;' : '0px 0px 0px 0px;');

?>

		<div style="width: 100%;<?php echo esc_attr($advance_style); ?>" class="void-divider">
			<span style="<?php echo esc_attr($divider_styles); ?>" class="divider-bar"></span>
		</div>
<?php
	}
}
