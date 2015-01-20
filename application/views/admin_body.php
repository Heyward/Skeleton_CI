<div id="body" class="row">
    <div id="container-menu" class="large-2 columns">
         <?=$menu?>
    </div>
    <div id="content" class="large-10 columns">
        <div class="row">
            <div class="large-12 columns">
                <span class="title"><?=$title?></span>
                 <? foreach ($actions as $label => $url):?>
                    <a href="<?=$url?>" class="button small actions">
                        <? $exp = explode("_", $label);?>
                        <i class="icon-<?=$exp[0]?>"></i> <?=lang($label)?>
                    </a>
                <?php endforeach;?>
            </div>
        </div>
        <div class="row">
            <div class="large-12 columns">
                <?php if(isset($subtitle)): ?>
                    <span class="sub-title"><?=$subtitle?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <?=$sections?>
        </div>
    </div>
</div>

<!-- DIALOGOS -->
<div id="confirmation_delete" title="<?=lang("confirmation_delete")?>">
    <div id="message_delete"></div>
</div>