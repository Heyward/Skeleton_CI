<?php 
    $fields = array();
    $fields[lang('section')] = form_input(array('name'=>'label', 'class'=>'span4', 'value'=>$label));
    $fields[lang('position')] = form_input(array('name'=>'position', 'class'=>'span4', 'value'=>$position));
    $hidden = array('id' => $id);
    echo print_form('/sections/persist/', $fields, $hidden);