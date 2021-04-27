jQuery(function ($) {
    $('#form-bg').on('change', function () {
        let data = "";
        if ($(this).val() == 0) {
            $(this).val(1);
            data = 'true';
        } else {
            $(this).val(0);
            data = 'false';
        }
        
        $.ajax({
            type: "post",
            url: form2field_actions.ajaxurl,
            data: {
                action: 'form2field_bg_update',
                data: data
            },
            success: function (response) { }
        });
    });
});