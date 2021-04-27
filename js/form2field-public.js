jQuery(function ($) {
    $('#form2field').submit(function (e) {
        e.preventDefault();

        let number = $('#ac_number').val();
        let field = $('#txt_field').val();

        if (number == "" || field == "") {
            if (number == "") {
                $('#ac_number').css('border', '1px solid red');
                $('#txt_field').css('border', '1px solid #ddd');
            }
            if (field == "") {
                $('#txt_field').css('border', '1px solid red');
                $('#ac_number').css('border', '1px solid #ddd');
            }
            return false;
        }


        $('#txt_field').css('border', '1px solid #ddd');
        $('#ac_number').css('border', '1px solid #ddd');

        $.ajax({
            type: "post",
            url: form2field_actions.ajaxurl,
            data: {
                action: 'form2field_data_check',
                number: number,
                field: field,
                nonce: form2field_actions.nonce
            },
            beforeSend: () => {
                $('#form2field-subtn').prop('disabled', true).val('Submiting...');
            },
            dataType: 'json',
            success: function (response) {
                $('#form2field-subtn').removeAttr('disabled').val('Submit');
            }
        });
        
    })
});