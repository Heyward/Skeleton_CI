<?php 
    $opModel = array();
    foreach ($models as $file => $name){
        $opModel[$file] = $name;
    }

    $fields = array();
    $fields["Model"] = form_dropdown("model", $opModel);
    $fields["Singular"] = form_input(array('name'=>'singular', 'class'=>'span3 focused'));
    $fields["Create permissions?"] = form_checkbox(array("name" => "permissions","value" => "1","class" => "uniform_on"));
    echo print_form('/builder/generateModule/', $fields);