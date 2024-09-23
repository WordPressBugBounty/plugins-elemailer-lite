<?php

namespace Elemailer_Lite\Integrations\Elementor\Widgets;

defined('ABSPATH') || exit;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Utils;
/**
 * shortcode widget class for registering shortcode widget
 *
 * @author elEmailer 
 * @since 1.0.0
 */
class Elemailer_Widget_Shortcode extends Widget_Base
{

	public function get_name()
	{
		return 'elemailer-shortcode';
	}

	public function get_title()
	{
		return esc_html__('Shortcode', 'elemailer-lite');
	}

	public function get_icon()
	{
		return 'eicon-shortcode';
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
		return ['void', 'template', 'shortcode', 'form', 'field', 'input'];
	}

	protected function register_controls()
	{

		$this->start_controls_section(
			'content_section',
			[
				'label' => __('Content', 'elemailer-lite'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'label_position',
			[
				'label' => __('Label position', 'elemailer-lite'),
				'type' => Controls_Manager::SELECT,
				'default' => 'inline-block',
				'options' => [
					'block'  => __('Top', 'elemailer-lite'),
					'inline-block' => __('Left', 'elemailer-lite'),
					'none' => __('None', 'elemailer-lite'),
				],
			]
		);

		$this->add_control(
			'label_text',
			[
				'label' => __('Label', 'elemailer-lite'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Label: ', 'elemailer-lite'),
				'label_block' => true,
				'description' => 'This will be label of shortcode',
				'condition' => [
					'label_position!' => 'none',
				]
			]
		);

		$shortcode_desc='Use multiple shortcodes here. <a target="_blank" href="https://elemailer.com/documentation/shortcodes">Learn more</a><br><br>';
		if(function_exists('elementor_pro_load_plugin')){
			$shortcode_desc .='You can use Elementor pro form shortcode like [field id="name"], [field id="email"]<br><br>';
		}
		if(class_exists('WPCF7_ContactForm')){
			$shortcode_desc .='Add Contact form 7 Shortcodes such as [your-name] [_site_admin_email]<br>';
		}
		

		$this->add_control(
			'shortcode',
			[
				'label' => __('Shortcode', 'elemailer-lite'),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'placeholder' => __('Enter supported Shortcode here', 'elemailer-lite'),
				'dynamic' => [
					'active' => true,
				],
				'description' => $shortcode_desc,
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

		$this->end_controls_section();

		$this->start_controls_section(
			'label_style_section',
			[
				'label' => esc_html__('Label', 'elemailer-lite'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'label_position!' => 'none',
				],
			]
		);

		$this->add_control(
			'label_spacing',
			[
				'label' => __('Spacing', 'elemailer-lite'),
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

		$this->add_control(
			'label_color',
			[
				'label' => __('Color', 'elemailer-lite'),
				'type' => Controls_Manager::COLOR,
				'dynamic' => [
                    'active' => false,
                ],
                'global' => [
                    'active' => false,
                ],
				'default' => '#93003C',
			]
		);

		$this->add_control(
			'label_font_size',
			[
				'label' => __('Font Size (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '16',
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
			'content_style_section',
			[
				'label' => esc_html__('Content', 'elemailer-lite'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_color',
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
			'content_font_size',
			[
				'label' => __('Font Size (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => '16',
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
		$shortcode = isset($settings['shortcode']) ? $settings['shortcode'] : '';
		$label_position = isset($settings['label_position']) ? $settings['label_position'] : 'inline-block';
		$label_text = isset($settings['label_text']) ? $settings['label_text'] : '';

		$parent_styles  = 'font-family: Arial,Helvetica,sans-serif;font-weight: 600;';
		$parent_styles .= 'color: ' . (($settings['content_color'] != '') ? $settings['content_color'] : '#000') . ';';
		$parent_styles .= 'font-size: ' . (($settings['content_font_size']['size'] ? $settings['content_font_size']['size'] : '18') . ($settings['content_font_size']['unit'] ? $settings['content_font_size']['unit'] : 'px')) . ';';
		$parent_styles .= 'text-align: ' . (($settings['text_align'] != '') ? $settings['text_align'] : 'left') . ';';

		$label_styles  = 'margin: 0px auto;font-family: Arial,Helvetica,sans-serif;font-weight: 600;vertical-align: middle;';
		$label_styles .= 'display: ' . (($settings['label_position'] != '') ? $settings['label_position'] : 'inline-block') . ';';
		$label_styles .= 'color: ' . (($settings['label_color'] != '') ? $settings['label_color'] : '#93003C') . ';';
		$label_styles .= 'font-size: ' . ((($settings['label_font_size']['size'] != '') ? $settings['label_font_size']['size'] : '18') . (($settings['label_font_size']['unit'] != '') ? $settings['label_font_size']['unit'] : 'px')) . ';';
		$label_styles .= (($label_position == 'block') ? 'margin-bottom: ' : 'margin-right: ') . ((($settings['label_spacing']['size'] != '') ? $settings['label_spacing']['size'] : '0') . (($settings['label_spacing']['unit'] != '') ? $settings['label_spacing']['unit'] : 'px')) . ';';

		$advance_style = 'background: ' . (($settings['advance_background_type'] == 'color') ? (($settings['advance_background_color'] != '') ? $settings['advance_background_color'] . ';' : '#0000;') : 'url("' . esc_url($settings['advance_background_image']['url']) . '") no-repeat fixed center;');
		$advance_style .= ' margin: ' . (($settings['advance_margin']['top'] != '') ? $settings['advance_margin']['top'] . 'px ' . $settings['advance_margin']['right'] . 'px ' . $settings['advance_margin']['bottom'] . 'px ' . $settings['advance_margin']['left'] . 'px;' : '0px 0px 0px 0px;');
		$advance_style .= ' padding: ' . (($settings['advance_padding']['top'] != '') ? $settings['advance_padding']['top'] . 'px ' . $settings['advance_padding']['right'] . 'px ' . $settings['advance_padding']['bottom'] . 'px ' . $settings['advance_padding']['left'] . 'px;' : '0px 0px 0px 0px;');

?>
		<div style="<?php echo esc_attr($advance_style); ?>" class="void-section-shortcode">
			<div style="<?php echo esc_attr($parent_styles); ?>" class="void-shortcode-with-label">
				<h4 style="<?php echo esc_attr($label_styles); ?>"><?php echo esc_html($label_text); ?></h4>
				<?php echo do_shortcode("$shortcode"); ?>
			</div>
		</div>

<?php
	}
}
