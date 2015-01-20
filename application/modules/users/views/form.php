<?php 
    $opProfile = array();
    $opProfile[""] = lang("default_select");
    foreach ($profiles as $aProfile){
        if ($aProfile->getId() != AuthConstants::ID_PROFILE_USER){
            $opProfile[$aProfile->getId()] = $aProfile->getName();
        }
    }

    $opCountry = array();
    $opCountry[""] = lang("default_select");
    foreach ($countries as $aCountry){
        $opCountry[$aCountry->getId()] = $aCountry->getName();
    }

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
    $fields[lang('country')] = form_dropdown("idCountry", $opCountry, $idCountry, "class='span4'");
    $fields[lang('city')] = form_dropdown("idCity", $opCity, $idCity, "class='span4'");
    $fields[lang('language')] = form_dropdown("language", $opLang, $language, "class='span4'");
    $fields[lang('profile')] = form_dropdown("idProfile", $opProfile, $idProfile, "class='span4'");
    $fields[lang('name')] = form_input(array('name'=>'name', 'class'=>'span4', 'value'=>$name));
    $fields[lang('last_name')] = form_input(array('name'=>'lastName', 'class'=>'span4', 'value'=>$last_name));
    $fields[lang('email')] = form_input(array('name'=>'email', 'class'=>'span4', 'value'=>$email));
    $fields[lang('password')] = form_password(array('name'=>'password', 'class'=>'span4'));
    $hidden = array('id' => $id);
    echo print_form('/users/persist/', $fields, $hidden);