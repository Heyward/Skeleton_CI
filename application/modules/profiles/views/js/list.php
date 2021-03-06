<script type="text/javascript">
    $(document).ready(function() {
        var list =$('#list').dataTable( {
            "aoColumns":[{"bVisible": false},null,null,{ "bSortable": false,"bSearchable": false }],
            "sAjaxSource": "<?php echo site_url('/profiles/getList'); ?>",
        });
        
        var id = 0;
        $( "#confirmation_delete" ).dialog({
            autoOpen: false,
            resizable: false,
            height:"auto",
            modal: true,
            buttons: {
                "<?php echo lang("button_delete_ok"); ?>": function() {
                    $.post("<?php echo site_url('/profiles/delete'); ?>",
                        {'id':id, '<?=$csrf?>': $('input[name=<?=$csrf?>]').val()},
                        function(data){
                            $("#confirmation_delete").dialog( "close" );
                            if(data.error != ""){
                                $('#alert').addClass("alert");
                                $("#message").html(data.error);
                                $("#alert").show();
                            }
                            
                            if (data.warning != "") {
                                $('#alert').addClass("alert-info");
                                $("#message").html(data.warning);
                                $("#alert").show();
                            }
                            
                            if(data.message != ""){
                                list.fnDraw();
                                $('#alert').addClass("success");
                                $("#message").html(data.message);
                                $("#alert").show();
                            }
                        },'json'
                    );
                },
                "<?php echo lang("button_delete_ko"); ?>": function() {
                    $( this ).dialog( "close" );
                }
            }
        });
        
        $(document).on("click", ".action_delete", function(){
            id = this.id;
            $( "#message_delete" ).html("<?php echo lang("message_delete_profile"); ?>");
            $( "#confirmation_delete" ).dialog("open");
        });
    });
</script>
