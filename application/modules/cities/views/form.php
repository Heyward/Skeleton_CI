<?php 
    $opCountry = array();
    $opCountry[""] = lang("default_select");
    foreach ($countries as $aCountry){
        $opCountry[$aCountry->getId()] = $aCountry->getName();
    }

    $disable = "";
    if ($this->session->userdata("country") > 0){
        $disable = 'disabled="disabled"';
    }
    
    $fields = array();
    $fields[lang('country')] = form_dropdown("idCountry", $opCountry, $idCountry, $disable." class='span4'");
    $fields[lang('name')] = form_input(array('name'=>'name', 'class'=>'span4', 'value'=>$name));
    $hidden = array('id' => $id);
    
    if ($disable != ""){
        $hidden['idCountry'] = $idCountry;
    }
    
    echo print_form('/cities/persist/', $fields, $hidden);