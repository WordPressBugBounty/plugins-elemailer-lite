<?php

namespace Elemailer_Lite\Integrations\Elementor\Widgets;

defined('ABSPATH') || exit;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Utils;

/**
 * image box widget class for registering image box widget
 *
 * @author elEmailer 
 * @since 1.0.0
 */
class Elemailer_Widget_Image_Box extends Widget_Base
{

	public function get_name()
	{
		return 'elemailer-image-box';
	}

	public function get_title()
	{
		return esc_html__('Image Box', 'elemailer-lite');
	}

	public function get_icon()
	{
		return 'eicon-image-box';
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
			'void_email_title',
			[
				'label' => __('Title', 'elemailer-lite'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __('Enter your title', 'elemailer-lite'),
				'default' => __('Image Title', 'elemailer-lite'),
			]
		);

		$this->add_control(
			'void_email_description',
			[
				'label' => __('Description', 'elemailer-lite'),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __('Enter your description', 'elemailer-lite'),
				'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed commodo tellus vitae cursus.', 'elemailer-lite'),
			]
		);

		$this->add_control(
			'image_position',
			[
				'label' => __('Image Position', 'elemailer-lite'),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'top',
				'options' => [
					'left' => [
						'title' => __('Left', 'elemailer-lite'),
						'icon' => 'eicon-h-align-left',
					],
					'top' => [
						'title' => __('Top', 'elemailer-lite'),
						'icon' => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __('Right', 'elemailer-lite'),
						'icon' => 'eicon-h-align-right',
					],
				],

				'toggle' => false,
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
				'default' => 'center',
				'toggle' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'image_section',
			[
				'label' => esc_html__('Image', 'elemailer-lite'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'width',
			[
				'label' => __('Width (%)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '30',
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
			'border_radius',
			[
				'label' => __('Border Radius (%)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '0',
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
			'image_spacing',
			[
				'label' => __('Spacing (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '10',
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

		$this->end_controls_section();

		$this->start_controls_section(
			'title_section',
			[
				'label' => esc_html__('Title', 'elemailer-lite'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __('Color', 'elemailer-lite'),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [
                    'active' => false,
                ],
                'global' => [
                    'active' => false,
                ],
				'default' => '#6EC1E4',
			]
		);

		$this->add_control(
			'title_font_size',
			[
				'label' => __('Font Size (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'size_units' => ['px'],
				'range' => [

					'px' => [
						'min' => 0,
						'max' => 100,
					],

				],
			]
		);

		$this->add_control(
			'title_spacing_top',
			[
				'label' => __('Top Spacing (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '0',
				],
				'size_units' => ['px'],
				'range' => [

					'px' => [
						'min' => 0,
						'max' => 100,
					],

				],
			]
		);

		$this->add_control(
			'title_spacing_bottom',
			[
				'label' => __('Bottom Spacing (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '0',
				],
				'size_units' => ['px'],
				'range' => [

					'px' => [
						'min' => 0,
						'max' => 100,
					],

				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'description_section',
			[
				'label' => esc_html__('Description', 'elemailer-lite'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'p_color',
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
			'description_font_size',
			[
				'label' => __('Font Size (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'size_units' => ['px'],
				'range' => [

					'px' => [
						'min' => 0,
						'max' => 100,
					],

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

		$void_email_image = ($settings['void_email_image']['url'] != '') ? $settings['void_email_image']['url'] : '';
		$void_email_image_alt = \Elementor\Control_Media::get_image_alt( $settings['void_email_image'] );
		$void_email_image_title = \Elementor\Control_Media::get_image_title( $settings['void_email_image'] );


		$email_image_text_title = $settings['void_email_title'];
		$email_image_text_description = $settings['void_email_description'];


		$image_position = isset($settings['image_position']) ? $settings['image_position'] : 'center';
		$alignment = isset($settings['text_align']) ? $settings['text_align'] : 'center';

		$image_width = (($settings['width']['size'] ? $settings['width']['size'] : '100') . ($settings['width']['unit'] ? $settings['width']['unit'] : '%'));
		$image_border_radius = (($settings['border_radius']['size'] ? $settings['border_radius']['size'] : '0') . ($settings['border_radius']['unit'] ? $settings['border_radius']['unit'] : '%'));
		$image_spacing = (($settings['image_spacing']['size'] ? $settings['image_spacing']['size'] : '10') . ($settings['image_spacing']['unit'] ? $settings['image_spacing']['unit'] : 'px'));

		$text_content_width = (100 - ((int) rtrim($image_width, "%")));
		$image_position_css = '';
		$image_title_text_position_css = '';

		if ($image_position == 'left') {
			$image_position_css = 'float: left;';
			$image_title_text_position_css = 'width:' . $text_content_width . '%;margin-right: 0;margin-left: auto;';
			$image_gap = 'margin-left:' . $image_spacing . ';';
		}
		if ($image_position == 'top') {
			$image_gap = 'margin-top:' . $image_spacing . ';';
		}
		if ($image_position == 'right') {
			$image_position_css = 'float: right;';
			$image_title_text_position_css = 'width:' . $text_content_width . '%;margin-right: auto;margin-left: 0;';
			$image_gap = 'margin-right:' . $image_spacing . ';';
		}

		$image_styles = 'border-radius: ' . $image_border_radius . ';';
		$image_styles .= $image_position_css;
		$image_styles .= 'margin: 0 auto;';
		$image_styles .= $image_title_text_position_css;
		$image_styles .= 'width: ' . $image_width . ';';
		$image_styles .= 'max-width: 100%;display: inline-block;';

		$title_styles = 'padding-bottom: ' . (($settings['title_spacing_bottom']['size'] != '') ? $settings['title_spacing_bottom']['size'] : 0) . 'px;padding-top: ' . (($settings['title_spacing_top']['size'] != '') ? $settings['title_spacing_top']['size'] : 0) . 'px;';
		$title_styles .= 'padding-left: 0px; padding-right: 0px; margin: 0 auto;display: block;line-height: initial;font-weight: 600;font-family: Arial,Helvetica,sans-serif;';
		$title_styles .= 'color: ' . (($settings['color'] != '') ? $settings['color'] : '#000') . ';';
		$title_styles .= 'font-size: ' . ((($settings['title_font_size']['size'] != '') ? $settings['title_font_size']['size'] : '22') . (($settings['title_font_size']['unit'] != '') ? $settings['title_font_size']['unit'] : 'px')) . ';';
		$title_styles .= $image_gap;

		$description_styles = 'padding-top:0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; font-weight: 400;margin: 5px auto;line-height: initial;font-family: Arial,Helvetica,sans-serif;display:block;';
		$description_styles .= 'color: ' . ($settings['p_color'] ? $settings['p_color'] : '#000') . ';';
		$description_styles .= 'font-size: ' . (($settings['description_font_size']['size'] ? $settings['description_font_size']['size'] : '14') . ($settings['description_font_size']['unit'] ? $settings['description_font_size']['unit'] : 'px')) . ';';
		$description_styles .= ' margin-top:0px;';
		$description_styles .= $image_gap;

		$advance_style = 'background: ' . (($settings['advance_background_type'] == 'color') ? (($settings['advance_background_color'] != '') ? $settings['advance_background_color'] . ';' : '#0000;') : 'url("' . esc_url($settings['advance_background_image']['url']) . '") no-repeat fixed center;');
		$advance_style .= ' margin: ' . (($settings['advance_margin']['top'] != '') ? $settings['advance_margin']['top'] . 'px ' . $settings['advance_margin']['right'] . 'px ' . $settings['advance_margin']['bottom'] . 'px ' . $settings['advance_margin']['left'] . 'px;' : '0px 0px 0px 0px;');
		$advance_style .= ' padding: ' . (($settings['advance_padding']['top'] != '') ? $settings['advance_padding']['top'] . 'px ' . $settings['advance_padding']['right'] . 'px ' . $settings['advance_padding']['bottom'] . 'px ' . $settings['advance_padding']['left'] . 'px;' : '0px 0px 0px 0px;');


?>
		<div style="text-align: <?php echo esc_attr($alignment); ?>; <?php echo esc_attr($advance_style); ?> " class="void-email-image-box">
			<img width="<?php echo $image_width; ?>" style="<?php echo esc_attr($image_styles); ?>" src="<?php echo esc_url($void_email_image) ?>" alt="<?php echo $void_email_image_alt; ?>" title="<?php echo $void_email_image_title; ?>">

			<div style="<?php echo esc_attr($image_title_text_position_css); ?>text-align: <?php echo esc_attr($alignment); ?>;" class="ib-content">
				<h3 style="<?php echo esc_attr($title_styles); ?>"><?php echo esc_html($email_image_text_title); ?></h3>
				<p style="<?php echo esc_attr($description_styles); ?>"><?php echo esc_html($email_image_text_description); ?></p>
			</div>
		</div>

<?php
	}
}
