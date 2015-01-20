<?php 
    $opCity = array();
    $opCity[""] = lang("default_select");
    foreach ($cities as $aCity){
        $opCity[$aCity->getId()] = $aCity->getName();
    }

    $opLang = array();
    $opLang[""] = lang("default_select");
    foreach ($languages as $aLanguage){
        $opLang[$aLanguage] = lang($aLanguage);
    }
    
    $fields = array();
    $fields[lang('city')]       = form_dropdown("idCity", $opCity, $idCity, "class='span10'");
    $fields[lang('language')]   = form_dropdown("language", $opLang, $language, "class='span10'");
    $fields[lang('name')]       = form_input(array('name'=>'name', 'class'=>'span10 focused', 'value'=>$name));
    $fields[lang('last_name')]  = form_input(array('name'=>'lastName', 'class'=>'span10 focused', 'value'=>$last_name));
    $fields[lang('email')]      = form_input(array('name'=>'email', 'class'=>'span10 focused', 'value'=>$email));
    $hidden = array('id' => $id);
    echo print_form('/users/persistMyData/', $fields, $hidden, "form", false, 12); 
