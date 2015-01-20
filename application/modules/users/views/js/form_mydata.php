<script type="text/javascript">
    $(document).ready(function() {
        $("#form").validate({
            rules: {
                idCity: "required",
                name: "required",
                lastName: "required",
                language: "required",
                email: {required: true,email: true}
            },
            messages: {
                idCity:"<?php echo lang('required'); ?>",
                name:"<?php echo lang('required'); ?>",
                lastName:"<?php echo lang('required'); ?>",
                language:"<?php echo lang('required'); ?>",
                email:"<?php echo lang('error_email'); ?>"
            },
            submitHandler: function(form) {
                $('#form').ajaxSubmit({success: function(data){
                        if (data.message != ""){
                            $('#alert').addClass("success");
                            $("#message").html(data.message);
                            $("#alert").show();
                        }
                        
                        if (data.error != ""){
                            $('#alert').addClass("alert");
                            $("#message").html(data.error);
                            $("#alert").show();
                        }      
                    },
                    dataType: 'json'
                    <?php echo ($id == "") ? ",'resetForm': true" : ''; ?>
                });
            }
        });
    });
</script>