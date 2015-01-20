<!DOCTYPE html>
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en"><!--<![endif]-->
    
    <head>
        <!--http://www.keenthemes.com/preview/metronic/index.html-->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width" />
        <title>Admin</title>
        
        <!-- JQuery UI -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>js/jquery-ui-1.10.2/themes/base/jquery.ui.all.css" />
        <!--Foundation-->
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/normalize.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/foundation.css" />
        <!--Custom-->
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/app.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/fonts.css" />
        <!--Icons-->
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/icons/general/css/general_foundicons.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/icons/accessibility/css/accessibility_foundicons.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/icons/FortAwesome/css/font-awesome.css" />
        <!--[if lt IE 8]>
            <link rel="stylesheet" href="<?php echo base_url(); ?>css/icons/general/css/general_foundicons_ie7.css">
            <link rel="stylesheet" href="<?php echo base_url(); ?>css/icons/accessibility/css/accessibility_foundicons_ie7.css">
            <link rel="stylesheet" href="<?php echo base_url(); ?>css/icons/FortAwesome/css/font-awesome-ie7.min.css" />
        <![endif]-->
        <!-- Datatables -->
            <link rel="stylesheet" href="<?php echo base_url(); ?>js/DataTables/media/css/demo_table_jui.css" />
        
        
        <script src="<?php echo base_url(); ?>js/vendor/custom.modernizr.js"></script>
        <script src="<?php echo base_url(); ?>js/jsapi.js"></script>
    </head>
    <body> 
        <?=$header?>
        <?=$body?>
        <?=$footer?>
        
        <script src="<?php echo base_url(); ?>js/vendor/jquery.js"></script>
        <script src="<?php echo base_url(); ?>js/app.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.alerts.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.clearing.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.cookie.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.dropdown.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.forms.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.joyride.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.magellan.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.orbit.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.placeholder.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.reveal.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.section.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.tooltips.js"></script>
        <script src="<?php echo base_url(); ?>js/foundation/foundation.topbar.js"></script>

        <script src="<?php echo base_url(); ?>js/custom/jquery.metadata.js"></script>
        <script src="<?php echo base_url(); ?>js/custom/jquery.validate.min.js"></script>
        <script src="<?php echo base_url(); ?>js/custom/additional-methods.min.js"></script>
        <script src="<?php echo base_url(); ?>js/custom/jquery.form.js"></script>
        <script src="<?php echo base_url(); ?>js/DataTables/media/js/jquery.dataTables.js"></script>
        <script src="<?php echo base_url(); ?>js/custom/jquery.blockUI.js"></script>
        <script src="<?php echo base_url(); ?>js/tiny_mce/tiny_mce.js"></script>

        <script src="<?php echo base_url(); ?>js/jquery-ui-1.10.2/ui/jquery-ui.js"></script>
        
        <?=$JS?>
        
        <script type="text/javascript">
            $(document).foundation();
            
            $.metadata.setType("attr", "validate");
            
            $(document).ready(function(){
                <?php if (isset($notifications)): ?>
                    $(".notification").click(function(){
                        if (<?=count($notifications)?> > 0){
                            $.post("<?=site_url("notifications/read")?>");
                        }
                    });
                <?php endif; ?>
                
                $(document).ajaxStart(function(){
                    $('#alert').removeClass("success secondary alert");
                    $.blockUI({ 
                        message: $("#loader").html(),
                        css: { 
                            border: 'none', 
                            padding: '15px', 
                            backgroundColor: 'none', 
                            '-webkit-border-radius': '10px', 
                            '-moz-border-radius': '10px', 
                            color: '#fff' 
                        } 
                    });
                })
                .ajaxStop(function(){
                    $.unblockUI();
                })
                .ajaxError(function(event, request, settings){
                    $.unblockUI();
                    $('#alert').addClass("alert");
                    $("#message").html("Error! Sorry!");
                    $("#alert").show();
                });
            });
            
            $.extend($.fn.dataTable.defaults, {
                "bStateSave": true,
                "fnStateSave": function(oSettings,oData){
                    var url = location.pathname.replace(/[0-9\/]/g, "");
                    suff=$(this).attr('id')+url;
                    localStorage.setItem('DataTables_'+ suff, JSON.stringify(oData));
                },
                "fnStateLoad":function(oSettings){
                    var url = location.pathname.replace(/[0-9\/]/g, "");
                    suff=$(this).attr('id')+url;
                    return JSON.parse(localStorage.getItem('DataTables_'+ suff));
                },
                "bJQueryUI": true,
                "bProcessing": true,
                "bAutoWidth": false,
                "sPaginationType": "full_numbers",
                "bServerSide": true,
                "oLanguage": $.extend($.fn.dataTable.defaults.oLanguage, {
                    "sUrl": "<?php echo base_url() ?>js/i18n/data_table_<?php echo $this->session->userdata(AuthConstants::LANG); ?>.txt"
                })
            });
            
            jQuery.validator.setDefaults({
                ignore: [],
                errorElement: "small",
                errorPlacement: function(error, element) {
                    error.appendTo( element.parent() );
                    element.parent().parent().find(".prefix").addClass("error");
                    element.addClass("error");
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).parent().parent().find(".prefix").addClass("error");
                    $(element).addClass("error");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).parent().parent().find(".prefix").removeClass("error");
                    $(element).removeClass("error");
                    $(element).parent().parent().find("span.prefix").removeClass("error");
                },
            });
            
            $(document).on("click", "a", function(event){
                if ($(this).attr("href") == "#"){
                    event.preventDefault();
                }
            });

        </script>
        
        <div class="hide" id="loader">
            <div class="progress progress-info progress-striped active" style="background: #ffffff">
                <div class="bar" style="width: 100%; color:#5d5d5d;">Loading...</div>
            </div>
        </div>
        
        <ul id="drop-user-menu" class="f-dropdown content-user-menu">
            <li><a href="<?=site_url("users/mydata")?>"><i class="foundicon-mail"></i> <?=lang("profile")?></a></li>
            <li class="last"><a href="<?=site_url("login/logout")?>"><i class="foundicon-a-key"></i> <?=lang("logout")?></a></li>
        </ul>
        
        <ul id="drop-notifications" class="f-dropdown content-notification">
            <?php if (isset($notifications)): ?>
                <li class="first"><?=sprintf(lang("title_notifications"), count($notifications))?></li>
                <?php foreach($notifications as $aNotification): ?>
                    <li><a href="<?=$aNotification->getUrl()?>"><i class="foundicon-people"></i><?=$aNotification->getMessage()?></a></li>
                <?php endforeach;?>
            <?php endif; ?>
        </ul>
        
    </body>
</html>