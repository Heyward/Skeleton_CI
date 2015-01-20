<?php 
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

    $opLanguages = array();
    $opLanguages[""] = lang("default_select");
    foreach ($languages as $aLanguage){
        $opLanguages[$aLanguage] = lang($aLanguage);
    }

    $opProfile = array();
    $opProfile[""] = lang("default_select");
    foreach ($profiles as $aProfile){
        if ($aProfile->getId() != AuthConstants::ID_PROFILE_USER){
            $opProfile[$aProfile->getId()] = $aProfile->getName();
        }
    }
    
    $fields = array();
    $fields[lang('country')] = form_dropdown("idCountry", $opCountry, $idCountry, "class='span10'");
    $fields[lang('city')] = form_dropdown("idCity", $opCity, $idCity, "class='span10'");
    $fields[lang('name')] = form_input(array('name'=>'name', 'class'=>'span10', 'value'=>$name));
    $fields[lang('last_name')] = form_input(array('name'=>'lastName', 'class'=>'span10', 'value'=>$last_name));
    $fields[lang('email')] = form_input(array('name'=>'email', 'class'=>'span10', 'value'=>$email));
    $fields[lang('password')] = form_password(array('name'=>'password', 'class'=>'span10'));
    $fields[lang('identification')] = form_input(array('name'=>'identification', 'class'=>'span10', 'value'=>$identification));
    $fields[lang('language')] = form_dropdown("language", $opLanguages, $language, "class='span10'");
    $fields[lang('profile')] = form_dropdown("idProfile", $opProfile, $idProfile, "class='span4'");
    $fields[""] = "";
    $hidden = array('id' => $id);
    echo print_form_columns('/usersdata/persist/', $fields, $hidden);