<script type="text/javascript">
    $(document).ready(function() {
        $("#reset").validate({
            errorElement: "span",
            errorPlacement: function(error, element) {
                var separator = "";
                if ($("#error-login div").html() != ""){
                    separator = "  ";
                }
                $(error).html(separator + $(error).html());
                
                if ($("#error-login div").find("[for='"+$(element).attr("name")+"']").length <= 0){
                    $("#error-login div").append(error);
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).parent().addClass("error");
            },
            unhighlight: function(element, errorClass, validClass) {
                $("[for='"+$(element).attr("name")+"']").remove();
            },
             rules: {
                 user: {required: true,email: true}
             },
             messages: {
                 user: "<?php echo lang('error_email'); ?>"
             },
             submitHandler: function(form) {
                 $('#reset').ajaxSubmit({success: function(data){
                         if (data.message == "ok"){
                             $("#reset .message").html("<?php echo lang("confimation_reset");?>");
                         }else{
                             $("#reset .message").html("Error");
                         }
                     },
                     dataType: 'json'
                 });
             }
         });
	     
        $("#forgot, #return").click(function(){
            $("#reset").toggle();
            $("#login").toggle();
            $("#forgot").toggle();
            $("#return").toggle();
            $("#error-login div").html("");
        });
        
        $("#login").validate({
            errorElement: "span",
            errorPlacement: function(error, element) {
                var separator = "";
                if ($("#error-login div").html() != ""){
                    separator = "  ";
                }
                $(error).html(separator + $(error).html());
                
                if ($("#error-login div").find("[for='"+$(element).attr("name")+"']").length <= 0){
                    $("#error-login div").append(error);
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).parent().addClass("error");
            },
            unhighlight: function(element, errorClass, validClass) {
              $("[for='"+$(element).attr("name")+"']").remove();
            },
            rules: {
                email: {required: true,email: true},
                password: "required"
            },
            messages: {
                email:{required:"<?php echo lang('error_email'); ?>", email:"<?php echo lang('error_email'); ?>"},
                password:"<?php echo lang('error_password'); ?>"
            }
        });
    });
</script>