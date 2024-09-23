<?php

namespace Elemailer_Lite\Integrations\Elementor\Widgets;

defined('ABSPATH') || exit;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Utils;

/**
 * heading widget class for registering heading widget
 *
 * @author elEmailer 
 * @since 1.0.0
 */
class Elemailer_Widget_Heading extends Widget_Base
{

	public function get_name()
	{
		return 'elemailer-heading';
	}

	public function get_title()
	{
		return esc_html__('Heading', 'elemailer-lite');
	}

	public function get_icon()
	{
		return 'eicon-t-letter';
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
		return ['void', 'template', 'heading'];
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_title',
			[
				'label' => __('Title', 'elemailer-lite'),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __('Title', 'elemailer-lite'),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __('Enter your title', 'elemailer-lite'),
				'default' => __('Add Your Heading Text Here', 'elemailer-lite'),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __('Link', 'elemailer-lite'),
				'type' => Controls_Manager::URL,
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'align',
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
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __('Title', 'elemailer-lite'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Color', 'elemailer-lite'),
				'type' => Controls_Manager::COLOR,
				'default' => '#6EC1E4',
				'dynamic' => [
					'active' => false,
				],
				'global' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'heading_font_size',
			[
				'label' => __('Font Size (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '36',
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
			'heading_line_height',
			[
				'label' => __('Line Height (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '44',
				],
				'size_units' => ['px'],
				'range' => [

					'px' => [
						'min' => 1,
						'max' => 150,
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
		$target = $settings['link']['is_external'] ? ' target="_blank"' : '';
		$nofollow = $settings['link']['nofollow'] ? ' rel="nofollow"' : '';
		
		$title = $settings['title'];

		$heading_styles = 'display: block;margin: 0 auto;font-family: Arial,Helvetica,sans-serif;';
		$heading_styles .= 'line-height: ' . (($settings['heading_line_height']['size'] ? $settings['heading_line_height']['size'] : '44') . ($settings['heading_line_height']['unit'] ? $settings['heading_line_height']['unit'] : 'px')) . ';';
		$heading_styles .= 'text-align: ' . (($settings['align'] != '') ? $settings['align'] : '') . ';';
		$heading_styles .= 'color: ' . (($settings['title_color']) ? $settings['title_color'] : '#6EC1E4') . ';';
		$heading_styles .= 'font-size: ' . (($settings['heading_font_size']['size'] ? $settings['heading_font_size']['size'] : '36') . ($settings['heading_font_size']['unit'] ? $settings['heading_font_size']['unit'] : 'px')) . ';';

		$advance_style = 'background: ' . (($settings['advance_background_type'] == 'color') ? (($settings['advance_background_color'] != '') ? $settings['advance_background_color'] . ';' : '#0000;') : 'url("' . esc_url($settings['advance_background_image']['url']) . '") no-repeat fixed center;');
		$advance_style .= ' margin: ' . (($settings['advance_margin']['top'] != '') ? $settings['advance_margin']['top'] . 'px ' . $settings['advance_margin']['right'] . 'px ' . $settings['advance_margin']['bottom'] . 'px ' . $settings['advance_margin']['left'] . 'px;' : '0px 0px 0px 0px;');
		$advance_style .= ' padding: ' . (($settings['advance_padding']['top'] != '') ? $settings['advance_padding']['top'] . 'px ' . $settings['advance_padding']['right'] . 'px ' . $settings['advance_padding']['bottom'] . 'px ' . $settings['advance_padding']['left'] . 'px;' : '0px 0px 0px 0px;');

?>

		<div style="<?php echo esc_attr($advance_style); ?>" class="heading-wrapper">
			<?php
			if (!empty($settings['link']['url'])) { ?>
				<a href="<?php echo esc_attr($settings['link']['url']); ?>" <?php  echo $target . $nofollow ?> >
				<?php } ?>

				<h2 style="<?php echo esc_attr($heading_styles); ?>"><?php echo esc_html($title); ?></h2>
				
				<?php
				if (!empty($settings['link']['url'])) { ?>
				</a>
			<?php } ?>

		</div>

<?php

	}
}
