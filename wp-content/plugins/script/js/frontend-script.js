jQuery(document).ready(function($) {
    $('#sb-frontend-banner .btn-accept').click(function() {
        $.post(sb_ajax_object.ajax_url, {
            action: 'sb_handle_user_consent',
            nonce: sb_ajax_object.nonce,
            consent: 'accept'
        }).done(function() {
            $('#sb-frontend-banner').fadeOut();
        });
    });

    $('#sb-frontend-banner .btn-reject').click(function() {
        $.post(sb_ajax_object.ajax_url, {
            action: 'sb_handle_user_consent',
            nonce: sb_ajax_object.nonce,
            consent: 'reject'
        }).done(function() {
            $('#sb-frontend-banner').fadeOut();
        });
    });
});
