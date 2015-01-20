<?php if (isset($box) && $box == true):
?>
<div class="columns large-<?=(isset($span)) ? $span : 12 ?>">
    <div class="box">
        <div class="box-header">
            <span> <?=$title ?></span>
            <div class="box-actions">
                <? foreach ($actions as $label => $url):
                ?>
                <a href="<?=$url ?>" class="button small" id="action_<?=$label ?>"> <? $exp = explode("_", $label); ?>
                <i class="icon-<?=$exp[0] ?>"></i> <?=lang($label) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="box-content">
            <?=$content ?>

            <div class="large-12 columns">
                <div data-alert id="alert" class="alert-box" style="display: none;">
                    <div id="message"></div>
                    <a href="#" class="close">&times;</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
    
<?=$content ?>
<div class="large-12 columns">
    <div data-alert id="alert" class="alert-box" style="display: none;">
        <div id="message"></div>
        <a href="#" class="close">&times;</a>
    </div>
</div>
<?php endif; ?>

