<?php
function form2field_output($atts){
    ob_start();
    ?>
    <form action="/" method="post" id="form2field">
        <div class="ufields" <?php echo (get_option( 'form_form2field__bg') == 'checked'? 'style="background: #fff;"': ''); ?>>
            <?php
            global $wpdb,$current_user;
            $table = $wpdb->prefix.'form2field_v1';
            $data = $wpdb->get_row("SELECT * FROM $table WHERE user_id = $current_user->ID");
            ?>

            <label for="txt_field">Field</label>
            <input type="text" id="txt_field" name="txt_field" value="<?php echo (!empty($data)? __($data->field, 'field-form') : ''); ?>" placeholder="Texts">

            <label for="ac_number">Account Number</label>
            <input type="number" id="ac_number" name="ac_number" placeholder="Account Number" value="<?php echo (!empty($data)? __($data->account_number, 'field-form') : ''); ?>" placeholder="Account Number">

            <input type="submit" name="submit" id="form2field-subtn" value="Submit">
        </div>
    </form>

    <?php
    return ob_get_clean();
}