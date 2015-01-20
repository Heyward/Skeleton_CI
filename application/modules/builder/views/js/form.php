<script type="text/javascript">
    $(document).ready(function() {
        $("#form").valdidate_({
            rules: {
                model: "required",
                singular: "required",
            },
            messages: {
                model:"<?php echo lang('required'); ?>",
                singular:"<?php echo lang('required'); ?>",
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
                });
            }
        });
    });
</script>