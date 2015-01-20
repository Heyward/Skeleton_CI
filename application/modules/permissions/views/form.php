<?php 
    $opMenu = array(AuthConstants::YES=>lang("yes"),AuthConstants::NO=>lang("no"));
    
    $opSection = array();
    $opSection[""] = lang("default_select");
    foreach ($sections as $aSection){
        $opSection[$aSection->getId()] = $aSection->getLabel();
    }
    
    $fields = array();
    $fields[lang('section')] = form_dropdown("idSection", $opSection, $idSection);
    $fields[lang('menu')] = form_dropdown("in_menu", $opMenu, $inMenu);
    $fields[lang('position')] = form_input(array('name'=>'position', 'class'=>'span3 focused', 'value'=>$position));
    $hidden = array('id' => $id);
    echo print_form('/permissions/persist/', $fields, $hidden);