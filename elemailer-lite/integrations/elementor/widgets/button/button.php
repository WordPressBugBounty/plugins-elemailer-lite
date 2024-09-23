<?php

namespace Elemailer_Lite\Integrations\Elementor\Widgets;

defined('ABSPATH') || exit;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Utils;
use \Elemailer_Lite\Helpers\Util;

/**
 * button widget class for registering button widget
 *
 * @author elEmailer 
 * @since 1.0.0
 */
class Elemailer_Widget_Button extends Widget_Base
{
	public function get_name()
	{
		return 'elemailer-button';
	}

	public function get_title()
	{
		return esc_html__('Button', 'elemailer-lite');
	}

	public function get_icon()
	{
		return 'eicon-button';
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
		return ['void', 'template', 'button'];
	}

	protected function register_controls()
	{

		$this->start_controls_section(
			'section_button',
			[
				'label' => __('Button', 'elemailer-lite'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'text',
			[
				'label' => __('Text', 'elemailer-lite'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Click here', 'elemailer-lite'),
				'placeholder' => __('Click here', 'elemailer-lite'),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => __('Link', 'elemailer-lite'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://your-link.com', 'elemailer-lite'),
				'default' => [
					'url' => '#',
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
					'justify' => [
						'title' => __('Justified', 'elemailer-lite'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'left',
				'toggle' => true,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_button',
			[
				'label' => __('Button Style', 'elemailer-lite'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_padding',
			[
				'label' => __('Padding (px)', 'elemailer-lite'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default' => [
					'unit' => 'px',
					'isLinked' => false,
					'top' => '12',
					'right' => '25',
					'bottom' => '12',
					'left' => '25',
				]
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __('Color', 'elemailer-lite'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'dynamic' => [
                    'active' => false,
                ],
                'global' => [
                    'active' => false,
                ],

			]
		);

		$this->add_control(
			'bg_color',
			[
				'label' => __('Background', 'elemailer-lite'),
				'type' => Controls_Manager::COLOR,
				'default' => '#61CE70',
				'dynamic' => [
                    'active' => false,
                ],
                'global' => [
                    'active' => false,
                ],

			]
		);

		$this->add_control(
			'font_size',
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
		$email_button_url = isset($settings['link']['url']) ? $settings['link']['url'] : '';
		$email_button_text = ($settings['text'] != '') ? $settings['text'] : '';
		$alignment = ($settings['text_align'] != '') ? $settings['text_align'] : 'left';

		$has_link = ((strpos($email_button_url, 'https://') !== false) ? true : ((strpos($email_button_url, 'http://') !== false) ? true : false));

		$button_styles = 'border-radius: 5px;display: inline-block;font-family: Arial,Helvetica,sans-serif;font-weight: 600;line-height: initial;text-decoration: none;';
		$button_styles .= 'font-size:' . (($settings['font_size']['size'] ? $settings['font_size']['size'] : '16') . ($settings['font_size']['unit'] ? $settings['font_size']['unit'] : 'px')) . ';';
		$button_styles .= 'color:' . (($settings['color'] != '') ? $settings['color'] : '#fff') . ';';
		$button_styles .= 'background-color:' . (($settings['bg_color'] != '') ? $settings['bg_color'] : '#6EC1E4') . ';';
		$button_styles .= 'padding:' . (($settings['button_padding']['top'] != '') ? $settings['button_padding']['top'] . 'px ' . $settings['button_padding']['right'] . 'px ' . $settings['button_padding']['bottom'] . 'px ' . $settings['button_padding']['left'] . 'px;' : '12px 25px 12px 25px;');
		$button_styles .= (($alignment == 'justify') ? 'width: 100%; text-align: center;padding-left:0px;padding-right:0px;' : '');

		$advance_style = 'background: ' . (($settings['advance_background_type'] == 'color') ? (($settings['advance_background_color'] != '') ? $settings['advance_background_color'] . ';' : '#0000;') : 'url("' . esc_url($settings['advance_background_image']['url']) . '") no-repeat fixed center;');
		$advance_style .= ' margin: ' . (($settings['advance_margin']['top'] != '') ? $settings['advance_margin']['top'] . 'px ' . $settings['advance_margin']['right'] . 'px ' . $settings['advance_margin']['bottom'] . 'px ' . $settings['advance_margin']['left'] . 'px;' : '0px 0px 0px 0px;');
		$advance_style .= ' padding: ' . (($settings['advance_padding']['top'] != '') ? $settings['advance_padding']['top'] . 'px ' . $settings['advance_padding']['right'] . 'px ' . $settings['advance_padding']['bottom'] . 'px ' . $settings['advance_padding']['left'] . 'px;' : '0px 0px 0px 0px;');

?>

		<div class="email-template-button-wrapper" style="text-align: <?php echo esc_attr($alignment); ?>; <?php echo esc_attr($advance_style); ?>">
			<?php
			/**
			 * anchor tag replaced by span for avoiding design break in mail client.
			 * mail client remove anchor tag with blank href.
			 */
			$button_markup_start = $has_link ? '<a style="' . esc_attr($button_styles) . '" href="' . $email_button_url . '" class="void-email-deafult-btn">' : '<span style="' . esc_attr($button_styles) . ';">';
			$button_markup_end = $has_link ? '</a>' : '</span>';
			echo Util::kses($button_markup_start);
			echo esc_html($email_button_text);
			echo Util::kses($button_markup_end);
			?>
		</div>

<?php
	}
}
