jQuery(function ($) {
    $('#form2field').submit(function (e) {
        e.preventDefault();

        let number_1 = $('#ac_number_1').val();
        let number_2 = $('#ac_number_2').val();

        if (number_1 == "") {
            $('#ac_number_1').css('border', '1px solid red');
            return false;
        }

        $('#ac_number_1').css('border', '1px solid #ddd');
        $.ajax({
            type: "post",
            url: form2field_actions.ajaxurl,
            data: {
                action: 'form2field_data_check',
                number_1: number_1,
                number_2: number_2,
                nonce: form2field_actions.nonce
            },
            beforeSend: () => {
                $('#form2field-subtn').prop('disabled', true).val('Activating...');
            },
            dataType: 'json',
            success: function (response) {
                $('#form2field-subtn').removeAttr('disabled').val('Activate');
            }
        });
        
    })
});