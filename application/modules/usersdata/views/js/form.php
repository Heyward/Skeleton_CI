<script type="text/javascript">
    $(document).ready(function() {
        <? if ($idCountry > 0): ?>
        
        <? endif; ?>
        
        $("[name='idCountry']").change(function(){
            $.post(
                "<?=site_url('/cities/getCitiesByCountry/')?>",
                {idCountry:$(this).val(), '<?=$csrf?>': $('input[name=<?=$csrf?>]').val()},
                function(data){
                    var options = "";
                    options = "<option value=''><?=lang("default_select")?></option>";
                    for (city in data.data){
                        var id = data.data[city].id;
                        var name = data.data[city].name;
                        options += "<option value='"+id+"'>"+name+"</option>";
                    }
                    $("[name='idCity']").html(options);
                },
                "json"
            );
        });
        
        $("#form").validate({
            rules: {
                idCity: "required",
                idProfile: "required",
                name: "required",
                lastName: "required",
                email: {required: true, email: true},
                identification: {required: true},
            },
            messages: {
                idCity:"<?php echo lang('required'); ?>",
                idProfile:"<?php echo lang('required'); ?>",    
                name:"<?php echo lang('required'); ?>",
                lastName:"<?php echo lang('required'); ?>",
                email:"<?php echo lang('error_email'); ?>",
                identification: {required: "<?php echo lang('required'); ?>"},
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