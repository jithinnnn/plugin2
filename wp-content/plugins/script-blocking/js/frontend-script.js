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

    function deleteCookie(name) {
        setCookie(name, "", -1); 
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

    function handleConsent(consent) {
        if (consent === 'accept') {
        } else {
            for (let cookieName in acceptedCookies) {
                deleteCookie(cookieName);
            }
        }
        setCookie('sb_user_consent', consent, 365);
    }
            
    document.querySelector('.btn-accept').addEventListener('click', () => handleConsent('accept'));
    document.querySelector('.btn-reject').addEventListener('click', () => handleConsent('reject'));

    $('#sb-frontend-banner .btn-accept, #sb-frontend-banner .btn-reject').click(function() {
        var consent = $(this).hasClass('btn-accept') ? 'accept' : 'reject';
        $.post(sb_ajax_object.ajax_url, {
            action: 'sb_handle_user_consent',
            nonce: sb_ajax_object.nonce,
            consent: consent
        }).done(function() {
            handleConsent(consent);
            $('#sb-frontend-banner').hide();
            $('#sb-revisit-banner').show();
        });
    });

    $('#sb-revisit-banner .btn-revisit').click(function() {
        $('#sb-revisit-banner').hide();
        $('#sb-frontend-banner').show();
        setCookie('sb_user_consent', 'accept', 365); 
    });

    if (checkConsent()) {
        $('#sb-frontend-banner').hide();
        $('#sb-revisit-banner').show();
    } else {
        $('#sb-frontend-banner').show();
        $('#sb-revisit-banner').hide();
    }
});