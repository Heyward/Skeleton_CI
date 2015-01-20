<script type="text/javascript">
    $(document).ready(function() {
        $("#form_password").validate({
            rules: {
                password: "required",
                new_password: "required",
                re_password: {equalTo: "#new_password"},
            },
            messages: {
                password:"<?php echo lang('required'); ?>",
                new_password:"<?php echo lang('required'); ?>",
                re_password:"<?php echo lang('error_equal') . " " . lang('new_password'); ?>",
            },
            submitHandler: function(form) {
                $('#form_password').ajaxSubmit({success: function(data){
                        if (data.message != ""){
                            $('#alert_form_password').removeClass("alert");
                            $('#alert_form_password').addClass("success");
                            $("#message_form_password").html(data.message);
                            $("#alert_form_password").show();
                        }
                        
                        if (data.error != ""){
                            $('#alert_form_password').addClass("alert");
                            $("#message_form_password").html(data.error);
                            $("#alert_form_password").show();
                        }      
                    },
                    dataType: 'json',
                    resetForm: true
                });
            }
        });
    });
</script>