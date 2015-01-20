<script type="text/javascript">
    $(document).ready(function() {
        $("#form").validate({
            rules: {
                name: "required",
                code: "required",
            },
            messages: {
                name:"<?php echo lang('required'); ?>",
                code:"<?php echo lang('required'); ?>",
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
