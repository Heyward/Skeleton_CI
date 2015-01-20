<?php 
    $fields = array();
    $fields[lang('date')] = form_input(array('name'=>'date', 'class'=>'span3 focused', 'value'=>$date));
	$fields[lang('time')] = form_input(array('name'=>'time', 'class'=>'span3 focused', 'value'=>$time));
	$fields[lang('logType')] = form_input(array('name'=>'logType', 'class'=>'span3 focused', 'value'=>$logType));
	$fields[lang('data')] = form_input(array('name'=>'data', 'class'=>'span3 focused', 'value'=>$data));
	$fields[lang('oldData')] = form_input(array('name'=>'oldData', 'class'=>'span3 focused', 'value'=>$oldData));
	$fields[lang('origin')] = form_input(array('name'=>'origin', 'class'=>'span3 focused', 'value'=>$origin));
	$fields[lang('user')] = form_input(array('name'=>'user', 'class'=>'span3 focused', 'value'=>$user));
    $hidden = array('id' => $id);
    echo print_form('/logs/persist/', $fields, $hidden);