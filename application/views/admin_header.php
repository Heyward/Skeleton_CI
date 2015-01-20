<div id="header" class="row">
    <div id="logo" class="large-2 columns">
        <a href="#">
            <img src="<?=base_url()?>images/logo.png" alt="Logo" />
        </a>
    </div>
    <div class="large-2 large-offset-8 columns">
        <?php if(isset($notifications)): ?>
            <a data-dropdown="drop-notifications" href="#" class="notification">
                <i class="foundicon-mail"></i>
                <span class="badge"><?=count($notifications)?></span>
            </a>
        <?php endif; ?>
        <a data-dropdown="drop-user-menu"  href="#" id="user-menu">
            <?php if(isset($full_name)): ?>
            <span><?php echo $full_name;?></span>
            <?php endif; ?>
            <i class="arrow icon-angle-down"></i>
        </a>
    </div>
</div>