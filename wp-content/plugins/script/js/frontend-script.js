jQuery(document).ready(function($) {
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function checkConsent() {
        return getCookie('sb_user_consent');
    }

    $('#sb-frontend-banner .btn-accept, #sb-frontend-banner .btn-reject').click(function() {
        var consent = $(this).hasClass('btn-accept') ? 'accept' : 'reject';
        $.post(sb_ajax_object.ajax_url, {
            action: 'sb_handle_user_consent',
            nonce: sb_ajax_object.nonce,
            consent: consent
        }).done(function() {
            setCookie('sb_user_consent', consent, 365);
            $('#sb-frontend-banner').hide();
            $('#sb-revisit-banner').show();
        });
    });

    $('#sb-revisit-banner .btn-revisit').click(function() {
        setCookie('sb_user_consent', '', -1); 
        location.reload();
    });

    if (checkConsent()) {
        $('#sb-frontend-banner').hide();
        $('#sb-revisit-banner').show();
    } else {
        $('#sb-frontend-banner').show();
        $('#sb-revisit-banner').hide();
    }
});
