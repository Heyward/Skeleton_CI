<script type="text/javascript">
    $(document).ready(function() {
        $("#form").validate({
            rules: {
                date: 'required',
				time: 'required',
				logType: 'required',
				data: 'required',
				oldData: 'required',
				origin: 'required',
				user: 'required',
            },
            messages: {
                date:'<?=lang('required')?>',
				time:'<?=lang('required')?>',
				logType:'<?=lang('required')?>',
				data:'<?=lang('required')?>',
				oldData:'<?=lang('required')?>',
				origin:'<?=lang('required')?>',
				user:'<?=lang('required')?>',
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