<?php 
    $fields = array();
    $fields[lang('name')] = form_input(array('name'=>'name', 'class'=>'span10', 'value'=>$name));
    $fields[lang('code')] = form_input(array('name'=>'code', 'class'=>'span10', 'value'=>$code));
    $hidden = array('id' => $id);
    echo print_form('/countries/persist/', $fields, $hidden);
