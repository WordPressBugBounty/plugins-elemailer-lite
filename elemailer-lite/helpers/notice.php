<?php

namespace Elemailer_Lite\Helpers;

if (!defined('ABSPATH')) die('Forbidden');

/**
 * elEmailer lite template builder notice class.
 * Handles dynamically notices for lazy developers.
 *
 * @author elEmailer
 * @since 1.0.0
 */

class Notice
{

    /**
     * initialize notice functionality
     *
     * @return void
     * @since 1.0.0
     */
    public static function init()
    {
        add_action('admin_head', [__CLASS__, 'enqueue_scripts']);
        add_action('wp_ajax_elemailer-notices', [__CLASS__, 'dismiss']);
    }

    /**
     * Dismiss Notice.
     *
     * @return void
     * @since 1.0.0
     */
    public static function dismiss()
    {

        $id = (isset($_POST['id'])) ? sanitize_text_field($_POST['id']) : '';
        $time = (isset($_POST['time'])) ? sanitize_text_field($_POST['time']) : '';
        $meta = (isset($_POST['meta'])) ? sanitize_text_field($_POST['meta']) : '';

        // Valid inputs?
        if (!empty($id)) {

            if ('user' === $meta) {
                update_user_meta(get_current_user_id(), $id, true);
            } else {
                set_transient($id, true, $time);
            }

            wp_send_json_success();
        }

        wp_send_json_error();
    }

    /**
     * Enqueue Scripts.
     *
     * @return void
     * @since 1.0.0
     */
    public static function enqueue_scripts()
    {
        echo "
			<script>
			jQuery(document).ready(function ($) {
				$( '.elemailer-notice.is-dismissible' ).on( 'click', '.notice-dismiss', function() {
					_this 		= $( this ).parents( '.elemailer-notice' );
					var id 	= _this.attr( 'id' ) || '';
					var time 	= _this.attr( 'dismissible-time' ) || '';
					var meta 	= _this.attr( 'dismissible-meta' ) || '';
			
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action 	: 'elemailer-notices',
							id 		: id,
							meta 	: meta,
							time 	: time,
						},
					});
			
				});
			
			});
			</script>
		";
    }

    /**
     * Show Notices
     *
     * @return void
     * @since 1.0.0
     */
    public static function push($notice)
    {

        $defaults = [
            'id' => '',
            'type' => 'info',
            'show_if' => true,
            'message' => '',
            'class' => 'elemailer-notice',
            'dismissible' => false,
            'btn' => [],
            'dismissible-meta' => 'user',
            'dismissible-time' => WEEK_IN_SECONDS,
            'data' => '',
        ];

        $notice = wp_parse_args($notice, $defaults);

        $classes = ['elemailer-notice', 'notice'];

        $classes[] = $notice['class'];
        if (isset($notice['type'])) {
            $classes[] = 'notice-' . $notice['type'];
        }

        // Is notice dismissible?
        if (true === $notice['dismissible']) {
            $classes[] = 'is-dismissible';

            // Dismissable time.
            $notice['data'] = ' dismissible-time=' . esc_attr($notice['dismissible-time']) . ' ';
        }

        // Notice ID.
        $notice_id = 'elemailer-sites-notice-id-' . $notice['id'];
        $notice['id'] = $notice_id;
        if (!isset($notice['id'])) {
            $notice_id = 'elemailer-sites-notice-id-' . $notice['id'];
            $notice['id'] = $notice_id;
        } else {
            $notice_id = $notice['id'];
        }

        $notice['classes'] = implode(' ', $classes);

        // User meta.
        $expired = '';
        $notice['data'] .= ' dismissible-meta=' . esc_attr($notice['dismissible-meta']) . ' ';
        if ('user' === $notice['dismissible-meta']) {
            $expired = get_user_meta(get_current_user_id(), $notice_id, true);
        } elseif ('transient' === $notice['dismissible-meta']) {
            $expired = get_transient($notice_id);
        }

        // Notice visible after transient expire.
        if (isset($notice['show_if'])) {
            if (true === $notice['show_if']) {

                // Is transient expired?
                if (false === $expired || empty($expired)) {
                    self::markup($notice);
                }
            }
        } else {
            self::markup($notice);
        }

        // add_action('admin_footer', [(new self), 'enqueue_scripts'], 9999);
    }


    public static function isMultiButton($array)
    {
        if (count($array) == count($array, COUNT_RECURSIVE)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Markup Notice.
     *
     * @param array $notice Notice markup.
     * @return void
     * @since 1.0.0
     */

    public static function markup($notice = [])
    {
?>
        <div id="<?php echo esc_attr($notice['id']); ?>" class="<?php echo esc_attr($notice['classes']); ?>" <?php echo $notice['data']; ?>>
            <p>
                <?php echo $notice['message']; ?>
            </p>

            <?php if (!empty($notice['btn'])) : ?>

                <?php if (self::isMultiButton($notice['btn'])) : ?>

                    <?php foreach ($notice['btn'] as $btn) : ?>

                        <?php if (isset($notice['style'])) : ?>

                            <?php if ($notice['style'] == 'block') : ?>

                                <a id="<?php echo esc_html(isset($btn['id']) ? $btn['id'] : ''); ?>" class="<?php echo esc_html((isset($btn['style']['class'])) ? $btn['style']['class'] : 'button-primary'); ?>" href="<?php echo esc_html($btn['url']); ?>" <?php echo isset($btn['custom_style']) ? "style='{$btn['custom_style']}'" : ""; ?> <?php echo esc_attr(isset($btn['target']) ? "target={$btn['target']}" : ""); ?> <?php echo esc_attr(isset($notice['target']) ? "target={$notice['target']}" : ""); ?>><?php echo esc_html($btn['label']) ?></a>

                            <?php else : ?>

                                <p>
                                    <a id="<?php echo esc_html(isset($btn['id']) ? $btn['id'] : ''); ?>" class="<?php echo esc_html((isset($btn['style']['class'])) ? $btn['style']['class'] : 'button-primary'); ?>" href="<?php echo esc_html($btn['url']); ?>" <?php echo esc_attr(isset($notice['target']) ? "target={$notice['target']}" : ""); ?>><?php echo esc_html($btn['label']) ?></a>
                                </p>

                            <?php endif; ?>


                        <?php else : ?>

                            <p>
                                <a id="<?php echo esc_html(isset($btn['id']) ? $btn['id'] : ''); ?>" class="<?php echo esc_html((isset($btn['style']['class'])) ? $btn['style']['class'] : 'button-primary'); ?>" href="<?php echo esc_html($btn['url']); ?>" <?php echo esc_attr(isset($notice['target']) ? "target={$notice['target']}" : ""); ?>><?php echo esc_html($btn['label']) ?></a>
                            </p>

                        <?php endif; ?>


                    <?php endforeach; ?>

        <?php else : ?>
            <p>
                <a href="<?php echo esc_url($notice['btn']['url']); ?>" class="<?php echo esc_html(isset($notice['btn']['style']['class']) ? $notice['btn']['style']['class'] : 'button-primary'); ?>" <?php echo esc_attr(isset($notice['target']) ? "target={$notice['target']}" : ""); ?>><?php echo esc_html($notice['btn']['label']); ?></a>
            </p>

        <?php endif; ?>
<?php endif; ?>
</div>
<?php
    }
}
