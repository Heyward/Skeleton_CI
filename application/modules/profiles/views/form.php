<?php 
    $fields = array();
    $fields[lang('profile')] = form_input(array('name'=>'name', 'class'=>'span4', 'value'=>$name));
    $fields[lang('description')] = form_textarea(array('rows'=>5, 'name'=>'description', 'class'=>'span4', 'value'=>$description));
    $hidden = array('id' => $id);
    echo print_form('/profiles/persist/', $fields, $hidden);