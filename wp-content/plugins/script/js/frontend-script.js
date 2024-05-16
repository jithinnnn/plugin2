jQuery(document).ready(function($) {
    $('#sb-frontend-banner .btn').click(function() {
        var action = $(this).hasClass('btn-accept') ? 'accept' : 'reject';
        
        if (action === 'accept') {
            $.ajax({
                type: 'post',
                url: sb_ajax_object.ajax_url,
                data: {
                    action: 'sb_accept_consent',
                    nonce: sb_ajax_object.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#sb-frontend-banner').hide();
                    } else {
                        alert('Error: ' + response.data);
                    }
                }
            });
        } else if (action === 'reject') {
            $.ajax({
                type: 'post',
                url: sb_ajax_object.ajax_url,
                data: {
                    action: 'sb_reject_consent',
                    nonce: sb_ajax_object.nonce
                },
                success: function(response) {
                    if (response.success) {
                    
                    } else {
                        alert('Error: ' + response.data);
                    }
                }
            });
        }
    });
});
