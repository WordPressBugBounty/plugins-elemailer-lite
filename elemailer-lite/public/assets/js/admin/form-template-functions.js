jQuery(document).ready(function ($) {
    "use strict";

    var form = $('#elemailer-modal-settings'),
        modal = $('#elemailerFormTemplateModal'),
        elResponse = modal.find('.response-message'),
        id = 0;

    var saveButton = $('button.elemailer-save');
    var editButton = $('button.elemailer-edit');

    $('.row-actions .edit a, .page-title-action, .column-title a.row-title').on('click', function (e) {
        e.preventDefault();
        //console.log('click event');
        var data = null,
            nonce = $(this).attr('data-nonce'),
            column = $(this).parents('.column-title');

        modal.addClass('loading');
        //modal.modal('show');
        elResponse.css('display', 'none');
        $('.elemailer-form-template-add-modal').addClass('show');
        modal.css('display', 'block');

        if (column.length > 0) {
            id = column.find('.hidden').attr('id').split("_")[1];

            $.ajax({
                url: window.elemailer_lite.restUrl + "form-templates/get/" + id,
                type: "get",
                headers: {
                    "X-WP-Nonce": nonce
                },
                dataType: "json",
                success: function (response) {
                    modalView(response);
                }
            });

        } else {

            data = {
                title: 'Form template # ' + Math.floor(new Date().getTime() / 1000),
                subject: '',
                emailTo: '',
                emailFrom: '',
                emailReplyTo: '',
            };

            modalView(data);
        }

    });

    // remove modal on outside click
    var modal = $('#elemailer-modal');

    $('body').on('click', function (event) {
        console.log($(event.target).is(modal));
        if ($(event.target).is(modal)) {
            modal.removeClass("show");
        }
    });

    $('.elemailer-form-template-add-modal .elemailer-modal-content button.close').on('click', function (e) {
        $('.elemailer-form-template-add-modal').removeClass('show');
    })

    saveButton.on('click', function (e) {
        e.preventDefault();
        form.trigger('submit');
        saveButton.attr("disabled", true);
        $('.elemailer-form-template-add-modal').addClass('activeModalPreloader');
    })

    editButton.on('click', function (e) {
        e.preventDefault();
        form.attr('data-open-editor', '1');
        form.trigger('submit');
        editButton.attr("disabled", true);
        $('.elemailer-form-template-add-modal').addClass('activeModalPreloader');
    })

    form.on('submit', function (e) {
        e.preventDefault();
        var nonce = $(this).attr('data-nonce'),
            submittedData = $(this).serialize(),
            modalEdit = $(this).attr('data-open-editor'),
            dataEditUrl = $(this).attr('data-editor-url');

        $.ajax({
            url: window.elemailer_lite.restUrl + "form-templates/update/" + id,
            type: "post",
            data: submittedData,
            headers: {
                "X-WP-Nonce": nonce
            },
            dataType: "json",
            success: function (response) {
                console.log(response);
                if (response.status == 1) {
                    elResponse.removeClass('alert-danger');
                    elResponse.addClass('alert-success').css('display', 'block');
                    elResponse.html(response.data.message);

                    setTimeout(function () {
                        $('.elemailer-form-template-add-modal').removeClass('show');
                        saveButton.attr("disabled", false);
                    }, 3000);

                    // reload page when new template created
                    if (id == 0) {
                        location.reload();
                    } else {
                        let title = $('#post-' + id).find('.row-title');
                        if (title.length > 0) {
                            title.html(response.data.updated_data.title);
                        }
                        setTimeout(function () {
                            elResponse.css('display', 'none');
                        }, 8000);
                    }

                    if (modalEdit == '1') {
                        $('.elemailer-form-template-add-modal').addClass('activeModalPreloader');
                        window.location.href = dataEditUrl + "?post=" + response.data.id + "&action=elementor";
                    } else {
                        $('.elemailer-form-template-add-modal').removeClass('activeModalPreloader');
                    }

                } else {
                    elResponse.removeClass('alert-success');
                    elResponse.addClass('alert-danger').css('display', 'block');
                    elResponse.html((response.error[1]) ? response.error[1] : response.error[0]);
                    $('.elemailer-form-template-add-modal').removeClass('activeModalPreloader');
                    setTimeout(function () {
                        $('.elemailer-form-template-add-modal').removeClass('show');
                        saveButton.attr("disabled", false);
                    }, 5000);
                }

                // reset post id value for avoiding old post replaced by new post
                id = 0;

            }
        });

    })

    function modalView(data) {

        form.find('.title').val(data.title);
        form.find('.subject').val(data.subject);
        form.find('.emailTo').val(data.emailTo);
        form.find('.emailFrom').val(data.emailFrom);
        form.find('.emailReplyTo').val(data.emailReplyTo);

    }

});
