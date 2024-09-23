<?php defined('ABSPATH') || exit; ?>
<div class="elemailer-form-template-add-modal" id="elemailer-modal">
  <div class="elemailer-modal fade" id="elemailerFormTemplateModal" tabindex="-1" role="dialog" aria-labelledby="elemailerFormTemplateModalLabel" aria-hidden="true">
    <div class="elemailer-modal-dialog" role="document">
      <div class="elemailer-modal-content">
        <div class="elemailer-modal-header" style="background-image: url(<?php echo ELE_MAILER_LITE_PLUGIN_PUBLIC . '/assets/img/modal-head-bg.png' ?>);">
          <h5 class="modal-title" id="elemailerFormTemplateModalLabel"><?php esc_html_e('Elemailer Form Template Settings', 'elemailer-lite'); ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </div>
        <p class="alert response-message"></p>
        <div class="elemailer-modal-body">
          <form action="" mathod="post" id="elemailer-modal-settings" data-open-editor="0" data-editor-url="<?php echo get_admin_url(); ?>" data-nonce="<?php echo wp_create_nonce('wp_rest'); ?>">
            <div class="form-group">
              <label for="templateName"><?php esc_html_e('Template Name', 'elemailer-lite'); ?></label>
              <input name="title" type="text" class="form-control title" id="templateName" placeholder="Enter Name">
              <small class="form-text text-muted"><?php esc_html_e('This is your template name or title', 'elemailer-lite'); ?></small>
            </div>
          </form>
          <p class="ele-instruction"><?php esc_html_e('This template will be used only for elementor pro\'s form action\'s email and email2.', 'elemailer-lite'); ?></p>
          <p class="ele-instruction"><?php esc_html_e('You can use our template on elementor form submission.', 'elemailer-lite'); ?></p>
        </div>

        <div class="elemailer-modal-footer">
          <button type="button" class="btn btn-secondary elemailer-edit">
            <img src="<?php echo ELE_MAILER_LITE_PLUGIN_PUBLIC . '/assets/img/elementor.png' ?>" alt="">
            <?php esc_html_e('Edit with elementor', 'elemailer-lite'); ?>
          </button>
          <button type="button" class="btn btn-primary elemailer-save">
            <img src="<?php echo ELE_MAILER_LITE_PLUGIN_PUBLIC . '/assets/img/save.png' ?>" alt="">
            <?php esc_html_e('Save changes', 'elemailer-lite'); ?>
          </button>
        </div>
      </div>
      <div class="elemailer-modal-preloader">
        <div class="sk-chase">
          <div class="sk-chase-dot"></div>
          <div class="sk-chase-dot"></div>
          <div class="sk-chase-dot"></div>
          <div class="sk-chase-dot"></div>
          <div class="sk-chase-dot"></div>
          <div class="sk-chase-dot"></div>
        </div>
      </div>
    </div>
  </div>
</div>