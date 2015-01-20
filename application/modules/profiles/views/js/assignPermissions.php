<script type="text/javascript">
    $(document).ready(function() {
        $("#form").validate({
            submitHandler: function(form) {
                $('#form').ajaxSubmit({
                    success: function(data){
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