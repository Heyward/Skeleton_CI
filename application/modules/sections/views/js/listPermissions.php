<script type="text/javascript">
    $(document).ready(function() {
        var list = $('#list').dataTable( {
            "aoColumns":[{"bVisible": false},null,null,null,null,{ "bSortable": false,"bSearchable": false }],
            "sAjaxSource": "<?php echo site_url('/sections/getListPermissions'); ?>",
        });

        $(document).on("click", ".action_change_menu", function(){
            $.post("<?php echo site_url("permissions/changeInMenu")?>",
                {'id':$(this).attr("id"), '<?=$csrf?>': $('input[name=<?=$csrf?>]').val()},
                    function(data){
                        list.fnDraw();
                    },
                'json'
                );
        });
        $(document).on("click", ".action_up_position", function(){
            $.post("<?php echo site_url("permissions/upPosition")?>",
                {'id':$(this).attr("id"), '<?=$csrf?>': $('input[name=<?=$csrf?>]').val()},
                    function(data){
                        list.fnDraw();
                    },
                'json'
                );
        });
        $(document).on("click", ".action_down_position", function(){
            $.post("<?php echo site_url("permissions/downPosition")?>",
                {'id':$(this).attr("id"), '<?=$csrf?>': $('input[name=<?=$csrf?>]').val()},
                    function(data){
                        list.fnDraw();
                    },
                'json'
                );
        });
    });
</script>
