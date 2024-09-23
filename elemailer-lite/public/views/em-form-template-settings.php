<div class="wrap">
    <h2><?php esc_html_e( 'Elemailer Settings', 'elemailer-lite' ) ?></h2>
    <form method="post" action="options.php" name="em-form-settings">
        <?php settings_fields( 'em-form-template-settings-group' ); ?>
        <?php do_settings_sections( 'em-form-template-settings-group' ); ?>
        <table class="em-form-template-settings-form form-table">
            <tr valign="middle">
                <td scope="row" colspan="3">
                    <p class="em-form-template-settings-notes"><?php echo sprintf( __( 'We would appreciate it if you keep the footer branding for spreading the news about our amazing <b>Elemailer Lite</b> plugin.', 'elemailer-lite' ) ); ?></p>
                    <img class="we-would-appreciate-icon" src="<?php echo esc_url( ELE_MAILER_LITE_PLUGIN_PUBLIC . '/assets/images/we-would-appreciate-you.png' ); ?>" alt="we-would-appreciate-you">
                </td>
            </tr>
            <tr valign="middle">
                <td scope="row" colspan="3">
                <p class="em-form-template-settings-notes"><?php echo sprintf( __( 'In case you sill would like to remove the footer branding, it would be highly appreciated if you get one of our <a href="https://elemailer.com/pricing/" target="_blank"><strong>PRO plans</strong></a> or tell your friends about us or even leave us a great <a href="https://wordpress.org/support/plugin/elemailer-lite/reviews/#new-post" target="_blank"><strong>REVIEW</strong></a> as this will boost our enthusiasm to work more on this amazing plugin.', 'elemailer-lite' ) ); ?></p>
                </td>
            </tr>
            <tr valign="middle" class="elemailer-middle-action-buttons-area">
                <td scope="row">
                    <label><input type="checkbox" name="elemailer_hide_mail_branding" value="yes" <?php checked( get_option( 'elemailer_hide_mail_branding', true ), 'yes' ); ?> /> <?php esc_html_e( 'Remove footer Branding', 'elemailer-lite' ) ?></label>
                    <?php submit_button(); ?>
                    </form>
                </td>
                <td scope="row" class="elemailer-middle-action-button">
                    <?php esc_html_e( 'Get us some coffee?', 'elemailer-lite' ) ?>
                    <form action="https://www.paypal.com/donate" method="post" target="_top" name="donate">
                    <input type="hidden" name="hosted_button_id" value="LYXA75EXQLYFN" />
                    <input type="image" src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" class="em-form-template-settings-get-us-coffee"/>
                    <img alt="" border="0" src="https://www.paypal.com/en_AU/i/scr/pixel.gif" width="1" height="1" />
                    </form>
                </td>
                <td scope="row">
                    <?php esc_html_e( 'Get Elemailer', 'elemailer-lite' ) ?>
                    <p class="submit"><a href="<?php echo esc_url('https://elemailer.com/pricing/') ?>" target="_blank" class="button button-primary em-form-template-settings-get-elemailer"><?php esc_html_e( 'Go Pro', 'elemailer-lite' ) ?></a></p>
                </td>
            </tr>
        </table>
</div>

<style>
    .em-form-template-settings-form {
        margin: 0px auto;
        max-width: 800px;
    }
    .em-form-template-settings-notes {
        font-size: 17px !important;
        text-align: center;
        padding-bottom: 20px;
    }
    .em-form-template-settings-form td,
    .em-form-template-settings-form p.submit{
        text-align: center;
        margin: 0px;
        font-size: 17px !important;
        padding: 10px 0px;
    }
    .em-form-template-settings-form .elemailer-middle-action-button {
        border-left: solid;
        border-right: solid;
    }
    .em-form-template-settings-form .button-primary {
        margin-top: 10px;
        width: 200px;
        height: 40px;
        line-height: 40px;
        font-size: 14px;
        border-radius: 35px;
    }
    .em-form-template-settings-form h3{ 
        margin-top: 0px;
    }
    .em-form-template-settings-get-us-coffee {
        margin-top: 18px;
    }
    .em-form-template-settings-get-elemailer {
        background-color: #ffbd59 !important;
        border-color: #ffbd59 !important;
    }
</style>
