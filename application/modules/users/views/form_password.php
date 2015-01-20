<?php 
    $fields = array();
    $fields[lang('password')]       = form_password(array('name'=>'password', 'class'=>'span10 focused'));
    $fields[lang('new_password')]   = form_password(array('name'=>'new_password', 'id'=>'new_password', 'class'=>'span10 focused'));
    $fields[lang('re_password')]    = form_password(array('name'=>'re_password', 'class'=>'span10 focused'));
    $hidden = array('id' => $id);
    echo print_form('/users/persistPassword/', $fields, $hidden, "form_password", false, 12); 
    
