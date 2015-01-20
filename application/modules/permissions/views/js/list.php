<script type="text/javascript">
    $(document).ready(function() {
        var list = $('#list').dataTable( {
            "aoColumns":[{"bVisible": false},null,null,null,null,null,{ "bSortable": false,"bSearchable": false }],
            "sAjaxSource": "<?php echo site_url('/permissions/getList'); ?>",
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
    });
</script>
